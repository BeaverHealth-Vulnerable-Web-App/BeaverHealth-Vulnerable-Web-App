import fs, { promises as fsPromises } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Ensures manifest.json is in expected location (public/build)
async function ensureManifestLocation()
{
    const projectRoot = path.resolve(__dirname, '..');
    const buildDir = path.join(projectRoot, 'public', 'build');
    const viteDir = path.join(buildDir, '.vite');
    const manifestPath = path.join(buildDir, 'manifest.json');

    if (fs.existsSync(manifestPath)) {
        console.log('manifest.json is already in public/build');
        process.exit(0);
    }

    if (!fs.existsSync(viteDir)) {
        console.error('.vite directory is missing. manifest.json could not be located');
        process.exit(1);
    }

    try {
        const files = await fsPromises.readdir(viteDir);

        await Promise.all(
            files.map(async(file) => {
                const src = path.join(viteDir, file);
                const dst = path.join(buildDir, file);
                await fsPromises.rename(src, dst);
            })
        );

        await fsPromises.rmdir(viteDir);
        console.log('Moved manifest.json from public/build/.vite to public/build');
    } catch (error) {
        console.error('Encountered an error while moving files from public/build/.vite to public/build:', error.message);
        process.exit(1);
    }
}

ensureManifestLocation();
