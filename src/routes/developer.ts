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
import { reconstructDocument } from '../websocket/yjs-persistence';
import { db } from '../db/client';
import { findProjectByUuid } from '../db/queries';
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
        .filter(e => e.isFile() && (e.name.endsWith('.elpx') || e.name.endsWith('.elp')))
        .map(e => e.name)
        .sort();
}

async function buildPreview(
    fixturePath: string,
    theme: string,
    exportType: AllowedExportType,
    metaOverrides: StyleLabMetaOverrides = {},
): Promise<Uint8Array> {
    const elpxBuffer = await fs.promises.readFile(fixturePath);
    return buildPreviewFromBuffer(new Uint8Array(elpxBuffer), theme, exportType, metaOverrides);
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

/** Build an installable theme ZIP from the theme directory + CSS overrides */
async function buildThemeZip(
    themeDir: string,
    cssOverrides: Record<string, string>,
): Promise<Uint8Array> {
    const zipFiles: Record<string, Uint8Array> = {};

    async function addDir(dir: string, prefix: string) {
        const entries = await fs.promises.readdir(dir, { withFileTypes: true });
        for (const entry of entries) {
            const fullPath = path.join(dir, entry.name);
            const zipPath = prefix ? `${prefix}/${entry.name}` : entry.name;
            if (entry.isDirectory()) {
                await addDir(fullPath, zipPath);
            } else {
                // Apply CSS override if this file was edited
                if (entry.name.endsWith('.css') && cssOverrides[zipPath] !== undefined) {
                    zipFiles[zipPath] = Buffer.from(cssOverrides[zipPath], 'utf-8');
                } else {
                    zipFiles[zipPath] = await fs.promises.readFile(fullPath);
                }
            }
        }
    }

    await addDir(themeDir, '');
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
    .get('/api/developer/sessions', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        const sessions = getAllSessions()
            .filter(s => s.odeId)
            .map(s => ({
                sessionId: s.sessionId,
                fileName: s.fileName || 'Untitled',
                odeId: s.odeId,
            }));
        return { sessions };
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
        const fixtures = [
            ...listFixtures(FIXTURES_PATH),
            ...listFixtures(DEV_FIXTURES_PATH),
        ];
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

        const resolvedExportType: AllowedExportType = ALLOWED_EXPORT_TYPES.includes(
            exportType as AllowedExportType,
        )
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
                if (!liveSession.odeId) {
                    set.status = 400;
                    return { error: 'Session has no project (no odeId)' };
                }
                const project = await findProjectByUuid(db, liveSession.odeId);
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
                    const wrapper = new ServerYjsDocumentWrapper(ydoc, liveSession.odeId);
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

        const { theme, cssOverrides } = body as { theme: string; cssOverrides?: Record<string, string> };
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
            const zipBuffer = await buildThemeZip(themeDir, cssOverrides || {});
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

    // Reload theme from disk: re-export is the reload — theme CSS is read fresh each time.
    .post('/api/developer/reload-theme', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        return { ok: true, reload: true };
    });
