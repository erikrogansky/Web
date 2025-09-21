//import stylelint from "stylelint";
import { defineConfig } from "vite";
import eslint from "vite-plugin-eslint";
import liveReload from "vite-plugin-live-reload";

import path from "path";
import { fileURLToPath } from "url";
const __dirname = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig({
    base: "/",
    server: {
        host: true,
        proxy: {
            "^/(wp-content|wp-admin|wp-includes)/.*": {
                target: "http://localhost:8080",
                changeOrigin: true,
                secure: false,
            },
        },
        hmr: {
            protocol: "ws",
            host: "host.docker.internal",
            port: 3000,
            path: "/@vite/client",
        },
        port: 3000,
        watch: {
            usePolling: true,
        },
        fs: {
            allow: [path.resolve(__dirname, "src")],
        },
    },
    plugins: [
        eslint({ cache: false }),
        //stylelint({ fix: true, cache: false }),

        liveReload([
            "**/*.php",
            "components/**/*.php",
            "templates/**/*.php",
            "functions.php",
        ]),
    ],
    publicDir: false,
    build: {
        outDir: "public/dist",
        emptyOutDir: true,
        manifest: false,
        rollupOptions: {
            input: {
                app: path.resolve(__dirname, "src/ts/app.ts"),
            },
            output: {
                assetFileNames: "styles.[hash].css",
                entryFileNames: "[name].[hash].js",
            },
        },
    },
    resolve: {
        alias: {
            "@ts": path.resolve(__dirname, "src/ts"),
            "@scss": path.resolve(__dirname, "src/scss"),
        },
    },
});
