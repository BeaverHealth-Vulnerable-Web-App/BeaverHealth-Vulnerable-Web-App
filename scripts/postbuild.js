import { promises as fs } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Make sure manifest.json is in public/build
// Sometimes manifest.json is created in public/build/.vite, which breaks the app
async function ensureManifestLocation() {
  const projectRoot = path.resolve(__dirname, '..');
  const buildDir = path.join(projectRoot, 'public', 'build');
  const viteDir = path.join(buildDir, '.vite');
  const manifestFilename = 'manifest.json';

  // Exit if manifest.json is already in public/build
  await fs.access(path.join(buildDir, manifestFilename)).then(() => {process.exit(0)});

  // If the .vite directory doesn't exit, manifest.json is nowhere to be found, so exit with an error
  await fs.access(viteDir).catch(() => {process.exit(1)})

  // Move all files in public/build/.vite to public/build
  try {
    const files = await fs.readdir(viteDir);

    await Promise.all(
      files.map(async (file) => {
        const src = path.join(viteDir, file);
        const dest = path.join(buildDir, file);
        await fs.rename(src, dest);
      })
    );

    await fs.rmdir(viteDir);
  } catch (err) {
    console.error(`Error moving files from ${viteDir} to ${buildDir}:`, err);
    process.exit(1);
  }
}

ensureManifestLocation();
