let selectedPrompt = -1;
let promptsData = [];
let favData = [];
let searchString = '';
var pdf = undefined;
var pdfName = '';
var pdfPath = '';
let filterType = 'all';
let imagePath = [];
var prompt_images = [];
let streamed_text = '';
let streamed_message_id = 0;

const chatsMD = window.markdownit({
	breaks: true,
	highlight: function (str, lang) {
		const language = lang && lang !== '' ? lang : 'md';
		try {
			return `<pre class="line-numbers rounded [direction:ltr] max-w-full"><code data-lang="${language}" class="language-${language}">${str.replace(/&/g, '&amp;').replace(/</g, '&lt;')}</code></pre>`;
		} catch (__) {}

		return '';
	}
});

function updateFav(id) {
	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/update-prompt',
		data: {
			id: id,
		},
		success: function (data) {
			favData = data;
			updatePrompts(promptsData);
		},
		error: function () { },
	});
}

function updatePrompts(data) {
	const $prompts = $('#prompts');

	$prompts.empty();

	if (data.length == 0) {
		$('#no_prompt').removeClass('hidden');
	} else {
		$('#no_prompt').addClass('hidden');
	}

	for (let i = 0; i < data.length; i++) {
		let isFav = favData.filter(item => item.item_id == data[i].id).length;

		let title = data[i].title.toLowerCase();
		let prompt = data[i].prompt.toLowerCase();
		let searchStr = searchString.toLowerCase();

		if (data[i].id == selectedPrompt) {
			if (title.includes(searchStr) || prompt.includes(searchStr)) {
				if (
					(filterType == 'fav' && isFav != 0) ||
					filterType != 'fav'
				) {
					let prompt = document
						.querySelector('#selected_prompt')
						.content.cloneNode(true);
					const favbtn = prompt.querySelector('.favbtn');
					prompt.querySelector('.prompt_title').innerHTML =
						data[i].title;
					prompt.querySelector('.prompt_text').innerHTML =
						data[i].prompt;
					favbtn.setAttribute('id', data[i].id);

					if (isFav != 0) {
						favbtn.classList.add('active');
					} else {
						favbtn.classList.remove('active');
					}

					$prompts.append(prompt);
				} else {
					selectedPrompt = -1;
				}
			} else {
				selectedPrompt = -1;
			}
		} else {
			if (title.includes(searchStr) || prompt.includes(searchStr)) {
				if (
					(filterType == 'fav' && isFav != 0) ||
					filterType != 'fav'
				) {
					let prompt = document
						.querySelector('#unselected_prompt')
						.content.cloneNode(true);
					const favbtn = prompt.querySelector('.favbtn');
					prompt.querySelector('.prompt_title').innerHTML =
						data[i].title;
					prompt.querySelector('.prompt_text').innerHTML =
						data[i].prompt;
					favbtn.setAttribute('id', data[i].id);

					if (isFav != 0) {
						favbtn.classList.add('active');
					} else {
						favbtn.classList.remove('active');
					}

					$prompts.append(prompt);
				}
			}
		}
	}
	let favCnt = favData.length;
	let perCnt = data.length;

	if (favCnt == 0) {
		$('#fav_count')[0].innerHTML = '';
	} else {
		$('#fav_count')[0].innerHTML = favCnt;
	}

	if (perCnt == 0 || perCnt == undefined) {
		$('#per_count')[0].innerHTML = '';
	} else {
		$('#per_count')[0].innerHTML = perCnt;
	}
}

function searchStringChange(e) {
	searchString = $('#search_str').val();
	updatePrompts(promptsData);
}

function openNewImageDlg(e) {
	$('#selectImageInput').click();
}

function updatePromptImages() {
	$('#chat_images').empty();
	if (prompt_images.length == 0) {
		$('#chat_images').removeClass('active');
		$('.split_line').addClass('hidden');
		return;
	}
	$('#chat_images').addClass('active');
	$('.split_line').removeClass('hidden');
	for (let i = 0; i < prompt_images.length; i++) {
		let new_image = document
			.querySelector('#prompt_image')
			.content.cloneNode(true);
		$(new_image.querySelector('img')).attr('src', prompt_images[i]);
		$(new_image.querySelector('.prompt_image_close')).attr('index', i);
		$(document.querySelector('#chat_images')).append(new_image);
	}
	let new_image_btn = document
		.querySelector('#prompt_image_add_btn')
		.content.cloneNode(true);
	document.querySelector('#chat_images').append(new_image_btn);
	$('.promt_image_btn').on('click', function (e) {
		e.preventDefault();
		$('#chat_add_image').click();
	});
	$('.prompt_image_close').on('click', function (e) {
		prompt_images.splice($(this).attr('index'), 1);
		updatePromptImages();
	});
}

function addImagetoChat(data) {
	if (prompt_images.filter(item => item == data).length == 0) {
		prompt_images.push(data);
		updatePromptImages();
	}
}

