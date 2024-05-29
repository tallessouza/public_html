var searchResultsVisible = false;

( () => {
	'use strict';

	const searchInput = document.querySelector( '.header-search-input' );
	const searchShortcutKey = document.querySelector( '#search-shortcut-key' );

	if ( !searchInput ) return;

	const navbarSearch = document.querySelector( '#header-search' );
	let inputFocused = false;
	var timer = null;

	if ( searchShortcutKey ) {
		if ( navigator.userAgent.indexOf( 'Mac OS X' ) != -1 ) {
			searchShortcutKey.innerText = 'cmd';
		} else {
			searchShortcutKey.innerText = 'ctrl';
		}
		searchShortcutKey.parentElement.classList.remove( 'opacity-0' );
	}

	searchInput.addEventListener( 'focus', function () {
		if ( !onlySpaces( searchInput.value ) ) {
			navbarSearch.classList.add( 'done-searching' );
			searchResultsVisible = true;
		}
	} );

	searchInput.addEventListener( 'keyup', function () {
		if ( onlySpaces( searchInput.value ) ) {
			searchResultsVisible = false;
			clearTimeout( timer );
			navbarSearch.classList.remove( 'is-searching' );
			navbarSearch.classList.remove( 'done-searching' );
		} else {
			navbarSearch.classList.add( 'is-searching' );
			clearTimeout( timer );
			timer = setTimeout( searchFunction, 1000 );
		}
	} );

	window.addEventListener( 'keydown', function ( e ) {
		if ( ( e.ctrlKey || e.shiftKey || e.altKey || e.metaKey ) && e.key === 'k' ) {
			e.preventDefault();
			e.stopPropagation();
			if ( inputFocused ) return;
			searchInput.focus();
			inputFocused = true;
			if ( !onlySpaces( searchInput.value ) ) {
				navbarSearch.classList.add( 'done-searching' );
				searchResultsVisible = true;
			}
		}
		if ( e.key === 'Escape' ) {
			if ( !inputFocused ) return;
			searchInput.blur();
			inputFocused = false;
			navbarSearch.classList.remove( 'done-searching' );
			searchResultsVisible = false;
		}
	} );

	searchInput.addEventListener( 'blur', () => {
		inputFocused = false;
	} );

	document.addEventListener( 'click', ev => {
		const { target } = ev;
		const clickedOutside = !navbarSearch?.contains( target );
		if ( clickedOutside ) {
			navbarSearch.classList.remove( 'is-searching' );
			navbarSearch.classList.remove( 'done-searching' );
			searchResultsVisible = false;
		}
	} );

} )();

//sadece boşlukla arama mı yapmış
function onlySpaces( str ) {
	'use strict';

	return str.trim().length === 0 || str === '';
}

function resetSearch() {
	'use strict';

	document.getElementById( 'search_form' ).reset();
	return searchFunction( 'delete' );
}

function searchFunction( n ) {
	'use strict';

	const formData = new FormData();
	const navbarSearch = document.querySelector( '#header-search' );
	const searchInput = document.querySelector( '.header-search-input' );
	formData.append( '_token', document.querySelector( 'input[name=_token]' )?.value );

	if ( n == 'delete' ) {
		formData.append( 'search', n );
	} else {
		formData.append( 'search', searchInput.value );
	}

	$.ajax( {
		type: 'POST',
		url: '/dashboard/api/search',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( result ) {
			//DİV LOAD
			$( '#search_results .search-results-container' ).html( result.html );
			navbarSearch.classList.add( 'done-searching' );
			navbarSearch.classList.remove( 'is-searching' );
			searchResultsVisible = true;
		}
	} );
}

