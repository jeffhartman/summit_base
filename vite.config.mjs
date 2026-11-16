import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
  base: './',
  plugins: [
    tailwindcss(),
  ],
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
