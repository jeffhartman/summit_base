import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

const themeRoot = path.dirname(fileURLToPath(import.meta.url));
const summitBase = path.resolve(themeRoot, '../../contrib/summit_base');

export default defineConfig({
  base: './',
  plugins: [
    tailwindcss(),
  ],
  resolve: {
    alias: {
      '@summit-base': summitBase,
    },
  },
  server: {
    fs: {
      allow: [themeRoot, summitBase],
    },
  },
  build: {
    outDir: 'css/dist',
    emptyOutDir: true,
    rollupOptions: {
      input: 'css/src/app.css',
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.names?.some((name) => name.endsWith('.css'))) {
            return 'app.css';
          }

          return 'assets/[name][extname]';
        },
      },
    },
  },
});
