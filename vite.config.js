
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  server: {
    proxy: {
      // Proxy PHP requests to your PHP server
      '/*.php': {
        target: 'http://localhost:8000',
        changeOrigin: true
      }
    }
  }
});
