@extends('panel.layout.settings')
@section('title', __('Menu Settings'))
@section('titlebar_actions')

    <x-button class="add-menu">
        <x-tabler-plus class="size-4" />
        {{ __('Add Menu') }}
    </x-button>
@endsection

@section('settings')
    <form
        id="settings_form"
        onsubmit="return menuSettingsSave();"
        enctype="multipart/form-data"
    >
        <div class="row mb-4">
            <div class="col-md-12">
                <div
                    class="flex flex-col space-y-1"
                    id="menu-items"
                >

                    @php
                        $setting->menu_options = $setting->menu_options
                            ? $setting->menu_options
                            : '[{"title": "Home","url": "#banner","target": false},{"title": "Features","url": "#features","target": false},{"title": "How it Works","url": "#how-it-works","target": false},{"title": "Testimonials","url": "#testimonials","target": false},{"title": "Pricing","url": "#pricing","target": false},{"title": "FAQ","url": "#faq","target": false}]';
                        $menu_options = json_decode($setting->menu_options, true);
                        foreach ($menu_options as $menu_item) {
                            printf(
                                '
							<div class="menu-item relative rounded-lg border !bg-white shadow-[0_10px_10px_rgba(0,0,0,0.06)] dark:!bg-opacity-5">
								<h4 class="accordion-title mb-0 flex cursor-pointer items-center justify-between !gap-1 !py-1 !pe-2 !ps-4">
									<span>%1$s</span>
									<small class="me-auto opacity-60">%4$s</small>
									<div class="accordion-controls flex items-center">
										<div class="menu-delete size-10 inline-flex cursor-pointer items-center justify-center rounded-md hover:bg-red-100 hover:text-red-500">
											<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M4 7l16 0"></path> <path d="M10 11l0 6"></path> <path d="M14 11l0 6"></path> <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path> <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path> </svg>
										</div>
										<span class="handle size-10 inline-flex cursor-move items-center justify-center rounded-md hover:bg-black hover:!bg-opacity-10 dark:hover:bg-white">
											<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> </svg>
										</span>
									</div>
								</h4>
								<div class="accordion-content mt-3 hidden p-3 pt-0">
									<div class="mb-3">
										<label class="form-label">%2$s</label>
										<input type="text" class="form-control menu-title" name="title" value="%1$s" required>
									</div>
									<div class="mb-3">
										<label class="form-label">%3$s</label>
										<input type="text" class="form-control menu-url" name="url" placeholder="https://" value="%4$s" required>
									</div>
									<div class="mb-3">
										<label class="form-check form-switch">
											<span class="form-check-label mr-2">%5$s</span>
											<input class="form-check-input menu-target" type="checkbox" %6$s>
										</label>
									</div>
								</div>
							</div>
							',
                                $menu_item['title'],
                                __('Title'),
                                __('URL'),
                                $menu_item['url'],
                                __('Open In New Tab'),
                                $menu_item['target'] === false ? '' : 'checked',
                            );
                        }
                    @endphp

                </div>
            </div>
        </div>

        <button
            class="btn btn-primary w-full"
            id="settings_button"
            form="settings_form"
        >
            {{ __('Save') }}
        </button>
    </form>
@endsection

@push('script')
    <script src="{{ custom_theme_url('/assets/js/panel/settings.js') }}"></script>

    <script src="{{ custom_theme_url('https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js') }}"></script>
    <script src="{{ custom_theme_url('https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.js') }}"></script>
    <script>
        $('#menu-items').sortable({
            handle: ".handle"
        });
    </script>

    <script>
        const new_menu_item = `
			<div class="menu-item !bg-white rounded-lg shadow-[0_10px_10px_rgba(0,0,0,0.06)] border relative dark:!bg-opacity-5">
				<h4 class="accordion-title flex items-center justify-between !gap-1 mb-0 !ps-4 !pe-2 !py-1 cursor-pointer">
					<span>Menu Item</span>
					<small class="opacity-60 me-auto">#</small>
					<div class="accordion-controls flex items-center">
						<div class="menu-delete inline-flex items-center justify-center w-10 h-10 rounded-md hover:bg-red-100 hover:text-red-500 cursor-pointer">
							<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path stroke="none" d="M0 0h24v24H0z" fill="none"></path> <path d="M4 7l16 0"></path> <path d="M10 11l0 6"></path> <path d="M14 11l0 6"></path> <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12"></path> <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3"></path> </svg>
						</div>
						<span class="handle inline-flex items-center justify-center w-10 h-10 rounded-md cursor-move hover:bg-black hover:!bg-opacity-10 dark:hover:bg-white">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"> <path d="M9 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M9 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> <path d="M15 19m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0"></path> </svg>
						</span>
					</div>
				</h4>
				<div class="accordion-content hidden mt-3 p-3 pt-0">
					<div class="mb-3">
						<label class="form-label">{{ __('Title') }}</label>
						<input type="text" class="form-control menu-title" name="title" value="" required>
					</div>
					<div class="mb-3">
						<label class="form-label">{{ __('URL') }}</label>
						<input type="text" class="form-control menu-url" name="title" placeholder="https://" value="" required>
					</div>
					<div class="mb-3">
						<label class="form-check form-switch">
							<span class="form-check-label mr-2">{{ __('Open In New Tab') }}</span>
							<input class="form-check-input menu-target" type="checkbox">
						</label>
					</div>
				</div>
			</div>`;

        $('body').on('click', '.accordion-title', ev => {
            const accordionTitle = ev.currentTarget;
            accordionTitle.classList.toggle("active");
            accordionTitle.nextElementSibling.classList.toggle("hidden");
        });

        $(".add-menu").click(function() {
            $("#menu-items").append(new_menu_item);
        });

        $('body').on('input', 'input.menu-title', ev => {
            const input = ev.currentTarget;
            const value = input.value;

            input.closest('.menu-item').querySelector('.accordion-title > span').innerText = value;
        });

        $('body').on('input', 'input.menu-url', ev => {
            const input = ev.currentTarget;
            const value = input.value;

            input.closest('.menu-item').querySelector('.accordion-title > small').innerText = value;
        });

        $('body').on('click', '.menu-delete', function() {
            $(this).closest('.menu-item').remove();
        });
    </script>
@endpush
