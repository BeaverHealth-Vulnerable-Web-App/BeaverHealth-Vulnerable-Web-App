import { promises as fs } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

// Guarentee manifest.json is in public/build
// Sometimes manifest.json is created in public/build/.vite, which breaks the app
async function guarenteeManifestLocation() {
  const projectRoot = path.resolve(__dirname, '..');
  const buildDir = path.join(projectRoot, 'public', 'build');
  const viteDir = path.join(buildDir, '.vite');

  // Check if manifest.json already exists in public/build
  try {
    await fs.access(path.join(buildDir, 'manifest.json'));
    process.exit(0);
  }

  // Check if the .vite directory exists
  try {
    await fs.access(viteDir);
  } catch (err) {
    // manifest.json is not in public/build and there is no .vite directory
    process.exit(1);
  }

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
    console.error('Error moving files from public/build/.vite to public/build:', err);
    process.exit(1);
  }
}

guarenteeManifestLocation();
