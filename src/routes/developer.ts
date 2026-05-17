import { Elysia } from 'elysia';
import * as fs from 'fs';
import * as path from 'path';
import * as os from 'os';
import * as Y from 'yjs';
import { renderTemplate } from '../services/template';
import { getBasePath } from '../utils/basepath.util';
import {
    Html5Exporter,
    PageExporter,
    Scorm12Exporter,
    FileSystemResourceProvider,
    FileSystemAssetProvider,
    FflateZipProvider,
    YjsDocumentAdapter,
    ServerYjsDocumentWrapper,
} from '../shared/export';
import { ElpxImporter, FileSystemAssetHandler } from '../shared/import';
import { getAllSessions } from '../services/session-manager';
import { reconstructDocument, storeUpdate } from '../websocket/yjs-persistence';
import { db } from '../db/client';
import { findProjectByUuid } from '../db/queries';
import { createTheme, themeDirNameExists, getNextSiteThemeSortOrder } from '../db/queries/themes';
import { DatabaseAssetProvider } from '../shared/export/providers/DatabaseAssetProvider';
import * as fflate from 'fflate';

const isDev = () => process.env.APP_ENV === 'dev';

const FIXTURES_PATH = 'test/fixtures';
const DEV_FIXTURES_PATH = 'public/dev/fixtures';
const PUBLIC_DIR = 'public';
const THEMES_BASE_PATH = 'public/files/perm/themes/base';

const ALLOWED_EXPORT_TYPES = ['html5', 'html5-sp', 'scorm12'] as const;
type AllowedExportType = (typeof ALLOWED_EXPORT_TYPES)[number];

export interface StyleLabMetaOverrides {
    addSearchBox?: boolean;
    addPagination?: boolean;
    addAccessibilityToolbar?: boolean;
    addExeLink?: boolean;
    exportSource?: boolean;
}

interface StyleLabThemeMetadata {
    name?: string;
    title?: string;
    version?: string;
    compatibility?: string;
    author?: string;
    license?: string;
    licenseUrl?: string;
    description?: string;
    downloadable?: string;
}

const THEME_METADATA_TAGS: Record<keyof StyleLabThemeMetadata, string> = {
    name: 'name',
    title: 'title',
    version: 'version',
    compatibility: 'compatibility',
    author: 'author',
    license: 'license',
    licenseUrl: 'license-url',
    description: 'description',
    downloadable: 'downloadable',
};

function safeFixturePath(filename: string): string | null {
    const basename = path.basename(filename);
    for (const base of [FIXTURES_PATH, DEV_FIXTURES_PATH]) {
        const resolved = path.resolve(path.join(base, basename));
        const resolvedBase = path.resolve(base);
        if (resolved.startsWith(resolvedBase + path.sep) || resolved === resolvedBase) {
            if (fs.existsSync(resolved)) return resolved;
        }
    }
    return null;
}

function listFixtures(dir: string): string[] {
    if (!fs.existsSync(dir)) return [];
    return fs
        .readdirSync(dir, { withFileTypes: true })
        .filter(e => e.isFile() && e.name.endsWith('.elpx'))
        .map(e => e.name)
        .sort();
}

async function buildPreview(
    fixturePath: string,
    theme: string,
    exportType: AllowedExportType,
    metaOverrides: StyleLabMetaOverrides = {},
): Promise<Uint8Array> {
    const elpxBuffer = new Uint8Array(await fs.promises.readFile(fixturePath));
    const tempDir = await fs.promises.mkdtemp(path.join(os.tmpdir(), 'exe-style-lab-'));
    try {
        const ydoc = new Y.Doc();
        const assetHandler = new FileSystemAssetHandler(tempDir);
        const importer = new ElpxImporter(ydoc, assetHandler);
        await importer.importFromBuffer(elpxBuffer);

        if (Object.keys(metaOverrides).length > 0) {
            const metaMap = ydoc.getMap('metadata');
            ydoc.transact(() => {
                for (const [key, value] of Object.entries(metaOverrides)) {
                    if (value !== undefined) metaMap.set(key, value);
                }
            });
        }

        const fakeOdeId = 'fixture-preview';
        const wrapper = new ServerYjsDocumentWrapper(ydoc, fakeOdeId);
        const document = new YjsDocumentAdapter(wrapper);
        const resources = new FileSystemResourceProvider(PUBLIC_DIR);
        const assets = new FileSystemAssetProvider(tempDir);
        const zip = new FflateZipProvider();

        let exporter;
        switch (exportType) {
            case 'html5-sp':
                exporter = new PageExporter(document, resources, assets, zip);
                break;
            case 'scorm12':
                exporter = new Scorm12Exporter(document, resources, assets, zip);
                break;
            default:
                exporter = new Html5Exporter(document, resources, assets, zip);
        }

        const result = await exporter.export({ theme });
        wrapper.destroy();
        if (!result.success || !result.data) throw new Error(result.error || 'Export failed');
        return result.data;
    } finally {
        await fs.promises.rm(tempDir, { recursive: true, force: true });
    }
}

