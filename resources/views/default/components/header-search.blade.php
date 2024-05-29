<form
    class="header-search group me-2 transition-all max-lg:invisible max-lg:fixed max-lg:bottom-16 max-lg:left-0 max-lg:z-[99] max-lg:m-0 max-lg:me-0 max-lg:w-full max-lg:origin-bottom max-lg:-translate-y-2 max-lg:scale-95 max-lg:opacity-0 max-lg:[&.lqd-is-active]:visible max-lg:[&.lqd-is-active]:translate-y-0 max-lg:[&.lqd-is-active]:scale-100 max-lg:[&.lqd-is-active]:opacity-100"
    id="header-search"
    x-init
    :class="{ 'lqd-is-active': !$store.mobileNav.searchCollapse }"
>
    <div class="relative w-full max-lg:bg-white max-lg:p-3 max-lg:dark:bg-zinc-800">
        <x-tabler-search
            class="lqd-header-search-icon pointer-events-none absolute start-3 top-1/2 z-10 w-5 -translate-y-1/2 opacity-75 max-lg:start-6"
            stroke-width="1.5"
        />
        <x-forms.input
            class="header-search-input rounded-full border-clay bg-clay ps-10 transition-colors max-lg:rounded-md"
            id="top_search_word"
            container-class="peer"
            type="search"
            onkeydown="return event.key != 'Enter';"
            placeholder="{{ __('Search for templates and documents...') }}"
        />
        <kbd
            class="peer-focus-within:scale-70 pointer-events-none absolute end-3 top-1/2 z-10 inline-block -translate-y-1/2 rounded-full bg-background px-2 py-1 text-3xs leading-none opacity-0 transition-all group-[.is-searching]:invisible group-[.is-searching]:opacity-0 peer-focus-within:invisible peer-focus-within:opacity-0 max-lg:hidden">
            <span id="search-shortcut-key"></span> + K
        </kbd>
        <span class="absolute end-5 top-1/2 -translate-y-1/2">
            <x-tabler-loader-2
                class="hidden animate-spin group-[.is-searching]:block"
                stroke-width="1.5"
                role="status"
            />
        </span>
        <span
            class="pointer-events-none absolute end-3 top-1/2 -translate-x-2 -translate-y-1/2 opacity-0 transition-all group-[.done-searching]:hidden group-[.is-searching]:hidden peer-focus-within:translate-x-0 peer-focus-within:opacity-100 rtl:-scale-x-100"
        >
            <x-tabler-chevron-right class="size-5" />
        </span>
        <div
            class="navbar-search-results absolute start-0 top-[calc(100%+17px)] z-50 hidden max-h-96 w-full overflow-y-auto overscroll-contain rounded-md bg-background shadow-[0_10px_70px_rgba(0,0,0,0.1)] group-[.done-searching]:block dark:bg-background max-lg:bottom-full max-lg:end-0 max-lg:start-0 max-lg:top-auto max-lg:w-auto"
            id="search_results"
        >
            <h3 class="m-0 border-b px-3 py-3 text-base font-medium">
                {{ __('Search results') }}
            </h3>
            <!-- Search results here -->
            <div class="search-results-container"></div>
        </div>
    </div>
</form>
