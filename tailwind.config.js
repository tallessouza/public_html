import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
	content: [
		'./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
		'./app/View/**/*.php',
		'./resources/views/**/*.blade.php',
		'./resources/js/**/*.js',
		'./public/assets/js/**/*.js',
	],
	darkMode: [ 'class', '.theme-dark' ],
	theme: {
		extend: {
			backdropFilter: {
				'none': 'none',
				'blur': 'blur(16px)',
			},
			container: {
				center: true,
				padding: '1rem',
			},
			screens: {
				sm: '576px',
				md: '768px',
				lg: '992px',
				xl: '1200px',
				'2xl': '1520px',
			},
			borderRadius: {
				input: '0.625rem', // 10px
				'4xl': '1.5rem', // 24px
				'5xl': '1.75rem', // 28px
				'6xl': '2rem', // 32px
				'navbar-ss': 'var(--navbar-rounded-ss)',
				'navbar-se': 'var(--navbar-rounded-se)',
				'navbar-ee': 'var(--navbar-rounded-ee)',
				'navbar-es': 'var(--navbar-rounded-es)',
				'dropdown': 'var(--dropdown-rounded)',
			},
			boxShadow: {
				DEFAULT: 'var(--shadow)',
				'xs': '0 2px 1px rgba(0, 0, 0, 0.06)',
				'card': 'var(--card-shadow)',
				'table': 'var(--table-shadow)',
			},
			colors: {
				primary: {
					DEFAULT: 'hsl(var(--primary))',
					foreground: 'hsl(var(--primary-foreground))',
				},
				secondary: {
					DEFAULT: 'hsl(var(--secondary))',
					foreground: 'hsl(var(--secondary-foreground))',
				},
				background: 'hsl(var(--background))',
				foreground: 'hsl(var(--foreground))',
				border: 'hsl(var(--border))',
				heading: {
					background: 'hsl(var(--heading-background))',
					foreground: 'hsl(var(--heading-foreground))',
				},
				input: {
					background: 'hsl(var(--input-background))',
					foreground: 'hsl(var(--input-foreground))',
					border: 'hsl(var(--input-border))',
				},
				card: {
					background: 'hsl(var(--card-background))',
					foreground: 'hsl(var(--card-foreground))',
					border: 'hsl(var(--card-border))',
				},
				table: {
					background: 'hsl(var(--table-background))',
					foreground: 'hsl(var(--table-foreground))',
					border: 'hsl(var(--table-border))',
				},
				folder: {
					background: 'hsl(var(--folder-background))',
					foreground: 'hsl(var(--folder-foreground))',
					border: 'hsl(var(--folder-border))',
				},
				label: 'hsl(var(--label))',
				navbar: {
					background: 'hsl(var(--navbar-background))',
					foreground: 'hsl(var(--navbar-foreground))',
					border: 'hsl(var(--navbar-border))',
					'background-hover': 'hsl(var(--navbar-background-hover))',
					'foreground-hover': 'hsl(var(--navbar-foreground-hover))',
					'background-active': 'hsl(var(--navbar-background-active))',
					'foreground-active': 'hsl(var(--navbar-foreground-active))',
				},
				'navbar-icon': {
					background: 'hsl(var(--navbar-icon-background))',
					foreground: 'hsl(var(--navbar-icon-foreground))',
					'background-hover': 'hsl(var(--navbar-icon-background-hover))',
					'foreground-hover': 'hsl(var(--navbar-icon-foreground-hover))',
					'background-active': 'hsl(var(--navbar-icon-background-active))',
					'foreground-active': 'hsl(var(--navbar-icon-foreground-active))',
				},
				'navbar-divider': {
					DEFAULT: 'hsl(var(--navbar-divider))',
				},
				header: {
					background: 'hsl(var(--header-background))',
					border: 'hsl(var(--header-border))',
				},
				dropdown: {
					background: 'hsl(var(--dropdown-background))',
					foreground: 'hsl(var(--dropdown-foreground))',
					border: 'hsl(var(--dropdown-border))',
				},
				surface: 'hsl(var(--surface))',
				clay: 'hsl(var(--clay))'
			},
			fontSize: {
				'4xs': '0.625rem', // 10px
				'3xs': '0.6875rem', // 11px
				'2xs': '0.8125rem', // 13px
				xs: ['0.875rem', '1.25rem'], // 14px/20px
				sm: ['0.9375rem', '1.4375'], // 15px/23px
				base: [ '1rem', '1.4285em' ],
				lg: [ '1.0625rem', '1.275rem' ], // 17px/20.4px
				'xl': [ '1.25rem', '1.5rem' ], // 20px/24px
				'2xl': [ '1.625rem', '1.75rem' ], // 26px/28px
				'3xl': [ '2.0625rem', '2rem' ], // 33px/32px
				'5xl': ['2.75rem', '2.75rem'], // 44px/44px
				'navbar': 'var(--navbar-fs)',
			},
			fontFamily: {
				body: [ 'var(--font-body)', ...defaultTheme.fontFamily.sans ],
				heading: [ 'var(--font-heading)', ...defaultTheme.fontFamily.sans ],
			},
			keyframes: {
				'pulse-intense': {
					'0%, 100%': { opacity: 1 },
					'50%': { opacity: 0.2 },
				},
				'spin-grow': {
					'from': { transform: 'rotate(0) scale(1)' },
					'to': { transform: 'rotate(360deg) scale(3)' },
				},
				'bounce-load-more': {
					'0%, 100%': { transform: 'translateY(-3px)', 'animation-timing-function': 'cubic-bezier(0.8, 0, 1, 1)' },
					'50%': { transform: 'translateY(3px)', 'animation-timing-function': 'cubic-bezier(0, 0, 0.2, 1)' },
				},
			},
			animation: {
				'pulse-intense': 'pulse-intense 2s ease-in-out infinite',
				'spin-grow': 'spin-grow 3s ease-in-out infinite alternate',
				'bounce-load-more': 'bounce-load-more 1.5s ease-in-out infinite alternate',
			},
			transitionProperty: {
				'border': 'border-color, border-width',
				'bg': 'background-color',
			},
			opacity: {
				15: '0.15'
			},
			backgroundOpacity: {
				15: '0.15'
			},
			borderOpacity: {
				15: '0.15'
			},
			spacing: {
				'navbar-icon': 'var(--navbar-icon-size)',
				'navbar-pt': 'var(--navbar-pt)',
				'navbar-pe': 'var(--navbar-pe)',
				'navbar-pb': 'var(--navbar-pb)',
				'navbar-ps': 'var(--navbar-ps)',
				'navbar-mt': 'var(--navbar-mt)',
				'navbar-me': 'var(--navbar-me)',
				'navbar-mb': 'var(--navbar-mb)',
				'navbar-ms': 'var(--navbar-ms)',
				'navbar-link-pt': 'var(--navbar-link-pt)',
				'navbar-link-pe': 'var(--navbar-link-pe)',
				'navbar-link-pb': 'var(--navbar-link-pb)',
				'navbar-link-ps': 'var(--navbar-link-ps)',
			},
			size: {
				'navbar-icon': 'var(--navbar-icon-size)',
			},
			zIndex: {
				1: 1,
				2: 2,
				3: 3,
				4: 4,
				5: 5,
				6: 6,
				7: 7,
				8: 8,
				9: 9,
			}
		},
	},
	plugins: [
		require('@tailwindcss/typography'),
	]
};
