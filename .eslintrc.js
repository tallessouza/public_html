module.exports = {
	root: true,
	env: {
		'browser': true,
		'es2021': true,
	},
	extends: [
		'eslint:recommended',
		'plugin:tailwindcss/recommended',
	],
	overrides: [
		{
			env: {
				'node': true
			},
			files: [
				'.eslintrc.{js,cjs}'
			],
			parserOptions: {
				sourceType: 'script'
			}
		},
		{
			files: ['*.html', '*.blade.php'],
			parser: '@angular-eslint/template-parser',
		},
	],
	parserOptions: {
		ecmaVersion: 'latest',
		sourceType: 'module'
	},
	globals: {
		'$': true,
		'jQuery': true,
		'toastr': true,
		'tinymce': true,
	},
	rules: {
		indent: [
			'error',
			'tab'
		],
		'linebreak-style': [
			'error',
			'unix'
		],
		quotes: [
			'error',
			'single'
		],
		semi: [
			'error',
			'always'
		]
	}
};
