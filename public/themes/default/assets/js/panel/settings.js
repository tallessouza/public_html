//admin.openai.custom.form
$(document).ready(function () {
    "use strict";
    if (!$.fn.select2) return;
    $('.select2').select2({
        tags: true
    });
});

function frontendSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('site_name', $("#site_name").val());
    formData.append('register_active', $("#register_active").val());
    formData.append('site_url', $("#site_url").val());
    formData.append('site_email', $("#site_email").val());
    formData.append('frontend_pricing_section', $("#frontend_pricing_section").val());
    formData.append('frontend_custom_templates_section', $("#frontend_custom_templates_section").val());
    formData.append('frontend_business_partners_section', $("#frontend_business_partners_section").val());
    formData.append('frontend_additional_url', $("#frontend_additional_url").val());
    formData.append('frontend_custom_css', $("#frontend_custom_css").val());
    formData.append('frontend_custom_js', $("#frontend_custom_js").val());
    formData.append('frontend_footer_facebook', $("#frontend_footer_facebook").val());
    formData.append('frontend_footer_twitter', $("#frontend_footer_twitter").val());
    formData.append('frontend_footer_instagram', $("#frontend_footer_instagram").val());

    formData.append('preheader_active', $("#preheader_active").val());
    formData.append('header_title', $("#header_title").val());
    formData.append('header_text', $("#header_text").val());
    formData.append('sign_in', $("#sign_in").val());
    formData.append('join_hub', $("#join_hub").val());

    formData.append('hero_subtitle', $("#hero_subtitle").val());
    formData.append('hero_title', $("#hero_title").val());
    formData.append('hero_title_text_rotator', $("#hero_title_text_rotator").val());
    formData.append('hero_description', $("#hero_description").val());
    formData.append('hero_scroll_text', $("#hero_scroll_text").val());
    formData.append('hero_button', $("#hero_button").val());
    formData.append('hero_button_url', $("#hero_button_url").val());
    formData.append('hero_button_type', $("#hero_button_type").val());

    formData.append('floating_button_small_text', $("#floating_button_small_text").val());
    formData.append('floating_button_bold_text', $("#floating_button_bold_text").val());
    formData.append('floating_button_link', $("#floating_button_link").val());
    formData.append('floating_button_active', $("#floating_button_active").val());


    if (frontend_code_before_head) {
        formData.append('frontend_code_before_head', frontend_code_before_head.getValue());
    }
    if (frontend_code_before_body) {
        formData.append('frontend_code_before_body', frontend_code_before_body.getValue());
    }

    formData.append('footer_header', $("#footer_header").val());
    formData.append('footer_text_small', $("#footer_text_small").val());
    formData.append('footer_text', $("#footer_text").val());
    formData.append('footer_button_text', $("#footer_button_text").val());
    formData.append('footer_button_url', $("#footer_button_url").val());
    formData.append('footer_copyright', $("#footer_copyright").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/settings-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function frontendSectionSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('features_active', $("#features_active").val());
    formData.append('features_title', $("#features_title").val());
    formData.append('features_description', $("#features_description").val());


    formData.append('generators_active', $("#generators_active").val());

    formData.append('who_is_for_active', $("#who_is_for_active").val());

    formData.append('custom_templates_active', $("#custom_templates_active").val());
    formData.append('custom_templates_subtitle_one', $("#custom_templates_subtitle_one").val());
    formData.append('custom_templates_subtitle_two', $("#custom_templates_subtitle_two").val());
    formData.append('custom_templates_title', $("#custom_templates_title").val());
    formData.append('custom_templates_description', $("#custom_templates_description").val());


    formData.append('tools_active', $("#tools_active").val());
    formData.append('tools_title', $("#tools_title").val());
    formData.append('tools_description', $("#tools_description").val());

    formData.append('how_it_works_active', $("#how_it_works_active").val());
    formData.append('how_it_works_title', $("#how_it_works_title").val());

    formData.append('testimonials_active', $("#testimonials_active").val());
    formData.append('testimonials_title', $("#testimonials_title").val());
    formData.append('testimonials_subtitle_one', $("#testimonials_subtitle_one").val());
    formData.append('testimonials_subtitle_two', $("#testimonials_subtitle_two").val());

    formData.append('pricing_active', $("#pricing_active").val());
    formData.append('pricing_title', $("#pricing_title").val());
    formData.append('pricing_description', $("#pricing_description").val());
    formData.append('pricing_save_percent', $("#pricing_save_percent").val());


    formData.append('faq_active', $("#faq_active").val());
    formData.append('faq_title', $("#faq_title").val());
    formData.append('faq_subtitle', $("#faq_subtitle").val());
    formData.append('faq_text_one', $("#faq_text_one").val());
    formData.append('faq_text_two', $("#faq_text_two").val());


    formData.append('blog_active', $("#blog_active").val());
    formData.append('blog_title', $("#blog_title").val());
    formData.append('blog_subtitle', $("#blog_subtitle").val());
    formData.append('blog_posts_per_page', $("#blog_posts_per_page").val());
    formData.append('blog_button_text', $("#blog_button_text").val());
    formData.append('blog_a_title', $("#blog_a_title").val());
    formData.append('blog_a_subtitle', $("#blog_a_subtitle").val());
    formData.append('blog_a_description', $("#blog_a_description").val());
    formData.append('blog_a_posts_per_page', $("#blog_a_posts_per_page").val());


    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/section-settings-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function menuSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    var menuData = [];
    $("#menu-items .accordion-content").each(function () {
        var title = $(this).find(".menu-title").val();
        var url = $(this).find(".menu-url").val();
        var target = $(this).find(".menu-target").prop("checked");

        var data = {
            title: title,
            url: url,
            target: target
        };

        menuData.push(data);
    });

    var jsonData = JSON.stringify(menuData);
    formData.append('menu_options', jsonData);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/menu-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function affiliateSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('affiliate_minimum_withdrawal', $("#affiliate_minimum_withdrawal").val());
    formData.append('affiliate_commission_percentage', $("#affiliate_commission_percentage").val());
    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/affiliate-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}


function generalSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('site_name', $("#site_name").val());
    formData.append('site_url', $("#site_url").val());
    formData.append('site_email', $("#site_email").val());
    formData.append('register_active', $("#register_active").val());
    formData.append('free_plan', $("#free_plan").val());
    formData.append('default_country', $("#default_country").val());
    formData.append('default_currency', $("#default_currency").val());
    formData.append('default_ai_engine', $("#default_ai_engine").val());
	formData.append('default_aw_image_engine', $("#default_aw_image_engine").val());
    formData.append('openai_default_stream_server', $("#openai_default_stream_server").is(":checked") ? 'frontend' : 'backend');
    formData.append('login_without_confirmation', $("#login_without_confirmation").is(":checked") ? 0 : 1);
    formData.append('daily_limit_count', $("#daily_limit_count").val());
    formData.append('daily_voice_limit_count', $("#daily_voice_limit_count").val());

    if ($('#limit').prop('checked')) {
        formData.append('limit', 1);
    } else {
        formData.append('limit', 0);
    }

    if ($('#voice_limit').prop('checked')) {
        formData.append('voice_limit', 1);
    } else {
        formData.append('voice_limit', 0);
    }

    formData.append('facebook_active', $("#facebook_active").is(":checked") ? 1 : 0);
    formData.append('google_active', $("#google_active").is(":checked") ? 1 : 0);
    formData.append('github_active', $("#github_active").is(":checked") ? 1 : 0);

    formData.append('metaTitleLocal', $("#metaTitleLocal").val());
    formData.append('metaDescLocal', $("#metaDescLocal").val());

    if ($('#logo').val() != 'undefined') {
        formData.append('logo', $('#logo').prop('files')[0]);
    }
    if ($('#logo_dark').val() != 'undefined') {
        formData.append('logo_dark', $('#logo_dark').prop('files')[0]);
    }
    if ($('#logo_sticky').val() != 'undefined') {
        formData.append('logo_sticky', $('#logo_sticky').prop('files')[0]);
    }
    if ($('#logo_dashboard').val() != 'undefined') {
        formData.append('logo_dashboard', $('#logo_dashboard').prop('files')[0]);
    }
    if ($('#logo_dashboard_dark').val() != 'undefined') {
        formData.append('logo_dashboard_dark', $('#logo_dashboard_dark').prop('files')[0]);
    }
    if ($('#logo_collapsed').val() != 'undefined') {
        formData.append('logo_collapsed', $('#logo_collapsed').prop('files')[0]);
    }
    if ($('#logo_collapsed_dark').val() != 'undefined') {
        formData.append('logo_collapsed_dark', $('#logo_collapsed_dark').prop('files')[0]);
    }
    if ($('#logo_2x').val() != 'undefined') {
        formData.append('logo_2x', $('#logo_2x').prop('files')[0]);
    }
    if ($('#logo_dark_2x').val() != 'undefined') {
        formData.append('logo_dark_2x', $('#logo_dark_2x').prop('files')[0]);
    }
    if ($('#logo_sticky_2x').val() != 'undefined') {
        formData.append('logo_sticky_2x', $('#logo_sticky_2x').prop('files')[0]);
    }
    if ($('#logo_dashboard_2x').val() != 'undefined') {
        formData.append('logo_dashboard_2x', $('#logo_dashboard_2x').prop('files')[0]);
    }
    if ($('#logo_dashboard_dark_2x').val() != 'undefined') {
        formData.append('logo_dashboard_dark_2x', $('#logo_dashboard_dark_2x').prop('files')[0]);
    }
    if ($('#logo_collapsed_2x').val() != 'undefined') {
        formData.append('logo_collapsed_2x', $('#logo_collapsed_2x').prop('files')[0]);
    }
    if ($('#logo_collapsed_dark_2x').val() != 'undefined') {
        formData.append('logo_collapsed_dark_2x', $('#logo_collapsed_dark_2x').prop('files')[0]);
    }
    if ($('#favicon').val() != 'undefined') {
        formData.append('favicon', $('#favicon').prop('files')[0]);
    }

    formData.append('google_analytics_code', $("#google_analytics_code").val());
    formData.append('meta_title', $("#meta_title").val());
    formData.append('meta_description', $("#meta_description").val());
    formData.append('meta_keywords', $("#meta_keywords").val());

    if (dashboard_code_before_head) {
        formData.append('dashboard_code_before_head', dashboard_code_before_head.getValue());
    }
    if (dashboard_code_before_head) {
        formData.append('dashboard_code_before_body', dashboard_code_before_body.getValue());
    }

    formData.append('feature_ai_writer', $("#feature_ai_writer").is(":checked") ? 1 : 0);
    formData.append('feature_ai_image', $("#feature_ai_image").is(":checked") ? 1 : 0);
    formData.append('feature_ai_video', $("#feature_ai_video").is(":checked") ? 1 : 0);
    formData.append('feature_ai_chat', $("#feature_ai_chat").is(":checked") ? 1 : 0);
    formData.append('feature_ai_code', $("#feature_ai_code").is(":checked") ? 1 : 0);
    formData.append('feature_ai_speech_to_text', $("#feature_ai_speech_to_text").is(":checked") ? 1 : 0);
    formData.append('feature_ai_voiceover', $("#feature_ai_voiceover").is(":checked") ? 1 : 0);
    formData.append('feature_affilates', $("#feature_affilates").is(":checked") ? 1 : 0);
    formData.append('user_api_option', $("#user_api_option").is(":checked") ? 1 : 0);
    formData.append('feature_ai_article_wizard', $("#feature_ai_article_wizard").is(":checked") ? 1 : 0);
    formData.append('feature_ai_vision', $("#feature_ai_vision").is(":checked") ? 1 : 0);
    formData.append('feature_ai_pdf', $("#feature_ai_pdf").is(":checked") ? 1 : 0);
    formData.append('feature_ai_youtube', $("#feature_ai_youtube").is(":checked") ? 1 : 0);
    formData.append('feature_ai_rss', $("#feature_ai_rss").is(":checked") ? 1 : 0);
    formData.append('team_functionality', $("#team_functionality").is(":checked") ? 1 : 0);
    formData.append('feature_ai_advanced_editor', $("#feature_ai_advanced_editor").is(":checked") ? 1 : 0);
    formData.append('mobile_payment_active', $("#mobile_payment_active").is(":checked") ? 1 : 0);
    formData.append('feature_ai_chat_image', $("#feature_ai_chat_image").is(":checked") ? 1 : 0);
    formData.append('feature_ai_rewriter', $("#feature_ai_rewriter").is(":checked") ? 1 : 0);
    formData.append('feature_ai_voice_clone', $("#feature_ai_voice_clone").is(":checked") ? 1 : 0)
    formData.append('user_prompt_library', $("#user_prompt_library").is(":checked") ? 1 : 0)
    formData.append('user_ai_image_prompt_library', $("#user_ai_image_prompt_library").is(":checked") ? 1 : 0)
    formData.append('chat_setting_for_customer', $("#chat_setting_for_customer").is(":checked") ? 1 : 0)
    formData.append('photo_studio', $("#photo_studio").is(":checked") ? 1 : 0)


    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/general-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved_redirecting ||'Settings saved succesfully. Redirecting...')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
            setTimeout(function () {
                location.href = '/dashboard/admin/settings/general'
            }, 1000);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}


function invoiceSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('invoice_name', $("#invoice_name").val());
    formData.append('invoice_website', $("#invoice_website").val());
    formData.append('invoice_address', $("#invoice_address").val());
    formData.append('invoice_city', $("#invoice_city").val());
    formData.append('invoice_state', $("#invoice_state").val());
    formData.append('invoice_postal', $("#invoice_postal").val());
    formData.append('invoice_country', $("#invoice_country").val());
    formData.append('invoice_phone', $("#invoice_phone").val());
    formData.append('invoice_vat', $("#invoice_vat").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/invoice-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function stripeSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('default_currency', $("#default_currency").val());
    formData.append('stripe_key', $("#stripe_key").val());
    formData.append('stripe_secret', $("#stripe_secret").val());
    formData.append('stripe_base_url', $("#stripe_base_url").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/payment-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}


function openaiSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('openai_api_secret', $("#openai_api_secret").val());
    formData.append('dalle_default_model', $("#dalle_default_model").val());
    formData.append('openai_default_model', $("#openai_default_model").val());
    formData.append('openai_default_language', $("#openai_default_language").val());
    formData.append('openai_default_tone_of_voice', $("#openai_default_tone_of_voice").val());
    formData.append('openai_default_creativity', $("#openai_default_creativity").val());
    formData.append('openai_max_input_length', $("#openai_max_input_length").val());
    formData.append('openai_max_output_length', $("#openai_max_output_length").val());
    formData.append('openai_default_stream_server', $("#openai_default_stream_server").val());
	formData.append('hide_creativity_option', $("#hide_creativity_option").is(":checked") ? 1 : 0);
	formData.append('hide_tone_of_voice_option', $("#hide_tone_of_voice_option").is(":checked") ? 1 : 0);
	formData.append('hide_output_length_option', $("#hide_output_length_option").is(":checked") ? 1 : 0);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/openai-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function anthropicSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('anthropic_api_secret', $("#anthropic_api_secret").val());
    formData.append('anthropic_default_model', $("#anthropic_default_model").val());
    formData.append('anthropic_max_input_length', $("#anthropic_max_input_length").val());
    formData.append('anthropic_max_output_length', $("#anthropic_max_output_length").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/anthropic",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function geminiSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('gemini_api_secret', $("#gemini_api_secret").val());
    formData.append('gemini_default_model', $("#gemini_default_model").val());
	formData.append('gemini_max_input_length', $("#gemini_max_input_length").val());
    formData.append('gemini_max_output_length', $("#gemini_max_output_length").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/gemini",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function stablediffusionSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();stable_hidden
    formData.append('stable_diffusion_api_key', $("#stable_diffusion_api_key").val());
    formData.append('stable_hidden', $("#stable_hidden").is(":checked") ? 1 : 0);
    formData.append('stablediffusion_default_language', $("#stablediffusion_default_language").val());
    formData.append('stablediffusion_default_model', $("#stablediffusion_default_model").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/stablediffusion-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function unsplashSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('unsplash_api_key', $("#unsplash_api_key").val());
    // formData.append( 'stablediffusion_default_language', $( "#stablediffusion_default_language" ).val() );
    // formData.append( 'stablediffusion_default_model', $( "#stablediffusion_default_model" ).val() );

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/unsplashapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}
function pexelsSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('pexels_api_key', $("#pexels_api_key").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/pexelsapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}
function pixabaySettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('pixabay_api_key', $("#pixabay_api_key").val());
    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/pixabayapi-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function serperSettingsSave() {
	"use strict";

	document.getElementById( "settings_button" ).disabled = true;
	document.getElementById( "settings_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	formData.append( 'serper_api_key', $( "#serper_api_key" ).val() );

    if ($( "#serper_seo_aw_sq" )) {
        formData.append( 'serper_seo_aw_sq', $( "#serper_seo_aw_sq" ).is( ":checked" ) ? 1 : 0 );
    }

    if ($( "#serper_seo_aw_keyword" )) {
        formData.append( 'serper_seo_aw_keyword', $( "#serper_seo_aw_keyword" ).is( ":checked" ) ? 1 : 0 );
    }

    if ($( "#serper_seo_blog_title_desc" )) {
        formData.append( 'serper_seo_blog_title_desc', $( "#serper_seo_blog_title_desc" ).is( ":checked" ) ? 1 : 0 );
    }

    if ($( "#serper_seo_site_meta" )) {
        formData.append( 'serper_seo_site_meta', $( "#serper_seo_site_meta" ).is( ":checked" ) ? 1 : 0 );
    }

	$.ajax( {
	    type: "post",
	 	url: "/dashboard/admin/settings/serperapi-save",
	 	data: formData,
	 	contentType: false,
	 	processData: false,
	 	success: function ( data ) {
	 		toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
	 		document.getElementById( "settings_button" ).disabled = false;
	 		document.getElementById( "settings_button" ).innerHTML = "Save";
	 	},
	 	error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "settings_button" ).disabled = false;
			document.getElementById( "settings_button" ).innerHTML = "Save";
		}
	} );
	return false;
}
function clipdropSettingsSave() {
	"use strict";

	document.getElementById( "settings_button" ).disabled = true;
	document.getElementById( "settings_button" ).innerHTML = magicai_localize.please_wait;

	var formData = new FormData();
	formData.append( 'clipdrop_api_key', $( "#clipdrop_api_key" ).val() );
	// formData.append( 'stablediffusion_default_language', $( "#stablediffusion_default_language" ).val() );
	// formData.append( 'stablediffusion_default_model', $( "#stablediffusion_default_model" ).val() );

	$.ajax( {
	    type: "post",
	 	url: "/dashboard/admin/settings/clipdrop-save",
	 	data: formData,
	 	contentType: false,
	 	processData: false,
	 	success: function ( data ) {
	 		toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
	 		document.getElementById( "settings_button" ).disabled = false;
	 		document.getElementById( "settings_button" ).innerHTML = "Save";
	 	},
	 	error: function ( data ) {
			var err = data.responseJSON.errors;
			$.each( err, function ( index, value ) {
				toastr.error( value );
			} );
			document.getElementById( "settings_button" ).disabled = false;
			document.getElementById( "settings_button" ).innerHTML = "Save";
		}
	} );
	return false;
}

function ttsSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    if ($("#feature_tts_elevenlabs").is(":checked") && ($("#feature_tts_google").is(":checked") || $("#feature_tts_openai").is(":checked"))) {
        toastr.warning("Cannot use Elevenlabs TTS with Google TTS or OpenAI TTS");
        document.getElementById("settings_button").disabled = false;
        document.getElementById("settings_button").innerHTML = "Save";
        return false;
    }

    var formData = new FormData();
    formData.append('feature_tts_google', $("#feature_tts_google").is(":checked") ? 1 : 0);
    formData.append('feature_tts_openai', $("#feature_tts_openai").is(":checked") ? 1 : 0);
    formData.append('feature_tts_elevenlabs', $("#feature_tts_elevenlabs").is(":checked") ? 1 : 0);
    formData.append('elevenlabs_api_key', $("#elevenlabs_api_key").val());

    if ($("#azure_api_key")) {
        formData.append('feature_tts_azure', $("#feature_tts_azure").is(":checked") ? 1 : 0);
        formData.append('azure_api_key', $("#azure_api_key").val());
        formData.append('azure_region', $("#azure_region").val());
    }

    formData.append('gcs_file', $("#gcs_file").val());
    formData.append('gcs_name', $("#gcs_name").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/tts-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function apiKeysSettingsSave() {
    'use strict';
    document.getElementById('settings_button').disabled = true;
    document.getElementById('settings_button').innerHTML =
	magicai_localize.please_wait;
    var formData = new FormData();
    formData.append('api_keys', $('#api_keys').val());
    formData.append('anthropic_api_keys', $('#anthropic_api_keys').val());
    formData.append('gemini_api_keys', $('#anthropic_api_keys').val());
    $.ajax({
        type: 'post',
        url: '/dashboard/user/api-keys/update',
        data: formData,
        contentType: false,
        processData: false,
        success: function(data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully');
            document.getElementById('settings_button').disabled = false;
            document.getElementById('settings_button').innerHTML = 'Save';
        },
        error: function(data) {
            var err = data.responseJSON.errors;
            $.each(err, function(index, value) {
                toastr.error(value);
            });
            document.getElementById('settings_button').disabled = false;
            document.getElementById('settings_button').innerHTML = 'Save';
        }
});
    return false;
}
function smtpSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('smtp_host', $("#smtp_host").val());
    formData.append('smtp_port', $("#smtp_port").val());
    formData.append('smtp_username', $("#smtp_username").val());
    formData.append('smtp_password', $("#smtp_password").val());
    formData.append('smtp_email', $("#smtp_email").val());
    formData.append('smtp_sender_name', $("#smtp_sender_name").val());
    formData.append('smtp_encryption', $("#smtp_encryption").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/smtp-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function gdprSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('gdpr_status', $("#gdpr_status").is(":checked") ? 1 : 0);
    formData.append('gdpr_button', $("#gdpr_button").val());
    formData.append('gdpr_content', $("#gdpr_content").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/gdpr-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function privacySettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('privacy_enable', $("#privacy_enable").is(":checked") ? 1 : 0);
    formData.append('privacy_enable_login', $("#privacy_enable_login").is(":checked") ? 1 : 0);
    formData.append('privacy_content', tinymce.get("privacy_content").getContent());
    formData.append('terms_content', tinymce.get("terms_content").getContent());
    formData.append('termsLocal', $("#termsLocal").val());
    formData.append('privacyLocal', $("#privacyLocal").val());


    console.log(formData);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/privacy-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}

function faqCreateOrUpdate(faq_id) {
    "use strict";

    document.getElementById("faq_button").disabled = true;
    document.getElementById("faq_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('question', $("#question").val());
    formData.append('answer', $("#answer").val());
    formData.append('faq_id', faq_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/faq/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.faq_saved ||'Faq saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/faq"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("faq_button").disabled = false;
            document.getElementById("faq_button").innerHTML = "Save";
        }
    });
    return false;
}

function toolsCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('title', $("#title").val());
    formData.append('description', $("#description").val());
    if ($('#image').val() != 'undefined') {
        formData.append('image', $('#image').prop('files')[0]);
    }
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/tools/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/tools"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}

function futureCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('title', $("#title").val());
    formData.append('description', $("#description").val());
    formData.append('image', $("#image").val());
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/future/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/future"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}


function whoisCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('title', $("#title").val());
    formData.append('color', $("#color").val());
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/whois/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/whois"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}

function generatorlistCreateOrUpdate(item_id) {
    "use strict";

    document.getElementById("item_button").disabled = true;
    document.getElementById("item_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('menu_title', $("#menu_title").val());
    formData.append('subtitle_one', $("#subtitle_one").val());
    formData.append('subtitle_two', $("#subtitle_two").val());
    formData.append('title', $("#title").val());
    formData.append('text', $("#text").val());
    formData.append('image_title', $("#image_title").val());
    formData.append('image_subtitle', $("#image_subtitle").val());
    formData.append('color', $("#color").val());
    if ($('#image').val() != 'undefined') {
        formData.append('image', $('#image').prop('files')[0]);
    }
    formData.append('item_id', item_id);

    $.ajax({
        type: "post",
        url: "/dashboard/admin/frontend/generatorlist/action/save",
        data: formData,
        contentType: false,
        processData: false,
        success: function () {
            toastr.success(magicai_localize?.item_saved ||'Item saved succesfully. Redirecting')
            setTimeout(function () {
                location.href = "/dashboard/admin/frontend/generatorlist"
            }, 750);
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("item_button").disabled = false;
            document.getElementById("item_button").innerHTML = "Save";
        }
    });
    return false;
}

function imageStorageSettingsSave() {
    "use strict";

    document.getElementById("settings_button").disabled = true;
    document.getElementById("settings_button").innerHTML = magicai_localize.please_wait;

    var formData = new FormData();
    formData.append('ai_image_storage', $("#ai_image_storage").val());

    $.ajax({
        type: "post",
        url: "/dashboard/admin/settings/storage-save",
        data: formData,
        contentType: false,
        processData: false,
        success: function (data) {
            toastr.success(magicai_localize?.settings_saved ||'Settings saved succesfully')
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        },
        error: function (data) {
            var err = data.responseJSON.errors;
            $.each(err, function (index, value) {
                toastr.error(value);
            });
            document.getElementById("settings_button").disabled = false;
            document.getElementById("settings_button").innerHTML = "Save";
        }
    });
    return false;
}
