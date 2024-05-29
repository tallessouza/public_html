@extends('panel.layout.settings')
@section('title', __('Frontend Section Settings'))
@section('titlebar_actions', '')

@section('settings')
    <form
        id="settings_form"
        onsubmit="return frontendSectionSettingsSave();"
        enctype="multipart/form-data"
    >
        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Features Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Features Section Active') }}</label>
                    <select
                        class="form-select"
                        id="features_active"
                        name="features_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->features_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->features_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Features Title') }}</label>
                    <input
                        class="form-control"
                        id="features_title"
                        type="text"
                        name="features_title"
                        value="{{ $fSectSettings->features_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Features Description') }}</label>
                    <textarea
                        class="form-control"
                        id="features_description"
                        name="features_description"
                    >{{ $fSectSettings->features_description }}</textarea>
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Generators Section') }}</h3>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Generators Active') }}</label>
                    <select
                        class="form-select"
                        id="generators_active"
                        name="generators_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->generators_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->generators_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <h3 class="mb-[25px] text-[20px]">{{ __('For Who Section') }}</h3>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('For Who Section Active') }}</label>
                    <select
                        class="form-select"
                        id="who_is_for_active"
                        name="who_is_for_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->who_is_for_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->who_is_for_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Custom Templates Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Custom Templates Active') }}</label>
                    <select
                        class="form-select"
                        id="custom_templates_active"
                        name="custom_templates_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->custom_templates_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->custom_templates_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Custom Templates Subtitle One') }}</label>
                    <input
                        class="form-control"
                        id="custom_templates_subtitle_one"
                        type="text"
                        name="custom_templates_subtitle_one"
                        value="{{ $fSectSettings->custom_templates_subtitle_one }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Custom Templates Subtitle Two') }}</label>
                    <input
                        class="form-control"
                        id="custom_templates_subtitle_two"
                        type="text"
                        name="custom_templates_subtitle_two"
                        value="{{ $fSectSettings->custom_templates_subtitle_two }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Custom Templates Title') }}</label>
                    <input
                        class="form-control"
                        id="custom_templates_title"
                        type="text"
                        name="custom_templates_title"
                        value="{{ $fSectSettings->custom_templates_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Custom Templates Description') }}</label>
                    <textarea
                        class="form-control"
                        id="custom_templates_description"
                        name="custom_templates_description"
                    >{{ $fSectSettings->custom_templates_description }}</textarea>
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Tools Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Tools Active') }}</label>
                    <select
                        class="form-select"
                        id="tools_active"
                        name="tools_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->tools_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->tools_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Tools Title') }}</label>
                    <input
                        class="form-control"
                        id="tools_title"
                        type="text"
                        name="tools_title"
                        value="{{ $fSectSettings->tools_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Tools Description') }}</label>
                    <textarea
                        class="form-control"
                        id="tools_description"
                        name="tools_description"
                    >{{ $fSectSettings->tools_description }}</textarea>
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('How It Works Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('How It Works Active') }}</label>
                    <select
                        class="form-select"
                        id="how_it_works_active"
                        name="how_it_works_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->how_it_works_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->how_it_works_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('How It Works Title') }}</label>
                    <input
                        class="form-control"
                        id="how_it_works_title"
                        type="text"
                        name="how_it_works_title"
                        value="{{ $fSectSettings->how_it_works_title }}"
                    >
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Testimonials Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Testimonials Active') }}</label>
                    <select
                        class="form-select"
                        id="testimonials_active"
                        name="testimonials_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->testimonials_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->testimonials_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Testimonials Title') }}</label>
                    <input
                        class="form-control"
                        id="testimonials_title"
                        type="text"
                        name="testimonials_title"
                        value="{{ $fSectSettings->testimonials_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Testimonials Subtitle One') }}</label>
                    <input
                        class="form-control"
                        id="testimonials_subtitle_one"
                        type="text"
                        name="testimonials_subtitle_one"
                        value="{{ $fSectSettings->testimonials_subtitle_one }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Testimonials Subtitle Two') }}</label>
                    <input
                        class="form-control"
                        id="testimonials_subtitle_two"
                        type="text"
                        name="testimonials_subtitle_two"
                        value="{{ $fSectSettings->testimonials_subtitle_two }}"
                    >
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Pricing Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Pricing Active') }}</label>
                    <select
                        class="form-select"
                        id="pricing_active"
                        name="pricing_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->pricing_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->pricing_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Pricing Title') }}</label>
                    <input
                        class="form-control"
                        id="pricing_title"
                        type="text"
                        name="pricing_title"
                        value="{{ $fSectSettings->pricing_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Pricing Description') }}</label>
                    <textarea
                        class="form-control"
                        id="pricing_description"
                        name="pricing_description"
                    >{{ $fSectSettings->pricing_description }}</textarea>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Pricing Save Percent') }}</label>
                    <input
                        class="form-control"
                        id="pricing_save_percent"
                        type="text"
                        name="pricing_save_percent"
                        value="{{ $fSectSettings->pricing_save_percent }}"
                    >
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('FAQ Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('FAQ Active') }}</label>
                    <select
                        class="form-select"
                        id="faq_active"
                        name="faq_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->faq_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->faq_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('FAQ Title') }}</label>
                    <input
                        class="form-control"
                        id="faq_title"
                        type="text"
                        name="faq_title"
                        value="{{ $fSectSettings->faq_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('FAQ Subtitle') }}</label>
                    <input
                        class="form-control"
                        id="faq_subtitle"
                        type="text"
                        name="faq_subtitle"
                        value="{{ $fSectSettings->faq_subtitle }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('FAQ Text One') }}</label>
                    <input
                        class="form-control"
                        id="faq_text_one"
                        type="text"
                        name="faq_text_one"
                        value="{{ $fSectSettings->faq_text_one }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('FAQ Text Two') }}</label>
                    <input
                        class="form-control"
                        id="faq_text_two"
                        type="text"
                        name="faq_text_two"
                        value="{{ $fSectSettings->faq_text_two }}"
                    >
                </div>
            </div>

        </div>

        <div class="row">
            <h3 class="mb-[25px] text-[20px]">{{ __('Blog Section') }}</h3>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Active') }}</label>
                    <select
                        class="form-select"
                        id="blog_active"
                        name="blog_active"
                    >
                        <option
                            value="1"
                            {{ $fSectSettings->blog_active == 1 ? 'selected' : '' }}
                        >
                            {{ __('Active') }}</option>
                        <option
                            value="0"
                            {{ $fSectSettings->blog_active == 0 ? 'selected' : '' }}
                        >
                            {{ __('Passive') }}</option>
                    </select>
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Title') }}</label>
                    <input
                        class="form-control"
                        id="blog_title"
                        type="text"
                        name="blog_title"
                        value="{{ $fSectSettings->blog_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Subtitle') }}</label>
                    <input
                        class="form-control"
                        id="blog_subtitle"
                        type="text"
                        name="blog_subtitle"
                        value="{{ $fSectSettings->blog_subtitle }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Posts Per Page') }}</label>
                    <input
                        class="form-control"
                        id="blog_posts_per_page"
                        type="number"
                        name="blog_posts_per_page"
                        min="1"
                        max="6"
                        value="{{ $fSectSettings->blog_posts_per_page }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Button Text') }}</label>
                    <input
                        class="form-control"
                        id="blog_button_text"
                        type="text"
                        name="blog_button_text"
                        value="{{ $fSectSettings->blog_button_text }}"
                    >
                </div>
            </div>

            <h5 class="mb-[25px] text-[16px]">{{ __('Blog Archive Options') }}</h5>
            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Title') }}</label>
                    <input
                        class="form-control"
                        id="blog_a_title"
                        type="text"
                        name="blog_a_title"
                        value="{{ $fSectSettings->blog_a_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Subtitle') }}</label>
                    <input
                        class="form-control"
                        id="blog_a_subtitle"
                        type="text"
                        name="blog_a_subtitle"
                        value="{{ $fSectSettings->blog_a_title }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Description') }}</label>
                    <input
                        class="form-control"
                        id="blog_a_description"
                        type="text"
                        name="blog_a_description"
                        value="{{ $fSectSettings->blog_a_description }}"
                    >
                </div>
            </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="form-label">{{ __('Blog Posts Per Page') }}</label>
                    <input
                        class="form-control"
                        id="blog_a_posts_per_page"
                        type="number"
                        name="blog_a_posts_per_page"
                        min="1"
                        max="12"
                        value="{{ $fSectSettings->blog_a_posts_per_page }}"
                    >
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
@endpush
