<?php

namespace App\Http\Controllers;

use App\Models\Favourite;
use Illuminate\Http\Request;

use App\Models\Prompt;
use GPBMetadata\Google\Api\Auth;

class PromptController extends Controller
{
    public function getAll() {
        $prompts = Prompt::where('user_id', auth()->user()->id)->get();
        $favourites = Favourite::where('type', 'prompt')
            ->where('user_id', auth()->user()->id)
            ->get();

		return view('panel.user.openai_chat.components.prompt_library_list', ['promptData' => $prompts, 'favData' => $favourites])->render();
    }

    public function addNew(Request $req) {
        $title = $req->title;
        $prompt = $req->prompt;

        $prompt_record = new Prompt([
            'user_id' => auth()->user()->id,
            'title' => $title,
            'prompt' => $prompt
        ]);

        $prompt_record->save();

        $prompts = Prompt::where('user_id', auth()->user()->id)->get();
		$favourites = Favourite::where('type', 'prompt')
            ->where('user_id', auth()->user()->id)
            ->get();

		return view('panel.user.openai_chat.components.prompt_library_list', ['promptData' => $prompts, 'favData' => $favourites])->render();
    }

    public function updateFav(Request $req) {
        $id = $req->id;

        $favourites = Favourite::where('type', 'prompt')
            ->where('item_id', $id)
            ->where('user_id', auth()->user()->id)
            ->get();

        if ($favourites->count() != 0) {
             $favourites->each->delete();
        } else {
            $favourite = new Favourite();
            $favourite->user_id = auth()->user()->id;
            $favourite->type = 'prompt';
            $favourite->item_id = $id;
            $favourite->save();
        }

        $favourites = Favourite::where('type', 'prompt')
            ->where('user_id', auth()->user()->id)
            ->get();
        return response()->json($favourites);
    }
}
