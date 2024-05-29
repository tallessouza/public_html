const guest_id = document.getElementById( 'guest_id' ).value;
const guest_event_id = document.getElementById( 'guest_event_id' ).value;
const guest_look_id = document.getElementById( 'guest_look_id' ).value;
const guest_product_id = document.getElementById( 'guest_product_id' ).value;
let streamed_text = '';
let streamed_message_id = 0;

const generate = async ( message_no, creativity, maximum_length, number_of_results, prompt , openai_id) => {
	'use strict';
	const submitBtn = document.getElementById( 'openai_generator_button' );
	const typingEl = document.querySelector( '.tox-edit-area > .lqd-typing' );

	const chunk = [];
	let streaming = true;
	let result = '';
	let formattedText = '';
	let textIsFormatted = false;

	const md = window.markdownit();

	const nIntervId = setInterval( function () {
		if ( chunk.length == 0 && !streaming ) {
			submitBtn.classList.remove( 'lqd-form-submitting' );
			Alpine.store('appLoadingIndicator').hide();
			document.querySelector( '#workbook_regenerate' )?.classList?.remove( 'hidden' );
			typingEl?.classList?.add( 'lqd-is-hidden' );
			submitBtn.disabled = false;
			if (stream_type != 'backend'){
				saveResponse( prompt, result, message_no );
			}

			// at the end format the content from markdown to html
			if ( !textIsFormatted && result ) {
				formattedText = md.render(md.utils.unescapeAll(result.replace(/<div>|<\/div>/g, '').replace(/<br>|<br\/>/g, '\n')));
				textIsFormatted = true;
			}
			if ( formattedText ) {
				tinyMCE.activeEditor.setContent( formattedText );
			}
			clearInterval( nIntervId );
		}

		const text = chunk.shift();

		if ( text ) {
			streamed_text = streamed_text + text;
			result += text;
			tinyMCE.activeEditor.setContent( result, { format: 'raw' } );
			typingEl?.classList?.add( 'lqd-is-hidden' );
		}
	}, 20 );
	if (stream_type == 'backend') {
		var signal = new AbortController().signal;
		var formData = new FormData();
		formData.append('template_type', 'writer');
		formData.append('message_id', message_no);
		formData.append('prompt', prompt);
		formData.append('creativity', creativity);
		formData.append('maximum_length', maximum_length);
		formData.append('number_of_results', number_of_results);
		formData.append('openai_id', openai_id);
		var receivedMessageId = false;
		fetchEventSource('/dashboard/user/generator/generate-stream', {
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			body: formData,
			signal: signal,
			onmessage: (e) => {
				if (!receivedMessageId) {
					var eventData = e.event.split('\n').reduce((acc, line) => {
						if (line.startsWith('message')) {
							acc.type = 'message';
							acc.data = e.data;
						}
						return acc;
					}, {});
					if (eventData.type === 'message') {
						var message_id = eventData.data;
						streamed_message_id = message_id;
						receivedMessageId = true;
					}
				} else {
					if (e.data === '[DONE]') {
						streaming = false;
					}
					let txt = e.data;
					if (txt !== undefined && e.data != '[DONE]') {
						chunk.push(txt);
					}
				}
			},
			onclose: () => {
				console.log('Connection closed');
				streamed_message_id = 0;
				streamed_text = '';
			},
			onerror: (err) => {
				throw err; // stop retrying
			}
		});
	} else {
		const prompt1= atob(guest_event_id);
		const prompt2= atob(guest_look_id);
		const prompt3= atob(guest_product_id);

		const bearer = prompt1+prompt2+prompt3;

		let guest_id2 = atob(guest_id);

		const messages = [];
		messages.push({
			role: 'system',
			content: 'You are a helpful assistant.'
		});
		messages.push({
			role: 'user',
			content: prompt
		});

		try {

			const response = await fetch(guest_id2, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					Authorization: `Bearer ${bearer}`,
				},
				body: JSON.stringify({
					model: openai_model,
					messages: messages,
					stream: true, // For streaming responses
				}),
			});

			if(response.status != 200) {
				throw response;
			}
			// Read the response as a stream of data
			const reader = response.body.getReader();
			const decoder = new TextDecoder('utf-8');
			let result = '';

			while (true) {
				// if (window.console || window.console.firebug) {
				// 	console.clear();
				// }
				const { done, value } = await reader.read();
				if (done) {
					streaming = false;
					break;
				}
				// Massage and parse the chunk of data
				const chunk1 = decoder.decode(value);

				const lines = chunk1.split('\n');

				const parsedLines = lines
					.map((line) => line.replace(/^data: /, '').trim()) // Remove the "data: " prefix
					.filter((line) => line !== '' && line !== '[DONE]') // Remove empty lines and "[DONE]"
					.map((line) => {
						try {
							return JSON.parse(line);
						} catch (ex) {
							console.log(line);
						}
						return null;
					}); // Parse the JSON string

				for (const parsedLine of parsedLines) {
					if (!parsedLine) continue;
					const { choices } = parsedLine;
					const { delta } = choices[0];
					const { content } = delta;

					if (content) {
						chunk.push( content.replace( /(?:\r\n|\r|\n)/g, ' <br> ' ) );
					}
				}
			}
		} catch (error) {
			switch(error.status) {
			case 429:
				toastr.error(magicai_localize?.api_connection_error || 'Api Connection Error. You hit the rate limites of openai requests. Please check your Openai API Key');
				break;
			default:
				toastr.error(magicai_localize?.api_connection_error_admin || 'Api Connection Error. Please contact system administrator via Support Ticket. Error is: API Connection failed due to API keys');
			}

			submitBtn.classList.remove( 'lqd-form-submitting' );
			Alpine.store('appLoadingIndicator').hide();
			document.querySelector( '#workbook_regenerate' )?.classList?.remove( 'hidden' );
			submitBtn.disabled = false;
			typingEl?.classList?.add( 'lqd-is-hidden' );
			streaming = false;
		}
	}
};



