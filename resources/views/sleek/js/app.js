import './bootstrap';
import Alpine from 'alpinejs';
import persist from '@alpinejs/persist';
import focus from '@alpinejs/focus';
import ajax from '@imacrayon/alpine-ajax';
import collapse from '@alpinejs/collapse';
import { fetchEventSource } from '@microsoft/fetch-event-source';

window.fetchEventSource = fetchEventSource;
const darkMode = localStorage.getItem('lqdDarkMode');
const docsViewMode = localStorage.getItem('docsViewMode');
const navbarShrink = localStorage.getItem('lqdNavbarShrinked');

window.Alpine = Alpine;

Alpine.plugin([persist, focus, ajax, collapse]);

// Navbar shrink
Alpine.store('navbarShrink', {
	active: Alpine.$persist(!!navbarShrink).as('lqdNavbarShrinked'),
	toggle( state ) {
		this.active = state ? ( state === 'shrink' ? true : false ) : !this.active;
		document.body.classList.toggle('navbar-shrinked', this.active);
	}
});

// Navbar item
Alpine.data('navbarItem', () => ({
	dropdownOpen: false,
	toggleDropdownOpen( state ) {
		this.dropdownOpen = state ? ( state === 'collapse' ? true : false ) : !this.dropdownOpen;
	},
	item: {
		['x-ref']: 'item',
		['@mouseenter']() {
			if ( !Alpine.store('navbarShrink').active ) return;
			const rect = this.$el.getBoundingClientRect();
			const dropdown = this.$refs.item.querySelector('.lqd-navbar-dropdown');
			['y', 'height', 'bottom'].forEach(prop => this.$refs.item.style.setProperty(`--item-${prop}`, `${rect[prop]}px`));

			if ( dropdown ) {
				const dropdownRect = dropdown.getBoundingClientRect();
				['height'].forEach(prop => this.$refs.item.style.setProperty(`--dropdown-${prop}`, `${dropdownRect[prop]}px`));
			}
		},
		// ['@mouseleave']() {
		// 	if ( !Alpine.store('navbarShrink').active ) return;
		// },
	}
}));

// Mobile nav
Alpine.store('mobileNav', {
	navCollapse: true,
	toggleNav( state ) {
		this.navCollapse = state ? ( state === 'collapse' ? true : false ) : !this.navCollapse;
	},
	templatesCollapse: true,
	toggleTemplates( state ) {
		this.templatesCollapse = state ? ( state === 'collapse' ? true : false ) : !this.templatesCollapse;
	},
	searchCollapse: true,
	toggleSearch( state ) {
		this.searchCollapse = state ? ( state === 'collapse' ? true : false ) : !this.searchCollapse;
	},
});

// light/dark mode
Alpine.store('darkMode', {
	on: Alpine.$persist(!!darkMode).as('lqdDarkMode'),
	toggle() {
		this.on = !this.on;
		document.body.classList.toggle('theme-dark', this.on);
		document.body.classList.toggle('theme-light', !this.on);
	}
});

// App loading indicator
Alpine.store('appLoadingIndicator', {
	showing: false,
	show() {
		this.showing = true;
	},
	hide() {
		this.showing = false;
	},
	toggle() {
		this.showing = !this.showing;
	},
});

// Documents view mode
Alpine.store('docsViewMode', {
	docsViewMode: Alpine.$persist(docsViewMode || 'list').as('docsViewMode'),
	change( mode ) {
		this.docsViewMode = mode;
	}
});

// Generators filter
Alpine.store('generatorsFilter', {
	init() {
		const urlParams = new URLSearchParams(window.location.search);
		this.filter = urlParams.get('filter') || 'all';
	},
	filter: 'all',
	changeFilter( filter ) {
		if ( this.filter === filter ) return;
		if ( !document.startViewTransition ) {
			return this.filter = filter;
		}
		document.startViewTransition(() => this.filter = filter);
	}
});

