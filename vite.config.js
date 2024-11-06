import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/admin.css', 'resources/js/post.js', 'resources/js/createPost.js', 'resources/js/filtr.js', 'resources/js/profile.js', 'resources/js/history.js', 'resources/js/highlight.js', 'resources/js/theme.js', 'resources/js/editPost.js', 'resources/js/loadPosts.js', 'resources/js/image.js'],
            refresh: true,
            server: {
                host: 'localhost',
            },
        }),
    ],
});
