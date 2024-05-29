<?php

namespace App\Http\Controllers\Market;

use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use App\Models\Extension;
use App\Models\SettingTwo;
use App\Repositories\Contracts\ExtensionRepositoryInterface;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MarketPlaceController extends Controller
{
    public function __construct(
        public ExtensionRepositoryInterface $extensionRepository
    ) {
    }

    public function index()
    {
        $items = $this->extensionRepository->extensions();

        // $jsonFile = base_path('addons.json');
        // $addonsData = File::get($jsonFile);
        // $addons = json_decode($addonsData);

        return view('panel.admin.marketplace.index', compact('items'));
    }

    public function extension($slug)
    {
        $item = $this->extensionRepository->find($slug);

        if (! $item) {
            return to_route('dashboard.admin.marketplace.index')->with('error', 'Extension not found.');
        }

        return view('panel.admin.marketplace.show', compact('item'));
    }

    public function licensedExtension()
    {
        $items = $this->extensionRepository->licensed(
            $this->extensionRepository->extensions()
        );


        return view('panel.admin.marketplace.licensed', compact( 'items'));
    }

    public function buyExtension($slug)
    {
        $item = $this->extensionRepository->find($slug);

        if (! $item) {
            return to_route('dashboard.admin.marketplace.index')->with('error', 'Extension not found.');
        }

        $response = $this->extensionRepository->request('get', '', [], $item['routes']['paymentJson']);

        if ($response->ok()) {
            $data = $response->json('data');

            return view('panel.admin.marketplace.payment', compact('item', 'data'));
        }

        if (! $item) {
            return to_route('dashboard.admin.marketplace.index')->with('error', 'Extension not found.');
        }
    }


    public function extensionActivate(Request $request, string $token)
    {
        $data = Helper::decodePaymentToken($token);

        $item = $this->extensionRepository->find($data['slug']);

        return view('panel.admin.marketplace.activate', [
            'item' => $item,
            'token' => $token,
            'success' => $request->get('redirect_status') == 'succeeded',
        ]);
    }
}