// Documents filter
Alpine.store('documentsFilter', {
	init() {
		const urlParams = new URLSearchParams(window.location.search);
		this.sort = urlParams.get('sort') || 'created_at';
		this.sortAscDesc = urlParams.get('sortAscDesc') || 'desc';
		this.filter = urlParams.get('filter') || 'all';
		this.page = urlParams.get('page') || '1';
	},
	sort: 'created_at',
	sortAscDesc: 'desc',
	filter: 'all',
	page: '1',
	changeSort( sort ) {
		if ( sort === this.sort ) {
			this.sortAscDesc = this.sortAscDesc === 'desc' ? 'asc' : 'desc';
		} else {
			this.sortAscDesc = 'desc';
		}
		this.sort = sort;
	},
	changeAscDesc( ascDesc ) {
		if ( this.ascDesc === ascDesc ) return;
		this.ascDesc = ascDesc;
	},
	changeFilter( filter ) {
		if ( this.filter === filter ) return;
		this.filter = filter;
	},
	changePage( page ) {
		if ( page === '>' || page === '<' ) {
			page = page === '>' ? Number(this.page) + 1 : Number(this.page) - 1;
		}

		if ( this.page === page ) return;

		this.page = page;
	},
});

// Chats filter
Alpine.store('chatsFilter', {
	init() {
		const urlParams = new URLSearchParams(window.location.search);
		this.filter = urlParams.get('filter') || 'all';
		this.setSearchStr(urlParams.get('search') || '');
	},
	searchStr: '',
	setSearchStr( str ) {
		this.searchStr = str.trim().toLowerCase();
	},
	filter: 'all',
	changeFilter( filter ) {
		if ( this.filter === filter ) return;
		if ( !document.startViewTransition ) {
			return this.filter = filter;
		}
		document.startViewTransition(() => this.filter = filter);
	}
});

// Generator V2
Alpine.data('generatorV2', () => ({
	itemsSearchStr: '',
	setItemsSearchStr(str) {
		this.itemsSearchStr = str.trim().toLowerCase();
		if ( this.itemsSearchStr !== '' ) {
			this.$el.closest('.lqd-generator-sidebar').classList.add('lqd-showing-search-results');
		} else {
			this.$el.closest('.lqd-generator-sidebar').classList.remove('lqd-showing-search-results');
		}
	},
	sideNavCollapsed: false,
	/**
	 *
	 * @param {'collapse' | 'expand'} state
	 */
	toggleSideNavCollapse( state ) {
		this.sideNavCollapsed = state ? (state === 'collapse' ? true : false) : !this.sideNavCollapsed;

		if ( this.sideNavCollapsed ) {
			tinymce?.activeEditor?.focus();
		}
	},
	generatorStep: 0,
	setGeneratorStep( step ) {
		if ( step === this.generatorStep ) return;
		if (!document.startViewTransition) {
			return this.generatorStep = Number( step );
		}
		document.startViewTransition(() => this.generatorStep = Number( step ));
	},
	selectedGenerator: null
}));

// Chat
Alpine.store('mobileChat', {
	sidebarOpen: false,
	toggleSidebar( state ) {
		this.sidebarOpen = state ? (state === 'collapse' ? false : false) : !this.sidebarOpen;
	}
});

// Dropdown
Alpine.data('dropdown', ({triggerType = 'hover'}) => ({
	open: false,
	toggle( state ) {
		this.open = state ? (state === 'collapse' ? false : true) : !this.open;
		this.$refs.parent.classList.toggle('lqd-is-active', this.open);
	},
	parent: {
		['@mouseenter']() {
			if ( triggerType !== 'hover' ) return;
			this.toggle('expand');
		},
		['@mouseleave']() {
			if ( triggerType !== 'hover' ) return;
			this.toggle('collapse');
		},
		['@click.outside']() {
			this.toggle( 'collapse' );
		},
	},
	trigger: {
		['@click.prevent']() {
			if ( triggerType !== 'click' ) return;
			this.toggle();
		},
	},
	dropdown: {

	}
}));

Alpine.start();
