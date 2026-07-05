import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_URL || 'http://127.0.0.1:8888';

    return {
        plugins: [
            {
                name: 'redirect-to-laravel',
                configureServer(server) {
                    server.middlewares.use((req, res, next) => {
                        const url = (req.url || '/').split('?')[0];

                        // Let Vite serve HMR, modules, and static assets
                        if (
                            url.startsWith('/@') ||
                            url.startsWith('/node_modules') ||
                            url.startsWith('/resources') ||
                            /\.[a-zA-Z0-9]+$/.test(url)
                        ) {
                            return next();
                        }

                        res.writeHead(302, {
                            Location: `${appUrl}${url === '/' ? '/' : url}`,
                        });
                        res.end();
                    });
                },
            },
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            vue(),
        ],
        resolve: {
            alias: {
                vue: 'vue/dist/vue.esm-bundler.js',
            },
        },
        server: {
            host: '127.0.0.1',
            port: 5173,
            strictPort: true,
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
