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
const PUBLIC_DIR = 'public';

const ALLOWED_EXPORT_TYPES = ['html5', 'html5-sp', 'scorm12'] as const;
type AllowedExportType = (typeof ALLOWED_EXPORT_TYPES)[number];

function safeFixturePath(filename: string): string | null {
    const basename = path.basename(filename);
    const resolved = path.resolve(path.join(FIXTURES_PATH, basename));
    const base = path.resolve(FIXTURES_PATH);
    if (!resolved.startsWith(base + path.sep) && resolved !== base) return null;
    return resolved;
}

async function buildPreview(
    fixturePath: string,
    theme: string,
    exportType: AllowedExportType,
): Promise<Uint8Array> {
    const tempDir = await fs.promises.mkdtemp(path.join(os.tmpdir(), 'exe-style-lab-'));
    try {
        const elpxBuffer = await fs.promises.readFile(fixturePath);

        const ydoc = new Y.Doc();
        const assetHandler = new FileSystemAssetHandler(tempDir);
        const importer = new ElpxImporter(ydoc, assetHandler);
        await importer.importFromBuffer(new Uint8Array(elpxBuffer));

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

    .get('/api/developer/fixtures', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        const fixtures: string[] = [];
        if (fs.existsSync(FIXTURES_PATH)) {
            const entries = fs.readdirSync(FIXTURES_PATH, { withFileTypes: true });
            for (const entry of entries) {
                if (entry.isFile() && (entry.name.endsWith('.elpx') || entry.name.endsWith('.elp'))) {
                    fixtures.push(entry.name);
                }
            }
            fixtures.sort();
        }
        return { fixtures };
    })

    .get('/api/developer/fixtures/:filename', ({ params, set }) => {
        if (!isDev()) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const filePath = safeFixturePath(params.filename);
        if (!filePath) {
            set.status = 403;
            return new Response('Forbidden', { status: 403 });
        }

        if (!fs.existsSync(filePath)) {
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

    .get('/api/developer/style-lab/preview', async ({ query, set }) => {
        if (!isDev()) {
            set.status = 404;
            return new Response('Not Found', { status: 404 });
        }

        const { fixture, theme, exportType } = query as Record<string, string>;

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
            set.status = 403;
            return new Response('Forbidden', { status: 403 });
        }

        if (!fs.existsSync(fixturePath)) {
            set.status = 404;
            return { error: `Fixture not found: ${fixture}` };
        }

        try {
            const zipBuffer = await buildPreview(fixturePath, theme, resolvedExportType);
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

    .post('/api/developer/reload-theme', ({ set }) => {
        if (!isDev()) {
            set.status = 404;
            return { error: 'Not Found' };
        }
        // Theme files are served statically — no server-side cache to flush.
        // The client should hard-reload the iframe after calling this endpoint.
        return { ok: true, message: 'Theme reload requested. Regenerate preview to apply changes.' };
    });