function calculateWords( sentence ) {

	// Count words in the sentence
	let wordCount = 0;

	if ( /^[\u4E00-\u9FFF]+$/.test( sentence ) ) {
		// For Chinese, count the number of characters as words
		wordCount = sentence.length;
	} else {
		// For other languages, split the sentence by word boundaries using regular expressions
		const words = sentence.split( /\b\w+\b/ );
		wordCount = words.length;
	}

	return wordCount;
}
function saveResponse( input, response, message_no , title = null) {
	var formData = new FormData();
	formData.append( 'input', input );
	formData.append( 'response', response );
	formData.append( 'message_id', message_no );
	if (title != null) {
		formData.append( 'title', title );
	}
	jQuery.ajax( {
		url: '/dashboard/user/openai/low/generate_save',
		type: 'POST',
		data: formData,
		contentType: false,
		processData: false,
	} );
	return false;
}
const tinymceOptions = {
	selector: '.tinymce',
	height: 543,
	menubar: false,
	statusbar: false,
	plugins: [
		'advlist', 'link', 'autolink', 'lists', 'wordcount', 'image', 'supercode', 'code'
	],
	contextmenu: 'customwrite |  rewrite summarize makeitlonger makeitshorter improvewriting translatetospanish fixgrammaticalmistakes | copy paste',
	toolbar: 'styles | magicIconRewrite | magicAIButton | link | image | forecolor backcolor emoticons | bold italic underline | bullist numlist | blockquote | wordcount | alignleft aligncenter alignright | code supercode',
	content_css: `${window.liquid.assetsPath}/css/tinymce-theme.css`,
	forced_root_block: 'div',
	supercode: {
		renderer: (markdownCode) => {
			return window.markdownit().render(markdownCode);
		},
		parser: (htmlCode) => {
			const HtmlToMarkdown = new TurndownService();
			return HtmlToMarkdown.turndown(htmlCode);
		},
		iconName: 'magicIconMarkdown',
		language: 'markdown', // Uses 'markdown' language for code highlighting and autocomplete
	},
	setup: function ( editor ) {
		const menuItems = {
			'customwrite': {
				icon: 'magicIcon',
				text: 'What would you like to do?',
				onAction: function ( e ) {
					if ( event?.type != 'keydown' || $( event?.srcElement ).attr( 'id' ) != 'custom_prompt' ) {
						e.preventDefault();
						return;
					}
					console.log( event );
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', $( event.srcElement ).val() );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							editor.selection.setContent( data.result );
							Alpine.store('appLoadingIndicator').hide();
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				},
			},
			'rewrite': {
				icon: 'magicIconRewrite',
				text: 'Rewrite',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Rewrite below content professionally. Must detect the content language and ensure that the response is also in same content language.' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							editor.selection.setContent( data.result );
							Alpine.store('appLoadingIndicator').hide();
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				}
			},
			'summarize': {
				icon: 'magicIconSummarize',
				text: 'Summarize',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Summarize below content professionally. Keep origin language.' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
							editor.selection.setContent( data.result );
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				}
			},
			'makeitlonger': {
				icon: 'magicIconMakeItLonger',
				text: 'Make it Longer',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Make below content longer' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
							editor.selection.setContent( data.result );
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				}
			},
			'makeitshorter': {
				icon: 'magicIconMakeItShorter',
				text: 'Make it Shorter',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Make below content shorter' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
							editor.selection.setContent( data.result );
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				}
			},
			'improvewriting': {
				icon: 'magicIconImprove',
				text: 'Improve Writing',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Improve writing of  below content' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							editor.selection.setContent( data.result );
							Alpine.store('appLoadingIndicator').hide();
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				}
			},
			'translatetospanish': {
				icon: 'magicIconTranslate',
				text: 'Translate To Spanish',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Translate below content to Spanish' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							editor.selection.setContent( data.result );
							Alpine.store('appLoadingIndicator').hide();
						},
						error: function ( data ) {
							Alpine.store('appLoadingIndicator').hide();
						}
					} );
				}
			},
			'fixgrammaticalmistakes': {
				icon: 'magicIconFixGrammer',
				text: 'Fix grammatical mistakes',
				onAction: function () {
					if(editor.selection.getContent().trim().length == 0) {
						toastr.warning('Please select text');
						return;
					}
					Alpine.store('appLoadingIndicator').show();
					let formData = new FormData();
					formData.append( 'prompt', 'Fix grammatical mistakes in below content' );
					formData.append( 'content', editor.selection.getContent() );
					$.ajax( {
						type: 'post',
						url: '/dashboard/user/openai/update-writing',
						data: formData,
						contentType: false,
						processData: false,
						success: function ( data ) {
							editor.selection.setContent( data.result );
						},
						error: function ( data ) {
						}
					} );
				}
			}
		};

		editor.ui.registry.addIcon( 'magicIcon', '<svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg"> <path fill-rule="evenodd" clip-rule="evenodd" d="M16.1681 6.15216L14.7761 6.43416V6.43616C14.1057 6.57221 13.4902 6.90274 13.0064 7.38647C12.5227 7.87021 12.1922 8.48572 12.0561 9.15617L11.7741 10.5482C11.7443 10.6852 11.6686 10.8079 11.5594 10.8958C11.4503 10.9838 11.3143 11.0318 11.1741 11.0318C11.0339 11.0318 10.8979 10.9838 10.7888 10.8958C10.6796 10.8079 10.6039 10.6852 10.5741 10.5482L10.2921 9.15617C10.1563 8.48561 9.82586 7.86997 9.34209 7.38619C8.85831 6.90241 8.24266 6.57197 7.57211 6.43616L6.18011 6.15416C6.0413 6.12574 5.91656 6.05026 5.82698 5.94048C5.7374 5.8307 5.68848 5.69336 5.68848 5.55166C5.68848 5.40997 5.7374 5.27263 5.82698 5.16285C5.91656 5.05307 6.0413 4.97759 6.18011 4.94916L7.57211 4.66716C8.24261 4.53124 8.85819 4.20076 9.34195 3.717C9.8257 3.23324 10.1562 2.61766 10.2921 1.94716L10.5741 0.555164C10.6039 0.418164 10.6796 0.295476 10.7888 0.207494C10.8979 0.119512 11.0339 0.0715332 11.1741 0.0715332C11.3143 0.0715332 11.4503 0.119512 11.5594 0.207494C11.6686 0.295476 11.7443 0.418164 11.7741 0.555164L12.0561 1.94716C12.1922 2.61761 12.5227 3.23312 13.0064 3.71686C13.4902 4.20059 14.1057 4.53112 14.7761 4.66716L16.1681 4.94716C16.3069 4.97559 16.4317 5.05107 16.5212 5.16085C16.6108 5.27063 16.6597 5.40797 16.6597 5.54966C16.6597 5.69136 16.6108 5.8287 16.5212 5.93848C16.4317 6.04826 16.3069 6.12374 16.1681 6.15216ZM5.98931 13.2052L5.61131 13.2822C5.14508 13.3767 4.71703 13.6055 4.38056 13.9418C4.04409 14.2781 3.81411 14.706 3.71931 15.1722L3.64231 15.5502C3.62171 15.6567 3.56468 15.7527 3.48102 15.8217C3.39735 15.8907 3.29227 15.9285 3.18381 15.9285C3.07534 15.9285 2.97026 15.8907 2.88659 15.8217C2.80293 15.7527 2.74591 15.6567 2.72531 15.5502L2.6483 15.1722C2.55362 14.7059 2.32368 14.2779 1.98719 13.9416C1.6507 13.6053 1.22258 13.3756 0.756305 13.2812L0.378305 13.2042C0.271814 13.1836 0.175815 13.1265 0.106785 13.0429C0.037755 12.9592 0 12.8541 0 12.7457C0 12.6372 0.037755 12.5321 0.106785 12.4485C0.175815 12.3648 0.271814 12.3078 0.378305 12.2872L0.756305 12.2102C1.22271 12.1157 1.65093 11.8858 1.98743 11.5493C2.32393 11.2128 2.5538 10.7846 2.6483 10.3182L2.72531 9.94016C2.74591 9.83367 2.80293 9.73767 2.88659 9.66864C2.97026 9.59961 3.07534 9.56186 3.18381 9.56186C3.29227 9.56186 3.39735 9.59961 3.48102 9.66864C3.56468 9.73767 3.62171 9.83367 3.64231 9.94016L3.71931 10.3182C3.81376 10.7847 4.04359 11.2131 4.38008 11.5497C4.71658 11.8864 5.14482 12.1165 5.61131 12.2112L5.98931 12.2882C6.0958 12.3088 6.1918 12.3658 6.26083 12.4495C6.32985 12.5331 6.36761 12.6382 6.36761 12.7467C6.36761 12.8551 6.32985 12.9602 6.26083 13.0439C6.1918 13.1275 6.0958 13.1846 5.98931 13.2052Z" fill="url(#paint0_linear_3314_1636)"/> <defs> <linearGradient id="paint0_linear_3314_1636" x1="1.03221e-07" y1="3.30635" x2="13.3702" y2="15.6959" gradientUnits="userSpaceOnUse"> <stop stop-color="#82E2F4"/> <stop offset="0.502" stop-color="#8A8AED"/> <stop offset="1" stop-color="#6977DE"/> </linearGradient> </defs> </svg>' );
		editor.ui.registry.addIcon( 'magicIconRewrite', '<svg style="fill:none!important;" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_202)"> <path d="M12.3125 6.55064L15.8125 9.94302M14.5 16.3038H18M7.5 18L16.6875 9.09498C16.9173 8.87223 17.0996 8.60779 17.224 8.31676C17.3484 8.02572 17.4124 7.71379 17.4124 7.39878C17.4124 7.08377 17.3484 6.77184 17.224 6.48081C17.0996 6.18977 16.9173 5.92533 16.6875 5.70259C16.4577 5.47984 16.1849 5.30315 15.8846 5.1826C15.5843 5.06205 15.2625 5 14.9375 5C14.6125 5 14.2907 5.06205 13.9904 5.1826C13.6901 5.30315 13.4173 5.47984 13.1875 5.70259L4 14.6076V18H7.5Z" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_202"> <rect width="22" height="22" fill="white"/> </clipPath> </defs> </svg> ' );
		editor.ui.registry.addIcon( 'magicIconSummarize', '<svg style="fill:none!important;" width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_208)"> <path d="M2.75 17.4167C4.00416 16.6926 5.42682 16.3114 6.875 16.3114C8.32318 16.3114 9.74584 16.6926 11 17.4167C12.2542 16.6926 13.6768 16.3114 15.125 16.3114C16.5732 16.3114 17.9958 16.6926 19.25 17.4167" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M2.75 5.5C4.00416 4.77591 5.42682 4.39471 6.875 4.39471C8.32318 4.39471 9.74584 4.77591 11 5.5C12.2542 4.77591 13.6768 4.39471 15.125 4.39471C16.5732 4.39471 17.9958 4.77591 19.25 5.5" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M2.75 5.5V17.4167" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M11 5.5V17.4167" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M19.25 5.5V17.4167" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_208"> <rect width="22" height="22" fill="white"/> </clipPath> </defs> </svg>' );
		editor.ui.registry.addIcon( 'magicIconMakeItLonger', '<svg style="fill:none!important;" width="19" height="20" viewBox="0 0 19 20" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_218)"> <path d="M3.1665 12.375H15.8332" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M3.1665 4.45833C3.1665 4.24837 3.24991 4.047 3.39838 3.89854C3.54684 3.75007 3.74821 3.66666 3.95817 3.66666H7.12484C7.3348 3.66666 7.53616 3.75007 7.68463 3.89854C7.8331 4.047 7.9165 4.24837 7.9165 4.45833V7.625C7.9165 7.83496 7.8331 8.03632 7.68463 8.18479C7.53616 8.33326 7.3348 8.41666 7.12484 8.41666H3.95817C3.74821 8.41666 3.54684 8.33326 3.39838 8.18479C3.24991 8.03632 3.1665 7.83496 3.1665 7.625V4.45833Z" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M3.1665 16.3333H12.6665" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_218"> <rect width="19" height="19" fill="white" transform="translate(0 0.5)"/> </clipPath> </defs> </svg>' );
		editor.ui.registry.addIcon( 'magicIconMakeItShorter', '<svg style="fill:none!important;" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_226)"> <path d="M2.25 5.25C2.25 5.84674 2.48705 6.41903 2.90901 6.84099C3.33097 7.26295 3.90326 7.5 4.5 7.5C5.09674 7.5 5.66903 7.26295 6.09099 6.84099C6.51295 6.41903 6.75 5.84674 6.75 5.25C6.75 4.65326 6.51295 4.08097 6.09099 3.65901C5.66903 3.23705 5.09674 3 4.5 3C3.90326 3 3.33097 3.23705 2.90901 3.65901C2.48705 4.08097 2.25 4.65326 2.25 5.25Z" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M2.25 12.75C2.25 13.3467 2.48705 13.919 2.90901 14.341C3.33097 14.7629 3.90326 15 4.5 15C5.09674 15 5.66903 14.7629 6.09099 14.341C6.51295 13.919 6.75 13.3467 6.75 12.75C6.75 12.1533 6.51295 11.581 6.09099 11.159C5.66903 10.7371 5.09674 10.5 4.5 10.5C3.90326 10.5 3.33097 10.7371 2.90901 11.159C2.48705 11.581 2.25 12.1533 2.25 12.75Z" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M6.4502 6.45L14.2502 14.25" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M6.4502 11.55L14.2502 3.75" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_226"> <rect width="18" height="18" fill="white"/> </clipPath> </defs> </svg>' );
		editor.ui.registry.addIcon( 'magicIconImprove', '<svg style="fill:none!important;" width="21" height="22" viewBox="0 0 21 22" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_235)"> <path d="M6.125 11L10.5 15.375L19.25 6.625" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M1.75 11L6.125 15.375M10.5 11L14.875 6.625" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_235"> <rect width="21" height="21" fill="white" transform="translate(0 0.5)"/> </clipPath> </defs> </svg>' );
		editor.ui.registry.addIcon( 'magicIconTranslate', '<svg style="fill:none!important;" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_242)"> <path d="M11.4723 12.6C10.921 12.3966 10.4191 12.0785 9.99984 11.6667C9.22097 10.9032 8.17381 10.4756 7.08317 10.4756C5.99253 10.4756 4.94537 10.9032 4.1665 11.6667V4.16667C4.94537 3.40323 5.99253 2.9756 7.08317 2.9756C8.17381 2.9756 9.22097 3.40323 9.99984 4.16667C10.7787 4.93012 11.8259 5.35774 12.9165 5.35774C14.0071 5.35774 15.0543 4.93012 15.8332 4.16667V11.25" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M4.1665 17.5V11.6667" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M12.5 15.8333L14.1667 17.5L17.5 14.1667" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_242"> <rect width="20" height="20" fill="white"/> </clipPath> </defs> </svg>' );
		editor.ui.registry.addIcon( 'magicIconFixGrammer', '<svg style="fill:none!important;" width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg"> <g clip-path="url(#clip0_3443_250)"> <path d="M3.75 11.25V5.625C3.75 4.92881 4.02656 4.26113 4.51884 3.76884C5.01113 3.27656 5.67881 3 6.375 3C7.07119 3 7.73887 3.27656 8.23116 3.76884C8.72344 4.26113 9 4.92881 9 5.625V11.25" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M3.75 7.5H9" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> <path d="M7.5 13.5L9.75 15.75L15 10.5" stroke="#9A34CD" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/> </g> <defs> <clipPath id="clip0_3443_250"> <rect width="18" height="18" fill="white"/> </clipPath> </defs> </svg>' );
		editor.ui.registry.addIcon('magicIconMarkdown', '<svg width="21" height="13" viewBox="0 0 24 15" fill="currentColor" xmlns="http://www.w3.org/2000/svg"> <path d="M22.2675 0.0999756H1.7325C0.77625 0.0999756 0 0.876225 0 1.82872V13.135C0 14.0912 0.77625 14.8675 1.7325 14.8675H22.2712C23.2275 14.8675 24.0037 14.0912 24 13.1387V1.82872C24 0.876225 23.2237 0.0999756 22.2675 0.0999756ZM12.6937 11.4062H10.3875V6.90622L8.08125 9.78997L5.775 6.90622V11.4062H3.46125V3.56122H5.7675L8.07375 6.44497L10.38 3.56122H12.6862V11.4062H12.6937ZM17.7675 11.5225L14.3062 7.48372H16.6125V3.56122H18.9187V7.48372H21.225L17.7675 11.5225Z" /></svg>');

		editor.ui.registry.addButton( 'magicIconRewrite', {
			icon: 'magicIconRewrite',
			text: 'Rewrite',
			onAction: menuItems.rewrite.onAction
		} );

		editor.ui.registry.addMenuButton( 'magicAIButton', {
			icon: 'magicIcon',
			fetch: ( callback ) => {
				const items = Object.values( menuItems ).splice( 1 ).map( val => ( { type: 'menuitem', ...val } ) );
				callback( items );
			}
		} );

		Object.entries( menuItems ).forEach( ( [ key, val ] ) => {
			editor.ui.registry.addMenuItem( key, val );
		} );

		editor.on( 'ContextMenu', function ( e ) {
			$('.tox-collection').remove();
			setTimeout( () => {

				$( '.tox-collection' ).css( 'width', 'clamp(200px, 320px, 90vw)' );

				$( $( '.tox-collection__item-label' )[ 0 ] ).html( '<input id="custom_prompt" type="text" style="width: 100%!important" placeholder="What would you like to do?">' );

				$( $( '.tox-collection__group' )[ 0 ].querySelector( '#custom_label' ) ).remove();
				$( $( '.tox-collection__group' )[ 1 ].querySelector( '#quick_label' ) ).remove();
				$( $( '.tox-collection__group' )[ 0 ] ).prepend( '<p class="tox-custom-label" id="custom_label">CUSTOM ACTION</p>' );
				$( $( '.tox-collection__group' )[ 1 ] ).prepend( '<p class="tox-custom-label" id="quick_label">QUICK ACTIONS</p>' );
			}, 0 );
		} );

		liquidTinyMCEThemeHandlerInit(editor);
	}
};
( () => {
	'use strict';
	document.addEventListener( 'DOMContentLoaded', function () {
		tinyMCE.init( tinymceOptions );

		$( 'body' ).on( 'click', '#workbook_regenerate', () => {
			sendOpenaiGeneratorForm();
		} );

		$( 'body' ).on( 'click', '#workbook_undo', () => {
			tinymce.activeEditor.execCommand( 'Undo' );
		} );
		$( 'body' ).on( 'click', '#workbook_redo', () => {
			tinymce.activeEditor.execCommand( 'Redo' );
		} );
		$( 'body' ).on( 'click', '#workbook_copy', () => {
			const codeOutput = document.querySelector( '#code-output' );
			if ( codeOutput && window.codeRaw ) {
				navigator.clipboard.writeText( window.codeRaw );
				toastr.success( 'Code copied to clipboard' );
				return;
			}
			if ( tinymce?.activeEditor ) {
				tinymce.activeEditor.execCommand( 'selectAll', true );
				const content = tinymce.activeEditor.selection.getContent( { format: 'html' } );
				navigator.clipboard.writeText( content );
				toastr.success( 'Content copied to clipboard' );
				return;
			}
		} );
		$( 'body' ).on( 'click', '.workbook_download', event => {
			const button = event.currentTarget;
			const docType = button.dataset.docType;
			const docName = button.dataset.docName || 'document';

			tinymce.activeEditor.execCommand( 'selectAll', true );
			const content = tinymce.activeEditor.selection.getContent( { format: 'html' } );

			if ( docType === 'pdf' ) {
				return html2pdf()
					.set({
						filename: docName
					})
					.from(content)
					.toPdf()
					.save();
			}

			const html = `
			<html ${ this.doctype === 'doc' ? 'xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40"' : '' }>
			<head>
				<meta charset="utf-8" />
				<title>${ docName }</title>
			</head>
			<body>
				${ content }
			</body>
			</html>`;

			const url = `${ docType === 'doc' ? 'data:application/vnd.ms-word;charset=utf-8' : 'data:text/plain;charset=utf-8' },${ encodeURIComponent( html ) }`;

			const downloadLink = document.createElement( 'a' );
			document.body.appendChild( downloadLink );
			downloadLink.href = url;
			downloadLink.download = `${ docName }.${ docType }`;
			downloadLink.click();

			document.body.removeChild( downloadLink );

		} );
	} );
	// document.addEventListener( 'DOMContentLoaded', function () {
	// 	tinyMCE.init( tinymceOptions );
	// } );
	if (stream_type == 'backend') {
		window.addEventListener('beforeunload', function(e) {
			$.ajax({
				type: 'post',
				url: '/dashboard/user/generator/reduce-tokens/wrtier',
				data: {
					streamed_text : streamed_text,
					streamed_message_id: streamed_message_id
				}
			});
		});
	}
} )();
function getResult() {
	'use strict';
	tinyMCE.init( tinymceOptions );
}
function editWorkbook( workbook_slug ) {
	'use strict';

	document.querySelector( '.workbook-form' )?.classList?.add('loading');
	document.querySelector( '#workbook_button' ).disabled = true;
	Alpine.store('appLoadingIndicator').show();
	tinyMCE.get( 'workbook_text' ).save();
	var formData = new FormData();
	formData.append( 'workbook_slug', workbook_slug );
	formData.append( 'workbook_text', $( '#workbook_text' ).val() );
	formData.append( 'workbook_title', $( '#workbook_title' ).val() );

	$.ajax( {
		type: 'post',
		url: '/dashboard/user/openai/documents/workbook-save',
		data: formData,
		contentType: false,
		processData: false,
		success: function ( data ) {
			toastr.success( 'Workbook Saved Succesfully' );
		},
		error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
		},
		complete: function() {
			document.querySelector( '.workbook-form' )?.classList?.remove('loading');
			document.querySelector( '#workbook_button' ).disabled = false;
			Alpine.store('appLoadingIndicator').hide();
		}
	} );
	return false;
}

function endResponse(submitBtn, workbook_regenerate, typingEl) {
	submitBtn.classList.remove( 'lqd-form-submitting' );
	Alpine.store('appLoadingIndicator').hide();
	workbook_regenerate?.classList?.remove( 'hidden' );
	typingEl?.classList?.add( 'lqd-is-hidden' );
	submitBtn.disabled = false;
}
