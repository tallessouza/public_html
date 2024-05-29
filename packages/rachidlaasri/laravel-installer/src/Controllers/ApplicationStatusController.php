<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use App\Helpers\Classes\Helper;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use RachidLaasri\LaravelInstaller\Repositories\ApplicationStatusRepositoryInterface;
use RachidLaasri\LaravelInstaller\Requests\LicenseKeyRequest;

class ApplicationStatusController extends Controller
{
    public function __construct(public ApplicationStatusRepositoryInterface $licenseRepository)
    {
    }

    public function license(Request $request)
    {
        $this->licenseRepository->generate($request);

        $portalData = $this->licenseRepository->portal();

        try {
            if (! $portalData) {
                $check = Helper::settingTwo('liquid_license_domain_key');

                if ($check) {
                    $success = $this->licenseRepository->check(
                        $check, true
                    );

                    if ($success) {
                        return to_route('dashboard.user.index')->with([
                            'type' => 'success',
                            'message' => 'License activated successfully'
                        ]);
                    }
                }
            }

        }catch (\Exception $e) {}

        return view('vendor.installer.license', [
            'portal' => $portalData,
            'text' => 'Activate',
        ]);
    }

    public function upgrade(Request $request): View|Application|Factory|\Illuminate\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $this->licenseRepository->generate($request);

        return view('vendor.installer.license', [
            'portal' => null,
            'text' => 'Upgrade',
        ]);
    }

    public function licenseCheck(LicenseKeyRequest $request): RedirectResponse
    {
        $this->licenseRepository->setLicense();

        return redirect()->route('dashboard.user.index');
    }

    public function webhook(Request $request)
    {
        $this->licenseRepository->webhook($request);

        return response()->noContent();
    }
}
