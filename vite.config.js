import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

const laravelInputs = [];

// adding theme files
const themes = fs.readdirSync('resources/views', { withFileTypes: true })
	.filter(dirent => dirent.isDirectory() && dirent.name !== 'vendor')
	.map(dirent => dirent.name);

themes.forEach(theme => {
	const themeDashboardScssPath = `resources/views/${theme}/scss/dashboard.scss`;
	const themeLPScssPath = `resources/views/${theme}/scss/landing-page.scss`;
	const themeAppJsPath = `resources/views/${theme}/js/app.js`;

	fs.existsSync(themeDashboardScssPath) && laravelInputs.push( themeDashboardScssPath );
	fs.existsSync(themeLPScssPath) && laravelInputs.push( themeLPScssPath );
	fs.existsSync(themeAppJsPath) && laravelInputs.push( themeAppJsPath );
});

export default defineConfig( {
	plugins: [
		laravel( {
			input: laravelInputs,
			refresh: true,
		} ),
	],
	resolve: {
		alias: {
			'@': '/resources/js',
			'@public': '/public',
			'@themeAssets': '/public/themes',
		}
	}
} );
