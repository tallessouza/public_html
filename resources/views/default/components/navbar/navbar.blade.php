<a class="lqd-skip-link pointer-events-none fixed start-7 top-7 z-[90] rounded-md bg-background px-3 py-1 text-lg opacity-0 shadow-xl focus-visible:pointer-events-auto focus-visible:opacity-100 focus-visible:outline-primary" href="#lqd-titlebar">
    {{ __('Skip to content') }}
</a>

<button class="lqd-navbar-expander size-6 fixed start-[--navbar-width] top-[calc(var(--header-height)/2)] z-[999] inline-flex -translate-x-1/2 -translate-y-1/2 cursor-pointer items-center justify-center rounded-full border-0 bg-foreground/10 p-0 text-heading-foreground backdrop-blur-sm transition-all hover:bg-heading-foreground hover:text-heading-background group-[.navbar-shrinked]/body:!start-[80px] group-[.navbar-shrinked]/body:rotate-180 max-lg:hidden" x-init @click.prevent="$store.navbarShrink.toggle()">
    <x-tabler-chevron-left class="w-4" />
</button>

<aside class="lqd-navbar max-lg:rounded-b-5 z-[99] w-[--navbar-width] shrink-0 overflow-hidden rounded-ee-navbar-ee rounded-es-navbar-es rounded-se-navbar-se rounded-ss-navbar-ss border-e border-navbar-border bg-navbar-background text-navbar font-medium text-navbar-foreground transition-all max-lg:invisible max-lg:absolute max-lg:left-0 max-lg:top-[65px] max-lg:z-[99] max-lg:max-h-[calc(85vh-2rem)] max-lg:min-h-0 max-lg:w-full max-lg:origin-top max-lg:-translate-y-2 max-lg:scale-95 max-lg:overflow-y-auto max-lg:bg-background max-lg:p-0 max-lg:opacity-0 max-lg:shadow-xl lg:sticky lg:top-0 lg:h-screen max-lg:[&.lqd-is-active]:visible max-lg:[&.lqd-is-active]:translate-y-0 max-lg:[&.lqd-is-active]:scale-100 max-lg:[&.lqd-is-active]:opacity-100" x-init :class="{ 'lqd-is-active': !$store.mobileNav.navCollapse }">
    <div class="lqd-navbar-inner -me-navbar-me h-full overflow-y-auto overscroll-contain pe-navbar-pe ps-navbar-ps">
        <div class="lqd-navbar-logo relative flex min-h-[--header-height] max-w-full items-center pe-navbar-link-pe ps-navbar-link-ps group-[.navbar-shrinked]/body:w-full group-[.navbar-shrinked]/body:justify-center group-[.navbar-shrinked]/body:px-0 group-[.navbar-shrinked]/body:text-center max-lg:hidden">
            <a class="block px-0" href="{{ LaravelLocalization::localizeUrl(route('dashboard.index')) }}">
                @if (isset($setting->logo_dashboard))
                <img class="h-auto w-full group-[.navbar-shrinked]/body:hidden dark:hidden" src="{{ custom_theme_url($setting->logo_dashboard_path, true) }}" @if (isset($setting->logo_dashboard_2x_path) && !empty($setting->logo_dashboard_2x_path)) srcset="/{{ $setting->logo_dashboard_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
                >
                <img class="hidden h-auto w-full group-[.navbar-shrinked]/body:hidden dark:block" src="{{ custom_theme_url($setting->logo_dashboard_dark_path, true) }}" @if (isset($setting->logo_dashboard_dark_2x_path) && !empty($setting->logo_dashboard_dark_2x_path)) srcset="/{{ $setting->logo_dashboard_dark_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
                >
                @else
                <img class="h-auto w-full group-[.navbar-shrinked]/body:hidden dark:hidden" src="{{ custom_theme_url($setting->logo_path, true) }}" @if (isset($setting->logo_2x_path) && !empty($setting->logo_2x_path)) srcset="/{{ $setting->logo_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
                >
                <img class="hidden h-auto w-full group-[.navbar-shrinked]/body:hidden dark:block" src="{{ custom_theme_url($setting->logo_dark_path, true) }}" @if (isset($setting->logo_dark_2x_path) && !empty($setting->logo_dark_2x_path)) srcset="/{{ $setting->logo_dark_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
                >
                @endif

                <!-- collapsed -->
                <img class="max-w-10 mx-auto hidden h-auto w-full group-[.navbar-shrinked]/body:block dark:!hidden" src="{{ custom_theme_url($setting->logo_collapsed_path, true) }}" @if (isset($setting->logo_collapsed_2x_path) && !empty($setting->logo_collapsed_2x_path)) srcset="/{{ $setting->logo_collapsed_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
                >
                <img class="max-w-10 mx-auto hidden h-auto w-full group-[.theme-dark.navbar-shrinked]/body:block" src="{{ custom_theme_url($setting->logo_collapsed_dark_path, true) }}" @if (isset($setting->logo_collapsed_dark_2x_path) && !empty($setting->logo_collapsed_dark_2x_path)) srcset="/{{ $setting->logo_collapsed_dark_2x_path }} 2x" @endif
                alt="{{ $setting->site_name }}"
                >

            </a>
        </div>
        <nav class="lqd-navbar-nav" id="navbar-menu">
            <ul class="lqd-navbar-ul">

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Lendár[IA]') }}" href="dashboard.user.openai.chat.chat" slug="lendaria-BJE0O" icon="tabler-yoga" />
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('IAs por Negócio') }}" dropdown-trigger icon="tabler-building-store" />
                    <x-navbar.dropdown.dropdown>
                        <x-navbar.item>
                            <x-navbar.link label="Social Media" href="dashboard.user.openai.list" slug="filter=Social Media" icon="tabler-users" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Copywriting" href="dashboard.user.openai.list" slug="filter=Copywriting" icon="tabler-pencil" />
                        </x-navbar.item>
                        <!-- <x-navbar.item>
                            <x-navbar.link label="Tráfego" href="dashboard.user.openai.list" slug="filter=advertising" icon="tabler-ad-circle" />
                        </x-navbar.item> -->
                        <x-navbar.item>
                            <x-navbar.link label="Branding" href="dashboard.user.openai.list" slug="filter=Branding" icon="tabler-building-store" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Marketing" href="dashboard.user.openai.list" slug="filter=Marketing" icon="tabler-target" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Conteúdo" href="dashboard.user.openai.list" slug="filter=Conteúdo" icon="tabler-movie" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Administrativo" href="dashboard.user.openai.list" slug="filter=Administrativo" icon="tabler-device-laptop" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Produtos" href="dashboard.user.openai.list" slug="filter=Produtos" icon="tabler-brand-producthunt" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Vendas" href="dashboard.user.openai.list" slug="filter=Vendas" icon="tabler-moneybag" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Todas" href="dashboard.user.openai.list" />
                        </x-navbar.item>
                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>
                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('GPTs') }}" dropdown-trigger icon="tabler-message-circle" />
                    <x-navbar.dropdown.dropdown>

                        <x-navbar.item>
                            <x-navbar.link label="Assistentes" href="dashboard.user.openai.chat.list" slug="filter=Assistentes" icon="tabler-users" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Clones Conselheiros" href="dashboard.user.openai.chat.list" slug="filter=Clones Conselheiros" icon="tabler-old" />
                        </x-navbar.item>
                        <!-- <x-navbar.item>
                            <x-navbar.link label="Tráfego" href="dashboard.user.openai.chat.list" slug="filter=Tráfego" icon="tabler-ad-circle" />
                        </x-navbar.item> -->
                        <x-navbar.item>
                            <x-navbar.link label="Código" href="dashboard.user.openai.chat.list" slug="filter=Código" icon="tabler-device-laptop" />
                        </x-navbar.item>
                        <x-navbar.item>
                            <x-navbar.link label="Escrita" href="dashboard.user.openai.chat.list" slug="filter=Escrita" icon="tabler-pencil" />
                        </x-navbar.item>
                        <!-- <x-navbar.item>
                            <x-navbar.link label="Tratamento de Dados" href="dashboard.user.openai.chat.list" slug="filter=Tratamento de Dados" icon="tabler-file-spreadsheet" />
                        </x-navbar.item> -->
                        <x-navbar.item>
                            <x-navbar.link label="Todos" href="dashboard.user.openai.chat.list" />
                        </x-navbar.item>
                        <!-- <x-navbar.item>
                            <x-navbar.link label="{{ __('AI Chat') }}" href="dashboard.user.openai.chat.list" icon="tabler-message-dots" active-condition="{{ activeRoute('dashboard.user.openai.chat.*') }}" />
                        </x-navbar.item> -->
                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>
                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('IAs para Gerar') }}" dropdown-trigger icon="tabler-pencil" />
                    <x-navbar.dropdown.dropdown>
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('Código') }}" href="dashboard.user.openai.chat.chat" slug="grimore-6isY5" icon="tabler-device-laptop" />
                        </x-navbar.dropdown.item>
                        @if ($setting->feature_ai_advanced_editor)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Editor') }}" href="dashboard.user.generator.index" icon="tabler-notebook" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_writer)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Writer') }}" href="dashboard.user.openai.list" active-condition="{{ activeRoute('dashboard.user.openai.list', 'dashboard.user.openai.generator.*') }}" icon="tabler-notes" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($settings_two->feature_ai_video)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Video') }}" href="dashboard.user.openai.generator" slug="ai_video" icon="tabler-video" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_image)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Image') }}" href="dashboard.user.openai.generator" slug="ai_image_generator" icon="tabler-photo" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_article_wizard)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Article Wizard') }}" href="dashboard.user.openai.articlewizard.new" icon="tabler-ad-2" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_pdf)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI File Chat') }}" href="dashboard.user.openai.generator.workbook" slug="ai_pdf" icon="tabler-file-pencil" new />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_vision)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Vision') }}" href="dashboard.user.openai.generator.workbook" slug="ai_vision" icon="tabler-scan-eye" new />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_rewriter)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI ReWriter') }}" href="dashboard.user.openai.rewriter" slug="ai_rewriter" icon="tabler-ballpen" active-condition="{{ activeRoute('dashboard.user.openai.rewriter', 'dashboard.user.openai.rewriter.*') }}" new />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_chat_image)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Chat Image') }}" href="dashboard.user.openai.generator.workbook" slug="ai_chat_image" icon="tabler-photo" active-condition="{{ route('dashboard.user.openai.generator.workbook', 'ai_chat_image') === url()->current() }}" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_chat)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Chat') }}" href="dashboard.user.openai.chat.list" icon="tabler-message-dots" active-condition="{{ activeRoute('dashboard.user.openai.chat.*') }}" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_code)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Code') }}" href="dashboard.user.openai.generator.workbook" slug="ai_code_generator" icon="tabler-terminal-2" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_youtube)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI YouTube') }}" href="dashboard.user.openai.generator.workbook" slug="ai_youtube" icon="tabler-brand-youtube" {{-- active-condition="{{ route('dashboard.user.openai.generator.workbook', 'ai_youtube') === url()->current() }}" --}} />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_rss)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI RSS') }}" href="dashboard.user.openai.generator.workbook" slug="ai_rss" icon="tabler-rss" {{-- active-condition="{{ route('dashboard.user.openai.generator.workbook', 'ai_rss') === url()->current() }}" --}} />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_speech_to_text)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Speech to Text') }}" href="dashboard.user.openai.generator" slug="ai_speech_to_text" icon="tabler-microphone" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_voiceover)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Voiceover') }}" href="dashboard.user.openai.generator" slug="ai_voiceover" icon="tabler-volume" />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($setting->feature_ai_voice_clone && $settings_two->elevenlabs_api_key)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('AI Voice Clone') }}" href="dashboard.user.voice.index" active-condition="{{ activeRoute('dashboard.user.voice.*') }}" slug="ai_voiceover" icon="tabler-microphone-2" new />
                        </x-navbar.dropdown.item>
                        @endif

                        @php
                        $checkPlan = \App\Models\PaymentPlans::query()->where('is_team_plan', 1)->first();
                        @endphp

                        @if ($setting->team_functionality && !auth()->user()->getAttribute('team_id') && $checkPlan)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('Team') }}" href="dashboard.user.team.index" slug="ai_voiceover" icon="tabler-user-plus" active-condition="{{ activeRoute('dashboard.user.team.*') }}" new />
                        </x-navbar.dropdown.item>
                        @endif

                        @if ($app_is_demo || $setting?->user_api_option)
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('API Keys') }}" active-condition="{{ activeRoute('dashboard.user.apikeys.*') }}" href="dashboard.user.apikeys.index" icon="tabler-key" />
                        </x-navbar.dropdown.item>
                        @endif

                        @php
                        try {
                        $files = File::files(resource_path('views/default/components/navbar/extnavbars'));
                        } catch (\Throwable $th) {
                        $files = [];
                        }
                        @endphp
                        @foreach ($files as $file)
                        @php
                        $filenameWithoutExtension = substr($file->getFilename(), 0, -10);
                        @endphp
                        @include("components.navbar.extnavbars.{$filenameWithoutExtension}")
                        @endforeach
                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>
                <x-navbar.item has-dropdown>
                    <!-- <x-navbar.label>
                        {{ __('User') }}
                    </x-navbar.label> -->
                    <x-navbar.link label="{{ __('Minha Conta') }}" dropdown-trigger icon="tabler-users" />
                    <x-navbar.dropdown.dropdown>

                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('Dashboard') }}" href="dashboard.user.index" icon="tabler-layout-2" />
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('Documents') }}" href="dashboard.user.openai.documents.all" active-condition="{{ activeRoute('dashboard.user.openai.documents.*') }}" icon="tabler-archive" />
                        </x-navbar.dropdown.item>
                        <x-navbar.dropdown.item>
                            <x-navbar.link label="{{ __('Brand Voice') }}" href="dashboard.user.brand.index" icon="tabler-brand-trello" />
                        </x-navbar.dropdown.item>
                        <x-navbar.item has-dropdown>
                            <x-navbar.link label="{{ __('Meus GPTS') }}" href="" icon="tabler-message-circle" dropdown-trigger />
                            <x-navbar.dropdown.dropdown>
                                <x-navbar.item>
                                    <x-navbar.link label="Acessar GPTS" href="dashboard.user.openai.chat.list" slug="filter=User" icon="tabler-robot-face" />
                                </x-navbar.item>
                                <x-navbar.dropdown.item>
                                    <x-navbar.dropdown.link label="{{ __('Gerenciar GPTS') }}" href="dashboard.user.openai.chat.listOwn" icon="tabler-pencil">
                                    </x-navbar.dropdown.link>
                                </x-navbar.dropdown.item>
                                <x-navbar.dropdown.item>
                                    <x-navbar.dropdown.link label="{{ __('Treinar GPTS') }}" href="dashboard.user.chatbot.index" icon="tabler-adjustments-bolt">
                                    </x-navbar.dropdown.link>
                                </x-navbar.dropdown.item>    
                            </x-navbar.dropdown.dropdown>
                            </x-navbar.item>
                        </x-navbar.dropdown.dropdown>
                    </x-navbar.item>
                    <x-navbar.item>
                        <x-navbar.link label="{{ __('Whatsapp Lendário') }}" href="dashboard.user.whatsapp" icon="tabler-brand-whatsapp" />
                    </x-navbar.item>




                @if ($setting->feature_affilates)
                <x-navbar.item>
                    <x-navbar.link label="{{ __('Affiliates') }}" href="dashboard.user.affiliates.index" icon="tabler-currency-dollar" />
                </x-navbar.item>
                @endif
                <x-navbar.item>
                    <x-navbar.link label="{{ __('Support') }}" href="dashboard.support.list" active-condition="{{ activeRoute('dashboard.support.*') }}" icon="tabler-lifebuoy" />
                </x-navbar.item>
                <a class="nav-link lqd-navbar-link flex items-center gap-2 ps-navbar-link-ps pe-navbar-link-pe pt-navbar-link-pt pb-navbar-link-pb rounded-xl relative transition-colors group/link
		                hover:bg-navbar-background-hover/5 hover:text-navbar-foreground-hover
		                [&.active]:bg-navbar-background-active/5 [&.active]:text-navbar-foreground-active
		                dark:[&.active]:bg-transparent
		                dark:before:w-1.5 dark:before:h-full dark:before:absolute dark:before:top-0 dark:before:-start-2 dark:before:bg-primary dark:before:rounded-e-lg dark:before:opacity-0
		                dark:[&.active]:before:opacity-100 " href="https://hotmart.com/pt-br/club/formacao-lendaria/products/3922448/" icon="tabler-brand-whatsapp" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Tutoriais
                    </a>

                @if (App\Models\Integration\Integration::query()->whereHas('hasExtension')->count())
                <x-navbar.item>
                    <x-navbar.link label="{{ __('Integration') }}" href="dashboard.user.integration.index" active-condition="{{ activeRoute('dashboard.user.integration.*') }}" icon="tabler-webhook" />
                </x-navbar.item>
                @endif

                <x-navbar.item>
                    <x-navbar.divider />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.label>
                        {{ __('Links') }}
                    </x-navbar.label>
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Favorites') }}" href="dashboard.user.openai.list" slug="filter=Favoritos" icon="tabler-star" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Workbook') }}" href="dashboard.user.openai.documents.all" icon="tabler-folder" />
                </x-navbar.item>

                {{-- Admin menu items --}}
                @if (Auth::user()->type == 'admin')
                <x-navbar.item>
                    <x-navbar.divider />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.label>
                        {{ __('Admin') }}
                    </x-navbar.label>
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Dashboard') }}" href="dashboard.admin.index" icon="tabler-layout-2" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Marketplace') }}" href="dashboard.admin.marketplace.index" icon="tabler-building-store" new />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Themes') }}" href="dashboard.admin.themes.index" icon="tabler-palette" new />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('User Management') }}" href="dashboard.admin.users.index" active-condition="{{ activeRoute('dashboard.admin.users.*') }}" icon="tabler-users" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Google Adsense') }}" href="dashboard.admin.ads.index" active-condition="{{ activeRoute('dashboard.admin.ads.*') }}" icon="tabler-ad-circle" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Support Requests') }}" href="dashboard.support.list" active-condition="{{ activeRoute('dashboard.support.*') }}" icon="tabler-lifebuoy" />
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('Templates') }}" href="dashboard.admin.openai.list" icon="tabler-list-details" active-condition="{{ activeRouteBulk('dashboard.admin.openai.list', 'dashboard.admin.openai.custom.*', 'dashboard.admin.openai.categories.*') }}" dropdown-trigger />
                    <x-navbar.dropdown.dropdown open="{{ activeRouteBulk('dashboard.admin.openai.list', 'dashboard.admin.openai.custom.*', 'dashboard.admin.openai.categories.*', 'dashboard.email-templates.*') }}">

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Built-in Templates') }}" href="dashboard.admin.openai.list">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Custom Templates') }}" href="dashboard.admin.openai.custom.list" active-condition="{{ activeRouteBulk('dashboard.admin.openai.custom.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('AI Writer Categories') }}" href="dashboard.admin.openai.categories.list" active-condition="{{ activeRouteBulk('dashboard.admin.openai.categories.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('Chat Settings') }}" href="" icon="tabler-message-circle" active-condition="{{ activeRouteBulk('dashboard.admin.chatbot.*', 'dashboard.admin.openai.chat.list', 'dashboard.admin.openai.chat.addOrUpdate', 'dashboard.admin.openai.chat.category', 'dashboard.admin.openai.chat.addOrUpdateCategory') }}" dropdown-trigger />
                    <x-navbar.dropdown.dropdown open="{{ activeRouteBulk('dashboard.admin.chatbot.*', 'dashboard.admin.openai.chat.list', 'dashboard.admin.openai.chat.addOrUpdate', 'dashboard.admin.openai.chat.category', 'dashboard.admin.openai.chat.addOrUpdateCategory') }}">

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Chat Categories') }}" href="dashboard.admin.openai.chat.category" active-condition="{{ activeRoute('dashboard.admin.openai.chat.category.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Chat Templates') }}" href="dashboard.admin.openai.chat.list" active-condition="{{ activeRoute('dashboard.admin.chat.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Chatbot Training') }}" href="dashboard.admin.chatbot.index" active-condition="{{ activeRoute('dashboard.admin.chatbot') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Floating Chat Settings') }}" href="dashboard.admin.chatbot.setting">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('Frontend') }}" href="dashboard.admin.frontend.settings" icon="tabler-device-laptop" active-condition="{{ activeRouteBulk('dashboard.admin.testimonials.*', 'dashboard.admin.frontend.authsettings', 'dashboard.admin.frontend.settings', 'dashboard.admin.frontend.faq.*', 'dashboard.admin.frontend.tools.*', 'dashboard.admin.frontend.tools.*', 'dashboard.admin.frontend.future.*', 'dashboard.admin.frontend.whois.*', 'dashboard.admin.frontend.generatorlist.*', 'dashboard.admin.clients.*', 'dashboard.admin.howitWorks.*', 'dashboard.admin.whois.*', 'dashboard.admin.frontend.menusettings', 'dashboard.admin.frontend.sectionsettings') }}" dropdown-trigger />
                    <x-navbar.dropdown.dropdown open="{{ activeRouteBulk('dashboard.admin.testimonials.*', 'dashboard.admin.frontend.authsettings', 'dashboard.admin.frontend.settings', 'dashboard.admin.frontend.faq.*', 'dashboard.admin.frontend.tools.*', 'dashboard.admin.frontend.tools.*', 'dashboard.admin.frontend.future.*', 'dashboard.admin.frontend.whois.*', 'dashboard.admin.frontend.generatorlist.*', 'dashboard.admin.clients.*', 'dashboard.admin.howitWorks.*', 'dashboard.admin.whois.*', 'dashboard.admin.frontend.menusettings', 'dashboard.admin.frontend.sectionsettings') }}">

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Frontend Settings') }}" href="dashboard.admin.frontend.settings">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Frontend Section Settings') }}" href="dashboard.admin.frontend.sectionsettings">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Menu') }}" href="dashboard.admin.frontend.menusettings">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Auth Settings') }}" href="dashboard.admin.frontend.authsettings">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('F.A.Q') }}" href="dashboard.admin.frontend.faq.index" active-condition="{{ activeRoute('dashboard.admin.frontend.faq.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Tools Section') }}" href="dashboard.admin.frontend.tools.index" active-condition="{{ activeRoute('dashboard.admin.frontend.tools.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Features Section') }}" href="dashboard.admin.frontend.future.index" active-condition="{{ activeRoute('dashboard.admin.frontend.future.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Testimonials Section') }}" href="dashboard.admin.testimonials.index" active-condition="{{ activeRoute('dashboard.admin.testimonials.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Clients Section') }}" href="dashboard.admin.clients.index" active-condition="{{ activeRoute('dashboard.admin.clients.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('How it Works Section') }}" href="dashboard.admin.howitWorks.index" active-condition="{{ activeRoute('dashboard.admin.howitWorks.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Who Can Use Section') }}" href="dashboard.admin.frontend.whois.index" active-condition="{{ activeRoute('dashboard.admin.frontend.whois.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Generators List Section') }}" href="dashboard.admin.frontend.generatorlist.index" active-condition="{{ activeRoute('dashboard.admin.frontend.generatorlist.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('Finance') }}" href="dashboard.admin.finance.plans.index" icon="tabler-wallet" active-condition="{{ activeRouteBulk('dashboard.admin.finance.*', 'dashboard.admin.bank.transactions.list') }}" dropdown-trigger />
                    <x-navbar.dropdown.dropdown open="{{ activeRouteBulk('dashboard.admin.finance.*', 'dashboard.admin.bank.transactions.list') }}">

                        @if (bankActive())
                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Bank Transactions') }}" href="dashboard.admin.bank.transactions.list" badge="{{ countBankTansactions() }}" />
                        </x-navbar.dropdown.item>
                        @endif

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Membership Plans') }}" href="dashboard.admin.finance.plans.index" active-condition="{{ activeRoute('dashboard.admin.finance.plans.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Payment Gateways') }}" href="dashboard.admin.finance.paymentGateways.index" active-condition="{{ activeRoute('dashboard.admin.finance.paymentGateways.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Trial Features') }}" href="dashboard.admin.finance.free.feature" active-condition="{{ activeRoute('dashboard.admin.finance.free.*') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        @if ($setting->mobile_payment_active)
                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Mobile Payment') }}" href="dashboard.admin.finance.mobile.index" active-condition="{{ activeRoute('dashboard.admin.finance.mobile.index') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>
                        @endif

                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Pages') }}" href="dashboard.page.list" icon="tabler-file-description" active-condition="{{ activeRoute('dashboard.page.*') }}" />
                </x-navbar.item>

                {{-- <x-navbar.item>
                        <x-navbar.link
                            label="{{ __('ChatBot') }}"
                href="dashboard.chatbot.index"
                icon="tabler-message-2-code"
                active-condition="{{ activeRoute('dashboard.chatbot.*') }}"
                new
                />
                </x-navbar.item> --}}

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Blog') }}" href="dashboard.blog.list" active-condition="{{ activeRoute('dashboard.blog.*') }}" icon="tabler-pencil" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Affiliates') }}" href="dashboard.admin.affiliates.index" active-condition="{{ activeRoute('dashboard.admin.affiliates.*') }}" icon="tabler-brand-mastercard" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Coupons') }}" href="dashboard.admin.coupons.index" active-condition="{{ activeRoute('dashboard.admin.coupons.*') }}" icon="tabler-ticket" />
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Email Templates') }}" href="dashboard.email-templates.index" active-condition="{{ activeRoute('dashboard.email-templates.*') }}" icon="tabler-mail" />
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('API Integration') }}" href="dashboard.admin.settings.general" icon="tabler-api" active-condition="{{ activeRouteBulk('dashboard.admin.settings.*', 'elseyyid.translations.home', 'elseyyid.translations.lang') }}" dropdown-trigger />
                    <x-navbar.dropdown.dropdown open="{{ activeRouteBulk('dashboard.admin.settings.*', 'elseyyid.translations.home', 'elseyyid.translations.lang') }}">

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('OpenAI') }}" onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}" href="{{ $app_is_demo ? '#' : 'dashboard.admin.settings.openai' }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Anthropic') }}" onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}" href="{{ $app_is_demo ? '#' : 'dashboard.admin.settings.anthropic' }}" badge="{{ trans('Beta') }}">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Gemini') }}" onclick="{{ $app_is_demo ? 'return toastr.info(\'This feature is disabled in Demo version.\')' : '' }}" href="{{ $app_is_demo ? '#' : 'dashboard.admin.settings.gemini' }}" {{--                                    badge="{{ trans('Beta') }}" --}}>
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('StableDiffusion') }}" href="dashboard.admin.settings.stablediffusion">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        @php
                        try {
                        $files = File::files(resource_path('views/default/components/navbar/extapinavbars'));
                        } catch (\Throwable $th) {
                        $files = [];
                        }
                        @endphp
                        @foreach ($files as $file)
                        @php
                        $filenameWithoutExtension = substr($file->getFilename(), 0, -10);
                        @endphp
                        @include("components.navbar.extapinavbars.{$filenameWithoutExtension}")
                        @endforeach
                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Unsplash') }}" href="dashboard.admin.settings.unsplashapi">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Pexels') }}" href="dashboard.admin.settings.pexelsapi">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Pixabay') }}" href="dashboard.admin.settings.pixabayapi">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Serper') }}" href="dashboard.admin.settings.serperapi">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('TTS') }}" href="dashboard.admin.settings.tts">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>

                <x-navbar.item has-dropdown>
                    <x-navbar.link label="{{ __('Settings') }}" href="dashboard.admin.settings.general" icon="tabler-device-laptop" active-condition="{{ activeRouteBulk('dashboard.admin.settings.*', 'elseyyid.translations.home', 'elseyyid.translations.lang') }}" dropdown-trigger />
                    <x-navbar.dropdown.dropdown open="{{ activeRouteBulk('dashboard.admin.settings.*', 'elseyyid.translations.home', 'elseyyid.translations.lang') }}">

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('General') }}" href="dashboard.admin.settings.general">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Invoice') }}" href="dashboard.admin.settings.invoice">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        @php
                        try {
                        $files = File::files(resource_path('views/default/components/navbar/extsettingnavbars'));
                        } catch (\Throwable $th) {
                        $files = [];
                        }
                        @endphp
                        @foreach ($files as $file)
                        @php
                        $filenameWithoutExtension = substr($file->getFilename(), 0, -10);
                        @endphp
                        @include("components.navbar.extsettingnavbars.{$filenameWithoutExtension}")
                        @endforeach

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Affiliate') }}" href="dashboard.admin.settings.affiliate">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Thumbnail System') }}" href="dashboard.admin.settings.thumbnail">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('SMTP') }}" href="dashboard.admin.settings.smtp">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('GDPR') }}" href="dashboard.admin.settings.gdpr">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Privacy Policy and Terms') }}" href="dashboard.admin.settings.privacy">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Languages') }}" href="elseyyid.translations.home" active-condition="{{ activeRoute('elseyyid.translations.home') }} {{ activeRoute('elseyyid.translations.lang') }}" localize-href>
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                        <x-navbar.dropdown.item>
                            <x-navbar.dropdown.link label="{{ __('Storage') }}" href="dashboard.admin.settings.storage">
                            </x-navbar.dropdown.link>
                        </x-navbar.dropdown.item>

                    </x-navbar.dropdown.dropdown>
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('Site Health') }}" href="dashboard.admin.health.index" icon="tabler-activity-heartbeat">
                    </x-navbar.link>
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link label="{{ __('License') }}" href="dashboard.admin.license.index" icon="tabler-checklist">
                    </x-navbar.link>
                </x-navbar.item>

                <x-navbar.item>
                    <x-navbar.link class="nav-link--update" label="{{ __('Update') }}" href="dashboard.admin.update.index" icon="tabler-refresh">
                    </x-navbar.link>
                </x-navbar.item>

                @if ($app_is_not_demo)
                <x-navbar.item>
                    <x-navbar.link label="{{ __('Premium Support') }}" href="#" icon="tabler-diamond" trigger-type="modal">
                        <x-slot:modal>
                            @includeIf('premium-support.index')
                        </x-slot:modal>
                    </x-navbar.link>
                </x-navbar.item>
                @endif
                @endif

                <x-navbar.item>
                    <x-navbar.divider />
                </x-navbar.item>

                <x-navbar.item class="group-[&.navbar-shrinked]/body:hidden">
                    <x-navbar.label>
                        {{ __('Credits') }}
                    </x-navbar.label>
                </x-navbar.item>

                <x-navbar.item class="pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt group-[&.navbar-shrinked]/body:hidden">
                    <x-remaining-credit class="text-2xs" />
                </x-navbar.item>

                @if ($setting->feature_affilates)
                <x-navbar.item class="group-[&.navbar-shrinked]/body:hidden">
                    <x-navbar.divider />
                </x-navbar.item>

                <x-navbar.item class="group-[&.navbar-shrinked]/body:hidden">
                    <x-navbar.label>
                        {{ __('Affiliation') }}
                    </x-navbar.label>
                </x-navbar.item>

                <x-navbar.item class="pb-navbar-link-pb pe-navbar-link-pe ps-navbar-link-ps pt-navbar-link-pt group-[&.navbar-shrinked]/body:hidden">
                    <div class="lqd-navbar-affiliation inline-block w-full rounded-xl border border-navbar-divider px-8 py-4 text-center text-2xs leading-tight transition-border">
                        <p class="m-0 mb-2 text-[20px] not-italic">🎁</p>
                        <p class="mb-4">{{ __('Invite your friend and get') }}
                            {{ $setting->affiliate_commission_percentage }}%
                            {{ __('on all their purchases.') }}
                        </p>
                        <x-button class="text-3xs" href="{{ route('dashboard.user.affiliates.index') }}" variant="ghost-shadow">
                            {{ __('Invite') }}
                        </x-button>
                    </div>
                </x-navbar.item>
                @endif
            </ul>
        </nav>
    </div>
</aside>