function initChat() {
	var mediaRecorder;
	let audioBlob;
	var chunks = [];
	var stream_;

	prompt_images = [];

	$('#scrollable_content').animate({ scrollTop: 1000 }, 200);
	// Start recording when the button is pressed
	$('#voice_record_button').click(function (e) {
		chunks = [];
		navigator.mediaDevices
			.getUserMedia({ audio: true })
			.then(function (stream) {
				stream_ = stream;
				mediaRecorder = new MediaRecorder(stream);
				$( '#voice_record_button' ).addClass( 'inactive' );
				$( '#voice_record_stop_button' ).addClass( 'active' );
				isRecord = true;
				mediaRecorder.ondataavailable = function (e) {
					chunks.push(e.data);
				};
				mediaRecorder.start();
			})
			.catch(function (err) {
				console.log('The following error occurred: ' + err);
				toastr.warning('Audio is not allowed');
			});

		$('#voice_record_stop_button').click(function (e) {
			e.preventDefault();
			$( '#voice_record_button' ).removeClass( 'inactive' );
			$( '#voice_record_stop_button' ).removeClass( 'active' );
			isRecord = false;
			mediaRecorder.onstop = function (e) {
				var blob = new Blob(chunks, { type: 'audio/mp3' });

				var formData = new FormData();
				var fileOfBlob = new File([blob], 'audio.mp3');
				formData.append('file', fileOfBlob);

				chunks = [];

				$.ajax({
					url: '/dashboard/user/openai/chat/transaudio',
					type: 'POST',
					data: formData,
					contentType: false,
					processData: false,
					success: function (response) {
						if (response.length >= 4) {
							$('#prompt').val(response);
						}
					},
					error: function (error) {
						// Handle the error response
					},
				});
			};
			mediaRecorder.stop();
			stream_
				.getTracks() // get all tracks from the MediaStream
				.forEach(track => track.stop()); // stop each of them
		});
	});
	$('#btn_add_new_prompt').on('click', function (e) {
		prompt_title = $('#new_prompt_title').val();
		prompt = $('#new_prompt').val();

		if (prompt_title.trim() == '') {
			toastr.warning('Please input title');
			return;
		}

		if (prompt.trim() == '') {
			toastr.warning('Please input prompt');
			return;
		}

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/add-prompt',
			data: {
				title: prompt_title,
				prompt: prompt,
			},
			success: function (data) {
				promptsData = data;
				updatePrompts(data);
				$('.custom__popover__back').addClass('hidden');
				$('#custom__popover').removeClass('custom__popover__wrapper');
			},
			error: function () { },
		});
	});

	$('#add_btn').on('click', function (e) {
		$('#custom__popover').addClass('custom__popover__wrapper');
		$('.custom__popover__back').removeClass('hidden');
		e.stopPropagation();
	});

	$('.custom__popover__back').on('click', function () {
		$(this).addClass('hidden');
		$('#custom__popover').removeClass('custom__popover__wrapper');
	});

	$('#prompt_library').on('click', function (e) {
		e.preventDefault();

		$('#prompts').empty();

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/prompts',
			success: function (data) {
				filterType = 'all';
				promptsData = data.promptData;
				favData = data.favData;
				updatePrompts(data.promptData);
				$('#modal').addClass('lqd-is-active');
				$('.modal__back').removeClass('hidden');
			},
			error: function () { },
		});
		e.stopPropagation();
	});

	$('.modal__back').on('click', function () {
		$(this).addClass('hidden');
		$('#modal').removeClass('lqd-is-active');
	});

	$(document).on('click', '.prompt', function () {
		const $promptInput = $('#prompt');
		selectedPrompt = Number($(this.querySelector('.favbtn')).attr('id'));
		$promptInput.val(
			promptsData.filter(item => item.id == selectedPrompt)[0].prompt
		);
		$('.modal__back').addClass('hidden');
		$('#modal').removeClass('lqd-is-active');
		selectedPrompt = -1;
		// updatePrompts(promptsData);
		$promptInput.css('height', '5px');
		$promptInput.css('height', $promptInput[0].scrollHeight + 'px');
	});

	$(document).on('click', '.filter_btn', function () {
		$('.filter_btn').removeClass('active');
		$(this).addClass('active');
		filterType = $(this).attr('filter');
		updatePrompts(promptsData);
	});

	$(document).on('click', '.favbtn', function (e) {
		updateFav(Number($(this).attr('id')));
		e.stopPropagation();
	});

	$('#chat_add_image').click(function () {
		openNewImageDlg();
	});

	$('#selectImageInput').change(function () {
		if (this.files && this.files[0]) {
			for (let i = 0; i < this.files.length; i++) {
				let reader = new FileReader();
				// Existing image handling code
				reader.onload = function (e) {
					var img = new Image();
					img.src = e.target.result;
					img.onload = function () {
						var canvas = document.createElement('canvas');
						var ctx = canvas.getContext('2d');
						canvas.height = (img.height * 200) / img.width;
						canvas.width = 200;
						ctx.drawImage(
							img,
							0,
							0,
							canvas.width,
							canvas.height
						);
						var base64 = canvas.toDataURL('image/png');
						addImagetoChat(base64);
					};
				};
				reader.readAsDataURL(this.files[i]);
			}
		}
		document.getElementById('mainupscale_src') &&
			(document.getElementById('mainupscale_src').style.display =
				'none');
	});

	$('#upscale_src').change(function () {
		if (this.files && this.files[0]) {
			for (let i = 0; i < this.files.length; i++) {
				let reader = new FileReader();

				reader.onload = function (e) {
					addImagetoChat(e.target.result);
					// $( "#selectImageInput" ).val( '' );
				};
				reader.readAsDataURL(this.files[i]);
			}
		}
		document.getElementById('mainupscale_src') &&
			(document.getElementById('mainupscale_src').style.display = 'none');
	});

	document.querySelectorAll('.lqd-chat-ai-bubble .chat-content')?.forEach(el => {
		highlightCode( el );
	});
}

