<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class hasTokens
{
    public function handle(Request $request, Closure $next): Response
    {
        $check = $this->checkCredit($request);

        if ($check){
            return $next($request);
        }

        return back()->with(['message' => 'Insufficient credits to create.' , 'type' => 'error']);
    }

    public function checkCredit(Request $request): bool
    {
        $user = Auth::user();

        $team = $user->getAttribute('team');

        $setting = Setting::query()->where('team_functionality', 0)->first();

        if (! $team || $setting) {
            if ($request->route('slug') == 'ai_image_generator'){
                return $user->remaining_images == -1 or $user->remaining_images >0;
            }else{
                return $user->remaining_words == -1 or $user->remaining_words >0;
            }
        }

        $manager = $team->getAttribute('user');

        $teamMember = $team->members()->where('user_id', $user->getAttribute('id'))->first();

        if ($teamMember->allow_unlimited_credits && $request->route('slug') == 'ai_image_generator') {
            return $manager->remaining_images == -1 or $manager->remaining_images >0;
        }else if ($request->route('slug') == 'ai_image_generator') {
            return $teamMember->remaining_images > 0;
        }

        if ($teamMember->allow_unlimited_credits) {
            return $manager->remaining_words == -1 or $manager->remaining_words >0;
        }else {
            return $teamMember->remaining_words > 0;
        }
    }
}
