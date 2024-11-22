import { promises as fs } from 'fs';
import path from 'path';
import { fileURLToPath } from 'url';

// Get the directory name of the current module
const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

async function moveManifest() {
  // Resolve paths based on the project root
  const projectRoot = path.resolve(__dirname, '..');
  const buildDir = path.join(projectRoot, 'public', 'build');
  const viteDir = path.join(buildDir, '.vite');

  try {
    // Check if manifest.json exists in buildDir
    await fs.access(path.join(buildDir, 'manifest.json'));
    console.log(`Found manifest.json in ${buildDir}`);
    process.exit(0);
  } catch (err) {
    // manifest.json not found in buildDir
    console.log(`Failed to locate manifest.json in ${buildDir}`);
  }

  try {
    // Check if the .vite directory exists
    await fs.access(viteDir);
    console.log(`Vite directory exists: ${viteDir}`);
  } catch (err) {
    console.error(`Failed to locate manifest.json`);
    process.exit(1); // Exit with error
  }

  try {
    // Move files from .vite to buildDir
    const files = await fs.readdir(viteDir);
    console.log(`Moving build output files: ${files.join(', ')}`);

    await Promise.all(
      files.map(async (file) => {
        const src = path.join(viteDir, file);
        const dest = path.join(buildDir, file);
        console.log(`Moving ${src} to ${dest}`);
        await fs.rename(src, dest);
      })
    );

    console.log(`Successfully moved files from ${viteDir} to ${buildDir}`);
    await fs.rmdir(viteDir);
    console.log(`Removed directory: ${viteDir}`);
  } catch (err) {
    console.error('Error moving files:', err);
    process.exit(1);
  }
}

moveManifest();
