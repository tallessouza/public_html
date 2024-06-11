<?php

namespace App\Http\Controllers;

use App\Helpers\Classes\Helper;
use App\Http\Requests\PhotoStudioRequest;
use App\Models\PhotoStudio;
use App\Services\Ai\PhotoStudioService;

class PhotoStudioController extends Controller
{
    public function __construct(
        public PhotoStudioService $service
    ) {
    }

    public function index()
    {
        $last = PhotoStudio::query()
            ->where('user_id', auth()->id())
            ->latest()
            ->first();

        return view('panel.user.photo-studio.index', [
            'last' => $last,
            'images' => PhotoStudio::query()
                ->where('user_id', auth()->id())
                ->latest()
                ->get(),
        ]);
    }

    public function store(PhotoStudioRequest $request)
    {
        if (Helper::appIsDemo()) {
            return response()->json([
                'status' => 'error',
                'message' => trans('This feature is disabled in demo mode.')
            ]);
        }

        if ($request->user()->remaining_images <= 0 and $request->user()->remaining_images != -1) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have no remaining images',
                ]);
            }

            return redirect()->route('dashboard.user.photo-studio.index')->with('error', 'You have no remaining images');
        }

        $photo = $this
            ->service
            ->setAction($request->input('action'))
            ->setPhoto($request->file('photo'))
            ->generate();

        if ($photo) {
            PhotoStudio::query()->create([
                'user_id' => auth()->id(),
                'photo' => $photo,
                'payload' => $request->input('action'),
                'credits' => 1,
            ]);

            userCreditDecreaseForImage($request->user(), 1, 'clipdrop');
        }

        if ($request->ajax()) {
            return response()->json([
                'type' => $photo ? 'success' : 'error',
                'photo' => $photo,
                'message' => $photo ? 'Imagem gerada com sucesso' : 'Failed to generate photo',
            ]);
        }

        if ($photo) {
            return redirect()->route('dashboard.user.photo-studio.index')->with([
                'type' => 'success',
                'message' => 'Imagem gerada com sucesso',
                'photo' => $photo,
            ]);
        }

        return redirect()->route('dashboard.user.photo-studio.index')->with('error', 'Failed to generate photo');
    }

    public function delete(PhotoStudio $photoStudio)
    {
        if (\Auth::id() == $photoStudio->user_id) {
            $photoStudio->delete();
        }

        return redirect()->route('dashboard.user.photo-studio.index')->with([
            'type' => 'success',
            'message' => 'Photo deleted successfully',
        ]);
    }
}
