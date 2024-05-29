function formatTime( time ) {
	var minutes = Math.floor( time / 60 );
	var seconds = Math.floor( time % 60 );
	return minutes + ':' + ( seconds < 10 ? '0' : '' ) + seconds;
}

/**
 *
 * @param {HTMLElement} el vanilla js element. not jquery
 */
function generateWaveForm( el ) {


	var audioUrl = el.getAttribute( 'data-audio' );
	var container = el.querySelector( '.audio-preview' );

	// empty the element first
	while ( container.firstElementChild ) {
		container.firstElementChild.remove();
	}

	var waveform = WaveSurfer.create( {
		container: container,
		waveColor: "#bcbac8",
		progressColor: "#ffffff00",
		cursorWidth: 0,
		barWidth: 1,
		interact: false,
		autoCenter: false,
		hideScrollbar: true,
		height: 22,
	} );

	waveform.load( audioUrl );

	var playButton = document.createElement( 'button' );
	playButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M6 4v16a1 1 0 0 0 1.524 .852l13 -8a1 1 0 0 0 0 -1.704l-13 -8a1 1 0 0 0 -1.524 .852z" stroke-width="0" fill="currentColor"></path> </svg><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M9 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z" stroke-width="0" fill="currentColor"></path> <path d="M17 4h-2a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h2a2 2 0 0 0 2 -2v-12a2 2 0 0 0 -2 -2z" stroke-width="0" fill="currentColor"></path> </svg>';
	playButton.setAttribute('type', 'button');
	container.appendChild( playButton );

	var timeData = document.createElement( 'span' );
	container.appendChild( timeData );

	waveform.on( 'ready', function () {
		var duration = waveform.getDuration();
		timeData.textContent = formatTime( duration );
	} );

	waveform.on( 'audioprocess', function () {
		var currentTime = waveform.getCurrentTime();
		timeData.textContent = formatTime( currentTime );
	} );
	waveform.on( 'play', function () {
		playButton.classList.add( 'is-playing' );
	} );
	waveform.on( 'pause', function () {
		playButton.classList.remove( 'is-playing' );
	} );

	playButton.addEventListener( 'click', function () {
		waveform.playPause();
	} );

}

document.addEventListener( 'DOMContentLoaded', function () {
	var audioElements = document.querySelectorAll( '.data-audio' );
	audioElements.forEach( generateWaveForm );
} );