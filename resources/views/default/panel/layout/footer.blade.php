<footer class="lqd-page-footer mt-auto py-8">
    <div class="container">
        <div class="flex items-center gap-4">
            <div class="grow basis-full md:basis-0 lg:ms-auto">
                <p>{{ __('Version') }}: {{ format_double($setting->script_version) }}</p>
                @if(request()->getHost() == 'magicai.test' || request()->getHost() == 'stagingmagicai.liquid-themes.com')
                    {{ microtime(true) - LARAVEL_START }}
                @endif
            </div>
            <div class="grow basis-full md:basis-0 md:text-end">
                <p>
                    {{ __('Copyright') }} &copy; <?php echo date('Y'); ?>
                    <a href="{{ route('index') }}">
                        {{ $setting->site_name }}
                    </a>.
                    {{ __('All rights reserved.') }}
                </p>
            </div>
        </div>
    </div>
</footer>
