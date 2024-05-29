@php
    $base_class = "lqd-docs-item lqd-docs-item-$style relative w-full items-center border-b transition-all last:border-b-0 hover:bg-foreground/5 group-[&[data-view-mode=grid]]:min-h-48 group-[&[data-view-mode=grid]]:bg-card-background group-[&[data-view-mode=grid]]:gap-0 group-[&[data-view-mode=grid]]:pb-1";

    if ($style === 'min') {
        $base_class .= ' flex gap-4 p-4 text-xs last:border-none';
    } else {
        $base_class .= ' grid gap-4 px-4 py-3 text-2xs font-medium';
    }
@endphp

@if ($style === 'min')
    <a
        data-type="{{ trim($entry->generator->type) }}"
        {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
        href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
    >
        <x-lqd-icon
            class="lqd-docs-item-icon"
            size="lg"
            style="background: {{ $entry->generator->color }}"
        >
            <span class="size-5 flex">
                @if ($entry->generator->image !== 'none')
                    {!! html_entity_decode($entry->generator->image) !!}
                @endif
            </span>
        </x-lqd-icon>
        <span class="block w-0 max-w-full grow overflow-hidden">
            <span class="lqd-docs-item-title block text-sm font-medium">
                {{ __($entry->generator->title) }}
            </span>
            <span class="lqd-docs-item-desc opacity-45 block w-full overflow-hidden overflow-ellipsis whitespace-nowrap italic">
                {{ str()->words(__($entry->generator->description), 50) }}
            </span>
        </span>
        <span class="flex flex-col whitespace-nowrap">
            {{ __('in Workbook') }}
            <span class="lqd-docs-item-date opacity-45 italic">
                {{ $entry->created_at->format('M d, Y') }}
            </span>
        </span>
    </a>
@else
    <div
        data-type="{{ trim($entry->generator->type) }}"
        {{ $attributes->withoutTwMergeClasses()->twMerge($base_class, $attributes->get('class')) }}
    >
        <a
            class="lqd-docs-item-overlay-link absolute left-0 top-0 z-[2] h-full w-full"
            href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.single', $entry->slug)) }}"
            title="{{ __('View and edit') }}"
        ></a>

        <div
            class="lqd-docs-item-content sort-name grid grid-flow-col-dense items-center justify-start gap-3 text-sm transition-border group-[&[data-view-mode=grid]]:mb-1 group-[&[data-view-mode=grid]]:block group-[&[data-view-mode=grid]]:h-28 group-[&[data-view-mode=grid]]:items-start group-[&[data-view-mode=grid]]:overflow-hidden group-[&[data-view-mode=grid]]:border-b group-[&[data-view-mode=grid]]:pb-3 group-[&[data-view-mode=grid]]:pt-3 group-[&[data-view-mode=grid]]:text-2xs">
            @if ($entry->generator->type == 'image')
                <img
                    class="lqd-docs-item-img size-9 rounded-full object-cover object-center group-[&[data-view-mode=grid]]:mb-2 group-[&[data-view-mode=grid]]:aspect-video group-[&[data-view-mode=grid]]:h-auto group-[&[data-view-mode=grid]]:w-full group-[&[data-view-mode=grid]]:rounded-md"
                    src="{{ custom_theme_url($entry->output) }}"
                    alt="{{ __($entry->generator->title) }}"
                    loading="lazy"
                />
            @else
                <x-lqd-icon
                    class="lqd-docs-item-icon"
                    style="background: {{ $entry->generator->color }}"
                >
                    <span class="size-5">
                        @if ($entry->generator->image !== 'none')
                            {!! html_entity_decode($entry->generator->image) !!}
                        @endif
                    </span>
                </x-lqd-icon>
            @endif

            <div class="lqd-docs-item-content-inner grow overflow-hidden group-[&[data-view-mode=grid]]:h-full">
                <p
                    class="lqd-docs-item-title overflow-hidden overflow-ellipsis whitespace-nowrap group-[&[data-view-mode=grid]]:h-full group-[&[data-view-mode=grid]]:whitespace-normal">
                    @if (in_array($entry->generator->type, ['text', 'youtube', 'rss', 'code', 'image']))
                        {{ str()->limit(strip_tags($entry->generator->type === 'image' ? $entry->title : $entry->title . ' : ' . $entry->output), 50) }}
                    @elseif($entry->generator->type == 'audio')
                        {!! str()->limit($entry->title . ' : ' . $entry->output, 50) !!}
                    @elseif ($entry->generator->type == 'voiceover')
                        {{ str()->limit($entry->title) }}
                    @endif
                </p>
            </div>
        </div>

        <p
            class="lqd-docs-item-type sort-file inline-block w-auto justify-self-start whitespace-nowrap rounded-md px-1.5 py-1 text-3xs font-medium leading-tight text-black"
            data-generator-title="{{ trim($entry->generator->title) }}"
            style="background: {{ $entry->generator->color }}"
        >
            {{ __($entry->generator->title) }}
        </p>

        <p
            class="lqd-docs-item-date sort-date m-0 group-[&[data-view-mode=list]]:font-normal"
            data-date="{{ trim(strtotime($entry->created_at)) }}"
        >
            {{ date('M j Y', strtotime($entry->created_at)) }}
            <span class="opacity-50 group-[&[data-view-mode=grid]]:hidden">
                , {{ date('H:i', strtotime($entry->created_at)) }}
            </span>
        </p>

        <span
            class="lqd-docs-item-cost sort-cost"
            data-cost="{{ trim($entry->credits) }}"
        >
            {{ $entry->credits }}
        </span>

        <div class="lqd-docs-item-actions flex items-center justify-end gap-2 font-normal">
            <x-favorite-button
                class="group-[&[data-view-mode=grid]]:absolute group-[&[data-view-mode=grid]]:end-3 group-[&[data-view-mode=grid]]:top-3 group-[&[data-view-mode=grid]]:h-8 group-[&[data-view-mode=grid]]:w-8"
                id="{{ $entry->id }}"
                is-favorite="{{ isFavoritedDoc($entry->id) }}"
                update-url="/dashboard/user/openai/documents/favorite"
            />
            <x-button
                class="size-9 z-10 group-[&[data-view-mode=grid]]:hidden"
                size="none"
                variant="ghost-shadow"
                hover-variant="danger"
                href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.delete', $entry->slug)) }}"
                onclick="return confirm('Are you sure?')"
                title="{{ __('Delete') }}"
            >
                <x-tabler-x class="size-4" />
            </x-button>

            <x-dropdown.dropdown
                class:dropdown-dropdown="group-[&[data-view-mode=grid]]:top-auto group-[&[data-view-mode=grid]]:bottom-full"
                anchor="end"
                offsetY="5px"
                triggerType="click"
            >
                <x-slot:trigger
                    class="before:-star[5%]-0 size-9 z-10 p-0 text-foreground/50 before:absolute before:-top-[5%] before:h-[120%] before:w-[120%] hover:bg-background group-[&[data-view-mode=grid]]:-me-3 group-[&[data-view-mode=grid]]:text-base group-[&[data-view-mode=grid]]:text-foreground"
                    variant="ghost"
                    size="xs"
                >
                    <x-tabler-dots-vertical class="size-5 group-[&[data-view-mode=grid]]:h-4 group-[&[data-view-mode=grid]]:w-4" />
                </x-slot:trigger>

                <x-slot:dropdown
                    class="overflow-hidden whitespace-nowrap py-1 text-2xs font-medium group-[&[data-view-mode=grid]]:-me-3"
                >
                    <x-modal
                        title="{{ __('Move Document') }}"
                        disable-modal="{{ $app_is_demo }}"
                        disable-modal-message="{{ __('This feature is disabled in Demo version.') }}"
                    >
                        <x-slot:trigger
                            class="w-full justify-start rounded-none px-3 py-2 text-2xs hover:translate-y-0 hover:bg-foreground/5 hover:shadow-none focus-visible:bg-foreground/5"
                            variant="ghost"
                        >
                            <x-tabler-file-export class="size-5" />
                            {{ __('Move to folder') }}
                        </x-slot:trigger>

                        <x-slot:modal>
                            @includeIf('panel.user.openai.components.modals.move-to-folder', [
                                'file_slug' => $entry->slug,
                            ])
                        </x-slot:modal>
                    </x-modal>

                    <x-button
                        class="hidden w-full justify-start rounded-none px-3 py-2 text-2xs shadow-none hover:translate-y-0 hover:bg-foreground/5 hover:text-inherit hover:shadow-none focus-visible:bg-foreground/5 focus-visible:text-inherit group-[&[data-view-mode=grid]]:flex"
                        size="none"
                        variant="ghost-shadow"
                        hover-variant="danger"
                        href="{{ LaravelLocalization::localizeUrl(route('dashboard.user.openai.documents.delete', $entry->slug)) }}"
                        onclick="return confirm('Are you sure?')"
                    >
                        <x-tabler-circle-minus class="size-4 text-red-600" />
                        {{ __('Delete') }}
                    </x-button>
                </x-slot:dropdown>
            </x-dropdown.dropdown>
        </div>
    </div>
@endif