function parseBoolParam(value: string | undefined): boolean | undefined {
    if (value === '1' || value === 'true') return true;
    if (value === '0' || value === 'false') return false;
    return undefined;
}

/** Resolve theme directory: checks base themes then site themes */
function resolveThemeDir(themeDirName: string): string | null {
    const basePath = path.join(THEMES_BASE_PATH, themeDirName);
    if (fs.existsSync(basePath)) return basePath;
    // Site themes stored in FILES_DIR/themes/site/
    const filesDir = process.env.FILES_DIR || './data';
    const sitePath = path.join(filesDir, 'themes', 'site', themeDirName);
    if (fs.existsSync(sitePath)) return sitePath;
    return null;
}

/** Patch a tag value in a theme config.xml string */
function patchConfigXml(xmlContent: string, updates: Record<string, string>): string {
    let result = xmlContent;
    for (const [tag, value] of Object.entries(updates)) {
        const escaped = value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        const regex = new RegExp(`<${tag}>[\\s\\S]*?<\\/${tag}>`);
        if (regex.test(result)) {
            result = result.replace(regex, `<${tag}>${escaped}</${tag}>`);
        } else {
            result = result.replace(/<\/theme>/, `  <${tag}>${escaped}</${tag}>\n</theme>`);
        }
    }
    return result;
}

function normalizeThemeMetadata(metadata: StyleLabThemeMetadata | undefined): Record<string, string> {
    const updates: Record<string, string> = {};
    for (const [key, tag] of Object.entries(THEME_METADATA_TAGS) as [keyof StyleLabThemeMetadata, string][]) {
        const value = metadata?.[key];
        if (value === undefined) continue;
        const text = String(value).trim();
        if (text === '' && key !== 'description') continue;
        updates[tag] = key === 'downloadable' && text !== '0' ? '1' : text;
    }
    return updates;
}

function defaultConfigXml(metadata: Record<string, string>): string {
    const values = {
        name: metadata.name || 'style-lab-theme',
        title: metadata.title || metadata.name || 'Style Lab Theme',
        version: metadata.version || '1.0',
        compatibility: metadata.compatibility || '3.0',
        author: metadata.author || '',
        license: metadata.license || '',
        'license-url': metadata['license-url'] || '',
        description: metadata.description || '',
        downloadable: metadata.downloadable || '1',
    };
    const escaped = Object.fromEntries(
        Object.entries(values).map(([key, value]) => [
            key,
            value.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;'),
        ]),
    );
    return `<?xml version="1.0"?>
<theme>
    <name>${escaped.name}</name>
    <title>${escaped.title}</title>
    <version>${escaped.version}</version>
    <compatibility>${escaped.compatibility}</compatibility>
    <author>${escaped.author}</author>
    <license>${escaped.license}</license>
    <license-url>${escaped['license-url']}</license-url>
    <description>${escaped.description}</description>
    <downloadable>${escaped.downloadable}</downloadable>
</theme>`;
}

/** Build an installable theme ZIP from the theme directory + file overrides (CSS, config.xml…) */
async function buildThemeZip(themeDir: string, fileOverrides: Record<string, string>): Promise<Uint8Array> {
    const zipFiles: Record<string, Uint8Array> = {};

    async function addDir(dir: string, prefix: string) {
        const entries = await fs.promises.readdir(dir, { withFileTypes: true });
        for (const entry of entries) {
            const fullPath = path.join(dir, entry.name);
            const zipPath = prefix ? `${prefix}/${entry.name}` : entry.name;
            if (entry.isDirectory()) {
                await addDir(fullPath, zipPath);
            } else {
                if (fileOverrides[zipPath] !== undefined) {
                    zipFiles[zipPath] = Buffer.from(fileOverrides[zipPath], 'utf-8');
                } else {
                    zipFiles[zipPath] = await fs.promises.readFile(fullPath);
                }
            }
        }
    }

    await addDir(themeDir, '');
    if (!zipFiles['config.xml'] && fileOverrides['config.xml'] !== undefined) {
        zipFiles['config.xml'] = Buffer.from(fileOverrides['config.xml'], 'utf-8');
    }
    return fflate.zipSync(zipFiles);
}