async function saveResponseAsync(
	input,
	response,
	chat_id,
	imagePath,
	pdfName,
	pdfPath,
	outputImage = ''
) {
	var formData = new FormData();
	if (!response) response = '';
	formData.append('chat_id', chat_id);
	formData.append('input', input);
	formData.append('response', response);
	formData.append('images', imagePath);
	formData.append('pdfName', pdfName);
	formData.append('pdfPath', pdfPath);
	formData.append('outputImage', outputImage);
	await jQuery.ajax({
		url: '/dashboard/user/openai/chat/low/chat_save',
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
		},
		data: formData,
		contentType: false,
		processData: false,
	});
	return false;
}

/*

DO NOT FORGET TO ADD THE CHANGES TO BOTH FUNCTION makeDocumentReadyAgain and the document ready function on the top!!!!

 */
function makeDocumentReadyAgain() {
	updateChatButtons();
	$(document).ready(function () {
		'use strict';

		const chat_id = $('#chat_id').val();
		$(`#chat_${chat_id}`).addClass('active').siblings().removeClass('active');

		scrollConversationAreaToBottom();
	});
}

function updateChatButtons() {
	setTimeout(function () {
		const generateBtn = document.getElementById('send_message_button');
		const stopBtn = document.getElementById('stop_button');
		const promptInput = document.getElementById('prompt');
		const realtime = document.getElementById('realtime');
		let controller = null; // Store the AbortController instance
		let scrollLocked = true;
		let nIntervId = null;
		let chunk = [];
		let streaming = true;

		const generate = async ev => {
			'use strict';
			ev?.preventDefault();
			document.getElementById('mainupscale_src') &&
				(document.getElementById('mainupscale_src').style.display =
					'none');
			document.getElementById('sugg') &&
				(document.getElementById('sugg').style.display = 'none');

			// Alert the user if no prompt value
			const promptInputValue = promptInput.value;
			const realtimePrompt = promptInput.value;
			if (
				!promptInputValue ||
				promptInputValue.length === 0 ||
				promptInputValue.replace(/\s/g, '') === ''
			) {
				return toastr.error(magicai_localize?.please_fill_message ||'Please fill the message field');
			}

			const chatsContainer = $('.chats-container');
			const chatsScroller = document.querySelector( '.conversation-area' );

			const userBubbleTemplate = document
				.querySelector('#chat_user_bubble')
				.content.cloneNode(true);
			const aiBubbleTemplate = document
				.querySelector('#chat_ai_bubble')
				.content.cloneNode(true);
			if (category.slug != 'ai_chat_image') {
				$(aiBubbleTemplate.querySelector('.lqd-typing-loader')).remove();
			} else {
				$(aiBubbleTemplate.querySelector('.chat-content-container')).attr('class', 'flex items-center');
				$(aiBubbleTemplate.querySelector('.lqd-typing')).remove();
				$(aiBubbleTemplate.querySelector('button')).remove();
			}

			if (generateBtn.classList.contains('submitting')) return;

			function onChatsAreaScroll() {
				if ( (chatsScroller.scrollTop + chatsScroller.offsetHeight + 10) >= chatsScroller.scrollHeight ) {
					scrollLocked = true;
				} else {
					scrollLocked = false;
				}
			}

			const prompt1 = atob( guest_event_id );
			const prompt2 = atob( guest_look_id );
			const prompt3 = atob( guest_product_id );

			const chat_id = $('#chat_id').val();

			const bearer = prompt1 + prompt2 + prompt3;
			// Disable the generate button and enable the stop button
			generateBtn.disabled = true;
			generateBtn.classList.add( 'hidden' );
			generateBtn.classList.add( 'submitting' );
			stopBtn.classList.add( 'active' );
			stopBtn.disabled = false;
			userBubbleTemplate.querySelector('.chat-content').innerHTML = promptInputValue;
			promptInput.value = '';
			promptInput.style.height = '';
			chatsContainer.append(userBubbleTemplate);
			for (let i = 0; i < prompt_images.length; i++) {
				const chatImageBubbleTemplate = document
					.querySelector('#chat_user_image_bubble')
					.content.cloneNode(true);
				chatImageBubbleTemplate.querySelector('a').href = prompt_images[i];
				chatImageBubbleTemplate.querySelector('.img-content').src = prompt_images[i];
				chatsContainer.append(chatImageBubbleTemplate);
			}
			refreshFsLightbox();
			// Create a new AbortController instance
			controller = new AbortController();
			const signal = controller.signal;

			let responseText = '';

			const aiBubbleWrapper = aiBubbleTemplate.firstElementChild;
			aiBubbleWrapper.classList.add('loading');
			aiBubbleTemplate.querySelector('.chat-content').innerHTML =
				responseText;
			chatsContainer.append(aiBubbleTemplate);

			scrollConversationAreaToBottom();

			if (prompt_images.length == 0) {
				messages.push({
					role: 'user',
					content: promptInputValue,
				});
			} else {
				messages.push({
					role: 'user',
					content: promptInputValue,
				});
				// messages.push({
				// 	role: "user",
				// 	content: [
				// 		{
				// 			type: 'text',
				// 			text: promptInputValue
				// 		},
				// 		...prompt_images.map(item => ({
				// 			type: 'image_url',
				// 			image_url: {
				// 				url: item
				// 			}
				// 		}))
				// 	]
				// });
			}

			let isImage = false;

			if (category.slug == 'ai_chat_image') {
				let image_formData = new FormData();
				image_formData.append('prompt', promptInputValue);
				image_formData.append('chatHistory', JSON.stringify(messages));
				// try {
				let response = await $.ajax({
					url: '/dashboard/user/openai/image/generate',
					type: 'POST',
					data: image_formData,
					processData: false,
					contentType: false,
				});

				const chatImageBubbleTemplate = document
					.querySelector('#chat_bot_image_bubble')
					.content.cloneNode(true);
				chatImageBubbleTemplate.querySelector('a').href = response.path;
				chatImageBubbleTemplate.querySelector('.img-content').src = response.path;
				chatsContainer.append(chatImageBubbleTemplate);
				messages.push({
					role: 'assistant',
					content: '',
				});

				if (messages.length >= 6) {
					messages.splice(1, 2);
				}
				// try {
				saveResponseAsync(
					promptInputValue,
					'',
					chat_id,
					'',
					'',
					'',
					response.path
				);
				// } catch (e) {

				// }

				generateBtn.disabled = false;
				generateBtn.classList.remove('submitting');
				aiBubbleWrapper.classList.remove('loading');
				stopBtn.disabled = true;

				generateBtn.classList.remove( 'hidden' );
				stopBtn.classList.remove( 'active' );

				controller = null; // Reset the AbortController instance

				scrollConversationAreaToBottom();

				window.removeEventListener( 'beforeunload', onBeforePageUnload );
				chatsScroller.removeEventListener( 'scroll', onChatsAreaScroll );
				isImage = true;
				refreshFsLightbox();
				return;
			}

			if ( isImage ) {
				scrollConversationAreaToBottom();
				return;
			}

			let guest_id2 = atob(guest_id);

			let guest_search_i = atob(guest_search);
			let guest_search_k = atob(guest_search_id);

			function onBeforePageUnload(e) {
				e.preventDefault();
				e.returnValue = '';
			}

			// to prevent from reloading when generating respond
			window.addEventListener('beforeunload', onBeforePageUnload);

			chatsScroller.addEventListener( 'scroll', onChatsAreaScroll );

			chunk = [];
			let all = '';
			streaming = true;
			nIntervId = setInterval(function () {
				if (chunk.length == 0 && !streaming) {
					messages.push({
						role: 'assistant',
						content: aiBubbleWrapper.querySelector('.chat-content').innerHTML,
					});
					if (messages.length >= 6) {
						messages.splice(1, 2);
					}
					generateBtn.disabled = false;
					generateBtn.classList.remove('submitting');
					aiBubbleWrapper.classList.remove('loading');
					stopBtn.disabled = true;
					generateBtn.classList.remove( 'hidden' );
					stopBtn.classList.remove( 'active' );
					controller = null; // Reset the AbortController instance
					highlightCode( aiBubbleWrapper.querySelector('.chat-content') );
					scrollConversationAreaToBottom();
					window.removeEventListener( 'beforeunload', onBeforePageUnload );
					chatsScroller.removeEventListener( 'scroll', onChatsAreaScroll );

					clearInterval( nIntervId );
					if (stream_type != 'backend') {
						saveResponseAsync(
							promptInputValue,
							aiBubbleWrapper.querySelector( '.chat-content' ).innerHTML,
							chat_id,
							imagePath,
							pdfName,
							pdfPath,
							''
						);
					}
				}

				let text = chunk.shift();
				if ( text ) {
					text = text
						.replace(/<br\s*\/?>/g, '\n')
						.replace(/&/g, '&amp;')
						.replace(/</g, '&lt;');
					streamed_text = streamed_text + text;
					if(realtime?.checked && guest_search_k != ''){
						all += text;
						aiBubbleWrapper.classList.remove('loading');
						$(aiBubbleWrapper.querySelector('.chat-content')).html(all);
						chatsContainer[0].scrollTo(0, chatsContainer[0].scrollHeight);
					} else {
						aiBubbleWrapper.classList.remove('loading');
						aiBubbleWrapper.querySelector('.chat-content').innerHTML += text;
					}

					scrollLocked && scrollConversationAreaToBottom();
					chatsContainer[0].scrollTo(0,chatsContainer[0].scrollHeight);
				}
			}, 20);

			if (stream_type == 'backend') {
				const isChecked = realtime?.checked ? 1 : 0;
				function implementChat(type, images) {
					var formData = new FormData();
					formData.append('template_type', type);
					formData.append('prompt', promptInputValue);
					formData.append('chat_id', chat_id);
					formData.append('category_id', category.id);
					formData.append('images', images == undefined ? '' : images);
					formData.append('pdfname', pdfName == undefined ? '' : pdfName);
					formData.append('pdfpath', pdfPath == undefined ? '' : pdfPath);
					formData.append('realtime', isChecked ? 1 : 0);
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
									generateBtn.classList.remove( 'hidden' );
									stopBtn.classList.remove( 'active' );
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
				}
				imagePath = [];
				pdfName = '';
				pdfPath = '';
				if (prompt_images.length == 0) {
					implementChat('chatbot');
				} else {
					let temp = [...prompt_images];
					prompt_images = [];
					updatePromptImages();
					$.ajax({
						type: 'POST',
						url: '/images/upload',
						data: {
							images: temp,
						},
						success: function (result) {
							imagePath = result.path;
							implementChat('vision', result.path);
						},
					});
				}
			} else {
				try {
					imagePath = [];
					var temp = [...prompt_images];
					async function implementChat() {
						// Fetch the response from the OpenAI API with the signal from AbortController
						let resmodel =
							temp.length == 0
								? openai_model
								: 'gpt-4-vision-preview';
						let resmessages = [
							...messages.slice(0, messages.length - 1),
							...training,
							messages[messages.length - 1],
						];
						if (resmodel == 'gpt-4-vision-preview') {
							resmessages = [
								{
									role: 'user',
									content: [
										{
											type: 'text',
											text: promptInputValue,
										},
										...temp.map(item => ({
											type: 'image_url',
											image_url: {
												url: item,
											},
										})),
									],
								},
							];
						}

						var formData = new FormData();
						formData.append('chat_id', chat_id);

						let chatbot = $('#chatbot_id').val();

						if(chatbot) {
							formData.append('chatbot_id', $('#chatbot_id').val());
						}

						formData.append('prompt', promptInputValue);

						$.ajax({
							url: '/pdf/getContent',
							type: 'POST',
							data: formData,
							processData: false,
							contentType: false,
							success: async function (response_) {
								if (!response_.extra_prompt == '') {
									resmessages = [
										{
											role: 'user',
											content:
												'\'this pdf\' means pdf content. Must not reference previous chats if user asking about pdf. Must reference pdf content if only user is asking about pdf. Else just response as an assistant shortly and professionaly without must not referencing pdf content. \n\n\n\nUser question: ' +
												messages[ messages.length - 1 ]
													.content +
												'\n\n\n\n\n Document Content: \n ' +
												response_.extra_prompt,
										},
									];
								}
								if (realtime?.checked && guest_search_k != '') {
									const response1 = await fetch(guest_search_i, {
										method: 'POST',
										headers: {
											'Content-Type': 'application/json',
											'X-API-KEY': guest_search_k
										},
										body: JSON.stringify({
											q: realtimePrompt,
										}),
									});
									let jsonContent = await response1.json();
									resmessages = [
										{
											role: 'user',
											content:
												'Prompt: ' + realtimePrompt +
												'\n\nWeb search real time results: ' +
												JSON.stringify(jsonContent) +
												'\n\nInstructions: Based on the Prompt generate a proper response with help of Web search results(if the Web search results in the same context). Only if the prompt require links: (make curated list of links and descriptions using only the <a target="_blank">, write links with using <a target="_blank"> with mrgin Top of <a> tag is 5px and start order as number and write link first and then write description). Must not write links if its not necessary. Must not mention anything about the prompt text.',
										},
									];
								}
								const response = await fetch(guest_id2, {
									method: 'POST',
									headers: {
										'Content-Type': 'application/json',
										Authorization: `Bearer ${bearer}`,
									},
									body: JSON.stringify({
										model: resmodel,
										messages: resmessages,
										max_tokens: 2000,
										stream: true, // For streaming responses
									}),
									signal, // Pass the signal to the fetch request
								});

								if (response.status != 200) {
									throw response;
								}
								// Read the response as a stream of data
								const reader = response.body.getReader();
								const decoder = new TextDecoder('utf-8');
								let result = '';

								while (true) {
									scrollLocked && scrollConversationAreaToBottom();

									const { done, value } = await reader.read();
									if ( done ) {
										generateBtn.classList.remove( 'hidden' );
										stopBtn.classList.remove( 'active' );

										streaming = false;
										break;
									}
									// Massage and parse the chunk of data
									const chunk1 = decoder.decode(value);
									const lines = chunk1.split('\n');

									const parsedLines = lines
										.map(line =>
											line.replace(/^data: /, '').trim()
										) // Remove the "data: " prefix
										.filter(
											line =>
												line !== '' && line !== '[DONE]'
										) // Remove empty lines and "[DONE]"
										.map(line => {
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
										// const { finish_reason } = choices[0];

										if (content) {
											chunk.push(content);
										}
									}
								}
							},
							error: function (xhr, status, error) {
								console.error('Error uploading PDF: ' + error);
							},
						});
					}
					prompt_images = [];
					updatePromptImages();
					$.ajax({
						type: 'POST',
						url: '/images/upload',
						data: {
							images: temp,
						},
						success: function (result) {
							imagePath = result.path;
							implementChat();
						},
					});

				} catch (error) {
					generateBtn.classList.remove( 'hidden' );
					stopBtn.classList.remove( 'active' );

					// Handle fetch request errors
					if (signal.aborted) {
						aiBubbleWrapper.querySelector(
							'.chat-content'
						).innerHTML = 'Request aborted by user. Not saved.';
					} else {
						switch (error.status) {
						case 429:
							aiBubbleWrapper.querySelector(
								'.chat-content'
							).innerHTML =
									'Api Connection Error. You hit the rate limites of openai requests. Please check your Openai API Key.';
							break;
						default:
							aiBubbleWrapper.querySelector(
								'.chat-content'
							).innerHTML =
									'Api Connection Error. Please contact system administrator via Support Ticket. Error is: API Connection failed due to API keys.';
						}
					}
					clearInterval(nIntervId);
					generateBtn.disabled = false;
					generateBtn.classList.remove('submitting');
					aiBubbleWrapper.classList.remove('loading');
					// document.getElementById('chat_form').reset();
					streaming = false;
					messages.pop();
				}
			}
		};

		const stop = () => {
			// Abort the fetch request by calling abort() on the AbortController instance
			generateBtn.classList.remove( 'hidden' );
			stopBtn.classList.remove( 'active' );

			if (controller) {
				controller.abort();
				controller = null;
				chunk = [];
				streaming = false;
				reduceOnStop();
			}
		};
		// if promptInput undefined, then refresh the page
		if (promptInput) {
			promptInput.addEventListener('keypress', ev => {
				if (ev.code == 'Enter' && !ev.shiftKey) {
					ev.preventDefault();
					$('.lqd-chat-record-trigger').show();
					return generate();
				}
			});
		}

		generateBtn?.addEventListener('click', generate);
		stopBtn?.addEventListener('click', stop);
	}, 100);
}

function escapeHtml(html) {
	var text = document.createTextNode(html);
	var div = document.createElement('div');
	div.appendChild(text);
	return div.innerHTML;
}

function openChatAreaContainer(chat_id) {
	'use strict';

	chatid = chat_id;
	$(`#chat_${chat_id}`).addClass('active').siblings().removeClass('active');

	var formData = new FormData();

	formData.append('chat_id', chat_id);

	let openChatAreaContainerUrl = $('#openChatAreaContainerUrl').val();

	$.ajax({
		type: 'post',
		url: openChatAreaContainerUrl,
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			$('#load_chat_area_container').html(data.html);

			initChat();

			messages = [
				{
					role: 'assistant',
					content: prompt_prefix,
				},
			];

			data.lastThreeMessage.forEach(message => {
				messages.push({
					role: 'user',
					content: message.input,
				});
				messages.push({
					role: 'assistant',
					content: message.output,
				});
			});

			makeDocumentReadyAgain();
			if (data.lastThreeMessage != '') {
				document.getElementById('mainupscale_src') &&
					(document.getElementById('mainupscale_src').style.display =
						'none');
				document.getElementById('sugg') &&
					(document.getElementById('sugg').style.display = 'none');
			}
			setTimeout( function () {
				scrollConversationAreaToBottom();
			}, 750 );
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});

	return false;
}

function startNewChat(category_id, local, website_url = null)
{
	var formData = new FormData();
	formData.append('category_id', category_id);

	// let website_url = $("#website_url")?.val();
	let createChatUrl = $('#createChatUrl')?.val();

	if (website_url != null) {
		formData.append('website_url', website_url);
	}

	let link = '/' + local + '/dashboard/user/openai/chat/start-new-chat';

	if (createChatUrl) {
		link = createChatUrl;
	}

	return $.ajax({
		type: 'post',
		url: link,
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			chatid = data.chat.id;
			$('#load_chat_area_container').html(data.html);
			$('#chat_sidebar_container').html(data.html2);

			initChat();
			messages = [
				{
					role: 'assistant',
					content: prompt_prefix,
				},
			];
			makeDocumentReadyAgain();
			setTimeout( function () {
				scrollConversationAreaToBottom();
			}, 750 );
		},
		error: function (data) {
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
}

function startNewDocChat(file, type) {
	'use strict';

	let category_id = $('#chat_search_word').data('category-id');

	var formData = new FormData();
	formData.append('category_id', category_id);
	formData.append('doc', pdf);
	formData.append('type', type);

	Alpine.store('appLoadingIndicator').show();
	$('.lqd-upload-doc-trigger').attr('disabled', true);

	$.ajax({
		type: 'post',
		url: '/dashboard/user/openai/chat/start-new-doc-chat',
		data: formData,
		contentType: false,
		processData: false,
		success: function (data) {
			Alpine.store('appLoadingIndicator').hide();
			$('.lqd-upload-doc-trigger').attr('disabled', false);
			$('#selectDocInput').val('');
			chatid = data.chat.id;
			$('#load_chat_area_container').html(data.html);
			$('#chat_sidebar_container').html(data.html2);

			initChat();
			messages = [
				{
					role: 'assistant',
					content: prompt_prefix,
				},
			];
			makeDocumentReadyAgain();
			setTimeout(function () {
				$('.conversation-area').stop().animate({ scrollTop: $('.conversation-area').outerHeight() }, 200);
			}, 750);

			toastr.success(magicai_localize.analyze_file_finish);
		},
		error: function (data) {
			Alpine.store('appLoadingIndicator').hide();
			$('.lqd-upload-doc-trigger').attr('disabled', false);
			$('#selectDocInput').val('');
			var err = data.responseJSON.errors;
			if (err) {
				$.each(err, function (index, value) {
					toastr.error(value);
				});
			} else {
				toastr.error(data.responseJSON.message);
			}
		},
	});
	return false;
}

function searchChatFunction() {
	'use strict';

	const categoryId = $('#chat_search_word').data('category-id');
	const formData = new FormData();
	formData.append(
		'_token',
		document.querySelector('input[name=_token]')?.value
	);
	formData.append(
		'search_word',
		document.getElementById('chat_search_word').value
	);
	formData.append('category_id', categoryId);

	$.ajax({
		type: 'POST',
		url: '/dashboard/user/openai/chat/search',
		data: formData,
		contentType: false,
		processData: false,
		success: function (result) {
			$('#chat_sidebar_container').html(result.html);
			$(document).trigger('ready');
		},
	} );
}

/**
 *
 * @param {HTMLElement} wrapperEl
 */
function highlightCode( wrapperEl ) {
	if ( !wrapperEl ) return;

	wrapperEl.innerHTML = chatsMD.render(
		chatsMD.utils.unescapeAll(
			wrapperEl.innerHTML
		)
	);

	wrapperEl.innerHTML = wrapperEl.innerHTML
		.replace(/>(\s*\r?\n\s*)</g, '><')
		.replace(/\n(?!.*\n)/, '');

	wrapperEl.querySelectorAll( 'code[class*=language-]' ).forEach( el => {
		Prism.highlightElement( el );
	} );
}

function scrollConversationAreaToBottom() {
	const el = document.querySelector('.conversation-area');
	if ( !el ) return;
	el.scrollTo({
		top: el.scrollHeight + 200,
		left: 0
	});
}

function saveResponse(input, response, chat_id, imagePath = '', pdfName = '', pdfPath = '', outputImage = '') {
	var formData = new FormData();
	formData.append('chat_id', chat_id);
	formData.append('input', input);
	formData.append('response', response);
	formData.append('images', imagePath);
	formData.append('pdfName', pdfName);
	formData.append('pdfPath', pdfPath);
	formData.append('outputImage', outputImage);
	jQuery.ajax({
		url: '/dashboard/user/openai/chat/low/chat_save',
		type: 'POST',
		headers: {
			'X-CSRF-TOKEN': '{{ csrf_token() }}',
		},
		data: formData,
		contentType: false,
		processData: false,
	});
	return false;
}

function addText(text) {
	var promptElement = document.getElementById('prompt');
	var currentText = promptElement.value;
	var newText = currentText + text;
	promptElement.value = newText;
}

function dropHandler(ev, id) {
	// Prevent default behavior (Prevent file from being opened)

	ev.preventDefault();
	const input = document.querySelector(`#${id}`);
	const fileNameEl = input?.previousElementSibling?.querySelector('.file-name');

	if (!input ) return;

	input.files = ev.dataTransfer.files;

	if ( fileNameEl ) {
		fileNameEl.innerText = ev.dataTransfer.files[0].name;
	}

	for (let i = 0; i < ev.dataTransfer.files.length; i++) {

		let reader = new FileReader();
		// Existing image handling code
		reader.onload = function(e) {
			var img = new Image();
			img.src = e.target.result;
			img.onload = function() {
				var canvas = document.createElement('canvas');
				var ctx = canvas.getContext('2d');
				canvas.height = img.height * 200 / img.width;
				canvas.width = 200;
				ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
				var base64 = canvas.toDataURL('image/png');
				addImagetoChat(base64);
			};
		};
		reader.readAsDataURL(input.files[i]);
	}
	document.getElementById('mainupscale_src').style.display = 'none';
}

function dragOverHandler(ev) {
	// Prevent default behavior (Prevent file from being opened)
	ev.preventDefault();
}

function handleFileSelect(id) {
	$('#' + id).prev().find('.file-name').text($('#' + id)[0].files[0].name);
}

function exportAsPdf() {
	var win = window.open(`/dashboard/user/openai/chat/generate-pdf?id=${chatid}`, '_blank');
	win.focus();
}

function exportAsWord() {
	var win = window.open(`/dashboard/user/openai/chat/generate-word?id=${chatid}`, '_blank');
	win.focus();
}

function exportAsTxt() {
	var win = window.open(`/dashboard/user/openai/chat/generate-txt?id=${chatid}`, '_blank');
	win.focus();
}

function reduceOnStop(){
	$.ajax({
		type: 'post',
		url: '/dashboard/user/generator/reduce-tokens/chat',
		data: {
			streamed_text : streamed_text,
			streamed_message_id: streamed_message_id
		},
		success: function (data) {
			streamed_message_id = 0;
			streamed_text = '';
		},
	});
}

$(document).ready(function () {
	'use strict';
	initChat();
	scrollConversationAreaToBottom();
	updateChatButtons();

	function saveChatNewTitle(chatId, newTitle) {
		var formData = new FormData();
		formData.append('chat_id', chatId);
		formData.append('title', newTitle);

		$.ajax({
			type: 'post',
			url: '/dashboard/user/openai/chat/rename-chat',
			data: formData,
			contentType: false,
			processData: false,
		});
		return false;
	}

	function deleteChatItem(chatId, chatTitle) {
		if (confirm(`Are you sure you want to remove ${chatTitle}?`)) {
			var formData = new FormData();
			formData.append('chat_id', chatId);

			const chatTrigger = $(`#${chatId}`);
			const chatIsActive = chatTrigger.hasClass('active');
			let nextChatToActivate = chatTrigger.prevAll(':visible').first();

			if (nextChatToActivate.length === 0) {
				nextChatToActivate = chatTrigger.nextAll(':visible').first();
			}

			$.ajax({
				type: 'post',
				url: '/dashboard/user/openai/chat/delete-chat',
				data: formData,
				contentType: false,
				processData: false,
				success: function (data) {
					//Remove chat li
					chatTrigger.hide();
					chatIsActive && $('#chat_area_to_hide').hide();
					chatIsActive && nextChatToActivate.children('.chat-list-item-trigger').click();
					toastr.success(magicai_localize.conversation_deleted_successfully);
				},
				error: function (data) {
					var err = data.responseJSON.errors;
					if (err) {
						$.each(err, function (index, value) {
							toastr.error(value);
						});
					} else {
						toastr.error(data.responseJSON.message);
					}
				},
			});
			return false;
		}
	}

	$('#chat_sidebar_container').on('click', '.chat-item-delete', ev => {
		const button = ev.currentTarget;
		const parent = button.closest('li');
		const chatId = parent.getAttribute('id');
		const chatTitle = parent.querySelector('.chat-item-title').innerText;
		deleteChatItem(chatId, chatTitle);
	});

	$('#chat_sidebar_container').on('click', '.chat-item-update-title', ev => {
		const button = ev.currentTarget;
		const parent = button.closest('.chat-list-item');
		const title = parent.querySelector('.chat-item-title');
		const chatId = parent.getAttribute('id');
		const currentText = title.innerText;

		function setEditMode(mode) {
			if (mode === 'editStart') {
				parent.classList.add('edit-mode');

				title.setAttribute('data-current-text', currentText);
				title.setAttribute('contentEditable', true);
				title.focus();
				window.getSelection().selectAllChildren(title);
			} else if (mode === 'editEnd') {
				parent.classList.remove('edit-mode');

				title.removeAttribute('contentEditable');
				title.removeAttribute('data-current-text');
			}
		}

		function keydownHandler(ev) {
			const { key } = ev;
			const escapePressed = key === 'Escape';
			const enterPressed = key === 'Enter';

			if (!escapePressed && !enterPressed) return;

			ev.preventDefault();

			if (escapePressed) {
				title.innerText = currentText;
			}

			if (enterPressed) {
				saveChatNewTitle(chatId, title.innerText);
			}

			setEditMode('editEnd');
			document.removeEventListener('keydown', keydownHandler);
		}

		// if alreay editting then turn the edit button to a save button
		if (title.hasAttribute('contentEditable')) {
			setEditMode('editEnd');
			document.removeEventListener('keydown', keydownHandler);
			return saveChatNewTitle(chatId, title.innerText);
		}

		$('.chat-list-ul .edit-mode').each((i, el) => {
			const title = el.querySelector('.chat-item-title');
			title.innerText = title.getAttribute('data-current-text');
			title.removeAttribute('data-current-text');
			title.removeAttribute('contentEditable');
			el.classList.remove('edit-mode');
		});

		setEditMode('editStart');

		document.addEventListener('keydown', keydownHandler);
	});

	$('#chat_search_word').on('keyup', function () {
		return searchChatFunction();
	});

	$('body').on('input', '#prompt', ev => {
		const el = ev.target;
		el.style.height = '5px';
		el.style.height = el.scrollHeight + 'px';
		const recordTrigger = $('.lqd-chat-record-trigger');

		// check if value is not empty and then hide .lqd-chat-record-trigger and .lqd-chat-record-stop-trigger elements
		if (
			el.value &&
			el.value !== '' &&
			!(Array.isArray(el.value) && el.value.length === 0) &&
			!(typeof el.value === 'object' && Object.keys(el.value).length === 0)
		) {
			recordTrigger.hide();
		} else {
			recordTrigger.show();
		}
	});

	$('#selectDocInput').change(function () {
		if (this.files && this.files[0]) {
			let reader = new FileReader();
			pdf = this.files[0];

			toastr.success(magicai_localize.analyze_file_begin);

			startNewDocChat(pdf, this.files[0].type);

			document.getElementById('mainupscale_src') &&
				(document.getElementById('mainupscale_src').style.display =
					'none');
		}
	});

	if (stream_type == 'backend') {
		window.addEventListener('beforeunload', function(e) {
			reduceOnStop();
		});
	}
});

$('body').on('click', '.chat-download', event => {
	const button = event.currentTarget;
	const docType = button.dataset.docType;
	const docName = button.dataset.docName || 'document';

	const container = document.querySelector('.chats-container');
	let content = container?.parentElement?.innerHTML;
	let html;

	if ( !content ) return;

	if ( docType === 'pdf' ) {
		return html2pdf()
			.set({
				filename: docName
			})
			.from(content)
			.toPdf()
			.save();
	}

	if ( docType === 'txt' ) {
		html = container.innerText;
	} else {
		html = `
	<html ${this.doctype === 'doc'
		? 'xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40"'
		: ''
}>
	<head>
		<meta charset="utf-8" />
		<title>${docName}</title>
	</head>
	<body>
		${content}
	</body>
	</html>`;
	}

	const url = `${docType === 'doc'
		? 'data:application/vnd.ms-word;charset=utf-8'
		: 'data:text/plain;charset=utf-8'
	},${encodeURIComponent(html)}`;

	const downloadLink = document.createElement('a');
	document.body.appendChild(downloadLink);
	downloadLink.href = url;
	downloadLink.download = `${docName}.${docType}`;
	downloadLink.click();

	document.body.removeChild(downloadLink);
});