import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/vendor.scss',
                'resources/assets/sass/app.scss',
                'resources/assets/js/pre-app.js',
                'resources/assets/js/app.js',
                'resources/assets/js/components/main.jsx',
                'resources/assets/realtime/main.js'
            ],
            refresh: true,
        }),
        react(),
    ],
    resolve: {
        alias: {
            '~': path.resolve(__dirname, 'node_modules'),
            '@': path.resolve(__dirname, 'resources/assets'),
            'jquery': path.resolve(__dirname, 'resources/assets/js/jquery-global.js'),
            'backbone': path.resolve(__dirname, 'resources/assets/js/backbone-global.js'),
            'underscore': path.resolve(__dirname, 'resources/assets/js/underscore-global.js')
        },
    },
    build: {
        commonjsOptions: {
            transformMixedEsModules: true,
            requireReturnsDefault: 'preferred',
            ignoreTryCatch: false
        }
    },
    css: {
        preprocessorOptions: {
            scss: {
                api: 'modern-compiler',
                silenceDeprecations: ['legacy-js-api', 'import', 'color-functions', 'global-builtin', 'if-function'],
                includePaths: [
                    path.resolve(__dirname, 'resources/assets/libs'),
                    path.resolve(__dirname, 'resources/assets/libs/bootstrap-sass/assets/stylesheets'),
                    path.resolve(__dirname, 'resources/assets/libs/gentelella/src/scss'),
                    path.resolve(__dirname, 'resources/assets/libs/gentelella/vendors'),
                    path.resolve(__dirname, 'node_modules/bourbon/app/assets/stylesheets')
                ],
                loadPaths: [
                    path.resolve(__dirname, 'resources/assets/libs'),
                    path.resolve(__dirname, 'resources/assets/libs/bootstrap-sass/assets/stylesheets'),
                    path.resolve(__dirname, 'resources/assets/libs/gentelella/src/scss'),
                    path.resolve(__dirname, 'resources/assets/libs/gentelella/vendors'),
                    path.resolve(__dirname, 'node_modules/bourbon/app/assets/stylesheets')
                ]
            }
        }
    },


    server: {
        host: '127.0.0.1',
        port: 5173,
        hmr: {
            host: '127.0.0.1'
        }
    },
    optimizeDeps: {
        exclude: ['jquery'],
        include: ['react', 'react-dom', 'backbone', 'underscore', 'socket.io-client'],
        entries: []
    }
});
