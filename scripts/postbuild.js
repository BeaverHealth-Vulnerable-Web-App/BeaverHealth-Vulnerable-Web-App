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
  const manifestPath = path.join(buildDir, 'manifest.json');

  // Exit if manifest.json already exists in public/build
  if (await fileExists(manifestPath)) {
    process.exit(0);
  }

  // Exit with error if .vite directory is missing
  // This means manifest.json can't be found
  if(!(await fileExists(viteDir))) {
    process.exit(1);
  }

  // Move files from public/build/.vite to public/build
  try {
    const files = await fs.readdir(viteDir);

    await Promise.all(
      files.map(async (file) => {
        const src = path.join(viteDir, file);
        const dst = path.join(buildDir, file);
        await fs.rename(src, dst);
      })
    );

    await fs.rmdir(viteDir);
  } catch {
    process.exit(1);
  }
}

async function fileExists(filePath) {
  try {
    await fs.stat(filePath);
    return true;
  } catch {
    return false;
  }
}

ensureManifestLocation();
