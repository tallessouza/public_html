<?php

namespace App\Http\Controllers;

use App\Jobs\SendConfirmationEmail;
use App\Models\Blog;
use App\Models\Clients;
use App\Models\CustomSettings;
use App\Models\Faq;
use App\Models\FrontendForWho;
use App\Models\FrontendFuture;
use App\Models\FrontendGenerators;
use App\Models\FrontendTools;
use App\Models\Hero;
use App\Models\HowitWorks;
use App\Models\OpenAIGenerator;
use App\Models\OpenaiGeneratorFilter;
use App\Models\PaymentPlans;
use App\Models\Setting;
use App\Models\SettingTwo;
use App\Models\FrontendSectionsStatusses;
use App\Models\Testimonials;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;



class IndexController extends Controller
{
    public function index()
    {
        $filters = OpenaiGeneratorFilter::all();
        $templates = OpenAIGenerator::all();
        $plansSubscription = PaymentPlans::where('type', 'subscription')->get()->sortBy('price');
        $plansSubscriptionMonthly = PaymentPlans::where([['type', '=', 'subscription'], ['frequency', '=', 'monthly']])->get()->sortBy('price');
		$plansSubscriptionLifetime = PaymentPlans::where('type', '=', 'subscription')
		->where(function ($query) {
			$query->where('frequency', '=', 'lifetime_yearly')
				->orWhere('frequency', '=', 'lifetime_monthly');
		})
		->get()->sortBy('price');
        $plansSubscriptionAnnual = PaymentPlans::where([['type', '=', 'subscription'], ['frequency', '=', 'yearly']])->get()->sortBy('price');
        $plansPrepaid = PaymentPlans::where('type', 'prepaid')->get()->sortBy('price');
        $faq = Faq::all();
        $tools = FrontendTools::all();
        $futures = FrontendFuture::all();
        $testimonials = Testimonials::all();
        $howitWorks = HowitWorks::orderBy('order', 'ASC')->limit(3)->get();
        $howitWorksDefaults = self::howitWorksDefaults();
        $clients = Clients::all();
        $who_is_for = FrontendForWho::all();
        $generatorsList = FrontendGenerators::all();
        $posts = Blog::where('status', 1)->orderBy('id', 'desc')->paginate(FrontendSectionsStatusses::first()->blog_posts_per_page ?? 3);

        $setting = Setting::first();

        if ($setting->frontend_additional_url != null){
            return Redirect::to($setting->frontend_additional_url);
        }

        $currency = currency()->symbol;

        return view('index', compact(
            'templates',
            'plansPrepaid',
            'plansSubscription',
            'filters',
            'faq',
            'tools',
            'testimonials',
            'howitWorks',
            'howitWorksDefaults',
            'clients',
            'futures',
            'who_is_for',
            'generatorsList',
            'plansSubscriptionMonthly',
			'plansSubscriptionLifetime',
            'plansSubscriptionAnnual',
            'posts',
            'currency'
        ));
    }

    public function activate(Request $request){
        $valid = $request->liquid_license_status;
        $liquid_license_domain_key = $request->liquid_license_domain_key;
        if ($valid == 'valid'){
            $client = new Client();

            try {
                $response = $client->request('GET', "https://portal.liquid-themes.com/api/license/" . $liquid_license_domain_key);
            } catch (\Exception $e) {
                return response()->json(["status" => "error", "message" => $e->getMessage()]);
            }

            $settings_two = SettingTwo::first();
            // $setting->stripe_status_for_now = 'active';
            $settings_two->liquid_license_domain_key = $liquid_license_domain_key;
            $settings_two->liquid_license_type = json_decode($response->getBody())->licenseType;
            $settings_two->save();
            return redirect()->route('dashboard.index');
        }else{
            echo 'Activation failed!';
        }
    }


    public function howitWorksDefaults(){
        $values = json_decode('{"option": TRUE, "html": ""}');
        $default_html = 'Want to see? <a class="text-[#FCA7FF]" href="https://codecanyon.net/item/magicai-openai-content-text-image-chat-code-generator-as-saas/45408109" target="_blank">'.__('Join').' Magic</a>';

        //Check display bottom line
        $bottomline = CustomSettings::where('key', 'howitworks_bottomline')->first();
        if($bottomline != null){
            $values["option"] = $bottomline->value_int ?? 1;
            $values["html"] = $bottomline->value_html ?? $default_html;
        }else{
            $bottomline = new CustomSettings();
            $bottomline->key = 'howitworks_bottomline';
            $bottomline->title = 'Used in How it Works section bottom line. Controls visibility and HTML value of line.';
            $bottomline->value_int = 1;
            $bottomline->value_html = $default_html;
            $bottomline->save();
            $values["option"] = 1;
            $values["html"] = $default_html;
        }

        return $values;
    }


}