export const developerRoutes = new Elysia({ name: 'developer-routes' })

    .get('/developer/style-lab', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return 'Not Found';
        }
        const html = renderTemplate('workarea/developer/style-lab', {
            basePath: getBasePath(),
            appEnv: process.env.APP_ENV || 'dev',
            locale: 'en',
        });
        set.headers['Content-Type'] = 'text/html; charset=utf-8';
        return html;
    })

    // List available fixtures from test/fixtures/ and public/dev/fixtures/
    .get('/api/developer/fixtures', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        const fixtures = listFixtures(FIXTURES_PATH);
        const devFixtures = listFixtures(DEV_FIXTURES_PATH).filter(f => !fixtures.includes(f));
        return { fixtures: [...fixtures, ...devFixtures] };
    })

    // List active project sessions (projects open in the editor right now)
    // sessionId === project UUID; only sessions with a saved project in DB are shown.
    // Title comes from DB so it's always up-to-date (session.fileName may lag behind).
    .get('/api/developer/sessions', async ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        const allSessions = getAllSessions();
        const seen = new Set<string>();
        const results: { sessionId: string; fileName: string }[] = [];
        for (const s of allSessions) {
            if (seen.has(s.sessionId)) continue;
            seen.add(s.sessionId);
            try {
                const project = await findProjectByUuid(db, s.sessionId);
                if (!project?.title) continue; // skip unsaved / untitled projects
                results.push({ sessionId: s.sessionId, fileName: project.title });
            } catch { /* skip on error */ }
        }
        return { sessions: results };
    })

    // Serve a fixture file by name
    .get('/api/developer/fixtures/:filename', ({ params, set }) => {
        if (!isDev()) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const filePath = safeFixturePath(params.filename);
        if (!filePath) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const data = fs.readFileSync(filePath);
        const basename = path.basename(filePath);
        return new Response(data, {
            headers: {
                'Content-Type': 'application/zip',
                'Content-Disposition': `attachment; filename="${basename}"`,
            },
        });
    })

    // Manifest: fixture list + export presets (for AI/automation tooling)
    .get('/api/developer/manifest', async ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        const fixtures = [...listFixtures(FIXTURES_PATH), ...listFixtures(DEV_FIXTURES_PATH)];
        return {
            fixtures,
            exportTypes: [
                { id: 'html5', label: 'Website' },
                { id: 'html5-sp', label: 'Single page' },
                { id: 'scorm12', label: 'SCORM 1.2' },
            ],
            viewports: [
                { id: 'desktop', label: 'Desktop' },
                { id: 'tablet', label: 'Tablet', maxWidth: 768 },
                { id: 'mobile', label: 'Mobile', maxWidth: 390 },
            ],
            exportOptions: [
                { id: 'addSearchBox', label: 'Search box', default: false },
                { id: 'addPagination', label: 'Page counter', default: false },
                { id: 'addAccessibilityToolbar', label: 'Accessibility toolbar', default: false },
                { id: 'addExeLink', label: 'eXeLearning link', default: true },
                { id: 'exportSource', label: 'Include source (content.xml)', default: true },
            ],
        };
    })

    // Generate preview ZIP from fixture + theme + export options
    .get('/api/developer/style-lab/preview', async ({ query, set }) => {
        if (!isDev()) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const q = query as Record<string, string>;
        const { fixture, session, theme, exportType } = q;

        if ((!fixture && !session) || !theme) {
            set.status = 400;
            return { error: 'Missing required params: (fixture or session) and theme' };
        }

        const resolvedExportType: AllowedExportType = ALLOWED_EXPORT_TYPES.includes(exportType as AllowedExportType)
            ? (exportType as AllowedExportType)
            : 'html5';

        const metaOverrides: StyleLabMetaOverrides = {
            addSearchBox: parseBoolParam(q.addSearchBox),
            addPagination: parseBoolParam(q.addPagination),
            addAccessibilityToolbar: parseBoolParam(q.addAccessibilityToolbar),
            addExeLink: parseBoolParam(q.addExeLink),
            exportSource: parseBoolParam(q.exportSource),
        };
        for (const key of Object.keys(metaOverrides) as (keyof StyleLabMetaOverrides)[]) {
            if (metaOverrides[key] === undefined) delete metaOverrides[key];
        }

        try {
            let zipBuffer: Uint8Array;

            if (session) {
                // Preview from live session (project currently open in editor)
                const liveSession = getAllSessions().find(s => s.sessionId === session);
                if (!liveSession) {
                    set.status = 404;
                    return { error: `Session not found: ${session}` };
                }
                // sessionId === project UUID (odeId is never set on session objects)
                const project = await findProjectByUuid(db, liveSession.sessionId);
                if (!project) {
                    set.status = 404;
                    return { error: 'Project not found in database' };
                }
                const ydoc = await reconstructDocument(project.id);
                if (!ydoc) {
                    set.status = 500;
                    return { error: 'Could not reconstruct project document' };
                }

                // Apply meta overrides
                if (Object.keys(metaOverrides).length > 0) {
                    const metaMap = ydoc.getMap('metadata');
                    ydoc.transact(() => {
                        for (const [key, value] of Object.entries(metaOverrides)) {
                            if (value !== undefined) metaMap.set(key, value);
                        }
                    });
                }

                const tempDir = await fs.promises.mkdtemp(path.join(os.tmpdir(), 'exe-style-lab-'));
                try {
                    const wrapper = new ServerYjsDocumentWrapper(ydoc, liveSession.sessionId);
                    const document = new YjsDocumentAdapter(wrapper);
                    const resources = new FileSystemResourceProvider(PUBLIC_DIR);
                    const dbAssets = new DatabaseAssetProvider(db, project.id, tempDir);
                    const zip = new FflateZipProvider();

                    let exporter;
                    switch (resolvedExportType) {
                        case 'html5-sp':
                            exporter = new PageExporter(document, resources, dbAssets, zip);
                            break;
                        case 'scorm12':
                            exporter = new Scorm12Exporter(document, resources, dbAssets, zip);
                            break;
                        default:
                            exporter = new Html5Exporter(document, resources, dbAssets, zip);
                    }

                    const result = await exporter.export({ theme });
                    if (!result.success || !result.data) {
                        throw new Error(result.error || 'Export failed');
                    }
                    zipBuffer = result.data;
                    wrapper.destroy();
                } finally {
                    await fs.promises.rm(tempDir, { recursive: true, force: true });
                }
            } else {
                // Preview from fixture file
                const fixturePath = safeFixturePath(fixture!);
                if (!fixturePath) {
                    set.status = 404;
                    return { error: `Fixture not found: ${fixture}` };
                }
                zipBuffer = await buildPreview(fixturePath, theme, resolvedExportType, metaOverrides);
            }

            const filename = `style-lab-${resolvedExportType}.zip`;
            return new Response(zipBuffer, {
                headers: {
                    'Content-Type': 'application/zip',
                    'Content-Disposition': `inline; filename="${filename}"`,
                },
            });
        } catch (err) {
            console.error('[style-lab] Preview error:', err);
            set.status = 500;
            return {
                error: 'Preview generation failed',
                message: err instanceof Error ? err.message : String(err),
            };
        }
    })

    // Export modified theme as an installable ZIP
    .post('/api/developer/style-lab/export-theme', async ({ body, set }) => {
        if (!isDev()) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const { theme, cssOverrides, fileOverrides, themeMetadata } = body as {
            theme: string;
            cssOverrides?: Record<string, string>;
            fileOverrides?: Record<string, string>;
            themeMetadata?: StyleLabThemeMetadata;
        };
        if (!theme) {
            set.status = 400;
            return { error: 'Missing required param: theme' };
        }

        const themeDir = resolveThemeDir(theme);
        if (!themeDir) {
            set.status = 404;
            return { error: `Theme not found: ${theme}` };
        }

        try {
            const overrides: Record<string, string> = { ...(cssOverrides || {}), ...(fileOverrides || {}) };
            const metadataUpdates = normalizeThemeMetadata(themeMetadata);
            if (Object.keys(metadataUpdates).length > 0) {
                const configPath = path.join(themeDir, 'config.xml');
                const originalXml = fs.existsSync(configPath)
                    ? await fs.promises.readFile(configPath, 'utf-8')
                    : defaultConfigXml(metadataUpdates);
                overrides['config.xml'] = patchConfigXml(originalXml, metadataUpdates);
            }
            const zipBuffer = await buildThemeZip(themeDir, overrides);
            return new Response(zipBuffer, {
                headers: {
                    'Content-Type': 'application/zip',
                    'Content-Disposition': `attachment; filename="${theme}-custom.zip"`,
                },
            });
        } catch (err) {
            console.error('[style-lab] Theme export error:', err);
            set.status = 500;
            return {
                error: 'Theme export failed',
                message: err instanceof Error ? err.message : String(err),
            };
        }
    })

    // Install a modified theme as a new site theme + optionally apply it to a project
    .post('/api/developer/style-lab/install-theme', async ({ body, set }) => {
        if (!isDev()) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const { theme, fileOverrides, themeMetadata, newDirName, newDisplayName, sessionId } = body as {
            theme: string;
            fileOverrides?: Record<string, string>;
            themeMetadata?: StyleLabThemeMetadata;
            newDirName: string;
            newDisplayName: string;
            sessionId?: string;
        };

        if (!theme || !newDirName || !newDisplayName) {
            set.status = 400;
            return { error: 'Missing required params: theme, newDirName, newDisplayName' };
        }
        if (!/^[a-z0-9_-]+$/.test(newDirName)) {
            set.status = 400;
            return { error: 'newDirName must contain only lowercase letters, numbers, hyphens and underscores' };
        }

        const themeDir = resolveThemeDir(theme);
        if (!themeDir) {
            set.status = 404;
            return { error: `Theme not found: ${theme}` };
        }

        const exists = await themeDirNameExists(db, newDirName);
        if (exists) {
            set.status = 409;
            return { error: `Theme directory "${newDirName}" already exists. Choose a different identifier.` };
        }

        try {
            const overrides: Record<string, string> = { ...(fileOverrides || {}) };
            const metadataUpdates = normalizeThemeMetadata({
                ...themeMetadata,
                name: newDirName,
                title: themeMetadata?.title || newDisplayName,
            });

            // Patch config.xml with new name
            const configPath = path.join(themeDir, 'config.xml');
            if (fs.existsSync(configPath)) {
                const originalXml = await fs.promises.readFile(configPath, 'utf-8');
                overrides['config.xml'] = patchConfigXml(originalXml, metadataUpdates);
            } else {
                overrides['config.xml'] = defaultConfigXml(metadataUpdates);
            }

            const zipBuffer = await buildThemeZip(themeDir, overrides);

            // Extract files to site themes directory
            const filesDir = process.env.FILES_DIR || './data';
            const targetDir = path.join(filesDir, 'themes', 'site', newDirName);
            await fs.promises.mkdir(targetDir, { recursive: true });
            const files = fflate.unzipSync(zipBuffer);
            for (const [filePath, data] of Object.entries(files)) {
                const fullPath = path.join(targetDir, filePath);
                await fs.promises.mkdir(path.dirname(fullPath), { recursive: true });
                await fs.promises.writeFile(fullPath, data);
            }

            // Register in database
            const sortOrder = await getNextSiteThemeSortOrder(db);
            await createTheme(db, {
                dir_name: newDirName,
                display_name: newDisplayName,
                description: themeMetadata?.description || null,
                version: themeMetadata?.version || null,
                author: themeMetadata?.author || null,
                license: themeMetadata?.license || null,
                is_builtin: 0,
                is_enabled: 1,
                is_default: 0,
                sort_order: sortOrder,
                storage_path: `themes/site/${newDirName}`,
            });

            // Optionally apply theme to an open project
            // sessionId === project UUID (odeId is never set on session objects)
            if (sessionId) {
                const liveSession = getAllSessions().find(s => s.sessionId === sessionId);
                if (liveSession) {
                    const project = await findProjectByUuid(db, liveSession.sessionId);
                    if (project) {
                        const ydoc = await reconstructDocument(project.id);
                        if (ydoc) {
                            ydoc.transact(() => {
                                ydoc.getMap('metadata').set('theme', newDirName);
                            });
                            await storeUpdate(project.id, Y.encodeStateAsUpdate(ydoc));
                        }
                    }
                }
            }

            return { ok: true, dirName: newDirName, displayName: newDisplayName };
        } catch (err) {
            console.error('[style-lab] Install theme error:', err);
            set.status = 500;
            return {
                error: 'Theme installation failed',
                message: err instanceof Error ? err.message : String(err),
            };
        }
    })

    // Reload theme from disk: re-export is the reload — theme CSS is read fresh each time.
    .post('/api/developer/reload-theme', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        return { ok: true, reload: true };
    });
