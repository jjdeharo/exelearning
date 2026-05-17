import { Elysia } from 'elysia';
import * as fs from 'fs';
import * as path from 'path';
import * as os from 'os';
import * as Y from 'yjs';
import { renderTemplate } from '../services/template';
import { prefixPath } from '../utils/basepath.util';
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

const isDev = () => process.env.APP_ENV === 'dev';

const FIXTURES_PATH = 'test/fixtures';
const DEV_FIXTURES_PATH = 'public/dev/fixtures';
const PUBLIC_DIR = 'public';

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
    const tempDir = await fs.promises.mkdtemp(path.join(os.tmpdir(), 'exe-style-lab-'));
    try {
        const elpxBuffer = await fs.promises.readFile(fixturePath);

        const ydoc = new Y.Doc();
        const assetHandler = new FileSystemAssetHandler(tempDir);
        const importer = new ElpxImporter(ydoc, assetHandler);
        await importer.importFromBuffer(new Uint8Array(elpxBuffer));

        // Apply meta overrides to Y.Doc before export
        if (Object.keys(metaOverrides).length > 0) {
            const metaMap = ydoc.getMap('metadata');
            ydoc.transact(() => {
                for (const [key, value] of Object.entries(metaOverrides)) {
                    if (value !== undefined) metaMap.set(key, value);
                }
            });
        }

        const wrapper = new ServerYjsDocumentWrapper(ydoc, 'style-lab');
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
        if (!result.success || !result.data) {
            throw new Error(result.error || 'Export failed');
        }
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

export const developerRoutes = new Elysia({ name: 'developer-routes' })

    .get('/developer/style-lab', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return 'Not Found';
        }
        const html = renderTemplate('workarea/developer/style-lab', {
            basePath: prefixPath(''),
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
        const { fixture, theme, exportType } = q;

        if (!fixture || !theme) {
            set.status = 400;
            return { error: 'Missing required params: fixture, theme' };
        }

        const resolvedExportType: AllowedExportType = ALLOWED_EXPORT_TYPES.includes(
            exportType as AllowedExportType,
        )
            ? (exportType as AllowedExportType)
            : 'html5';

        const fixturePath = safeFixturePath(fixture);
        if (!fixturePath) {
            set.status = 404;
            return { error: `Fixture not found: ${fixture}` };
        }

        const metaOverrides: StyleLabMetaOverrides = {
            addSearchBox: parseBoolParam(q.addSearchBox),
            addPagination: parseBoolParam(q.addPagination),
            addAccessibilityToolbar: parseBoolParam(q.addAccessibilityToolbar),
            addExeLink: parseBoolParam(q.addExeLink),
            exportSource: parseBoolParam(q.exportSource),
        };
        // Remove undefined keys
        for (const key of Object.keys(metaOverrides) as (keyof StyleLabMetaOverrides)[]) {
            if (metaOverrides[key] === undefined) delete metaOverrides[key];
        }

        try {
            const zipBuffer = await buildPreview(fixturePath, theme, resolvedExportType, metaOverrides);
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

    // Reload theme from disk: re-export is the reload — theme CSS is read fresh each time.
    // This endpoint signals the client to trigger a new preview.
    .post('/api/developer/reload-theme', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        return { ok: true, reload: true };
    });
