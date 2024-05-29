<?php

namespace App\Http\Controllers\Themes;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ExtensionRepositoryInterface;
use Illuminate\Http\Request;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Market\MarketPlaceController;
use App\Models\Extension;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class ThemeController extends Controller
{
    public function __construct(
        public ExtensionRepositoryInterface $extensionRepository
    )
    {
    }

    public function index()
	{
        $items = $this->extensionRepository->themes();

		# sort the result desc
		$extensions = Extension::query()
            ->where('is_theme', true)->orderBy('id', 'desc')->get();

		return view('panel.admin.themes.index', compact('extensions', 'items'));
	}
	public function buyTheme($slug)
	{
        $item = $this->extensionRepository->find($slug);


		return view('panel.admin.themes.buy', compact('item'));
	}

    public function themeActivate(Request $request, string $token)
    {
        $data = Helper::decodePaymentToken($token);

        $item = $this->extensionRepository->find($data['slug']);

        return view('panel.admin.themes.activate', [
            'item' => $item,
            'token' => $token,
            'success' => $request->get('redirect_status') == 'succeeded',
        ]);
    }
}