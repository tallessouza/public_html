<?php

namespace App\Http\Controllers;

use App\Jobs\SendPasswordResetEmail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use App\Jobs\SendConfirmationEmail;

class MailController extends Controller
{
    public function emailConfirmationMail($email_confirmation_code){
        $user = User::where('email_confirmation_code', $email_confirmation_code)->firstOrFail();
        $user->email_confirmation_code = null;
        $user->email_confirmed = 1;
        $user->status = 1;
        $user->save();
		dispatch(new SendConfirmationEmail($user));
        return redirect()->route('login')->with(['message' => __('E-Mail confirmed succesfully.'), 'type' => 'success']);
    }


    public function sendPasswordResetMail(Request $request){
        $user = User::where('email', $request->email)->first();
        if ($user != null){
            $user->password_reset_code = Str::random(67);
            $user->save();
            dispatch(new SendPasswordResetEmail($user));
            return back()->with(['message' => __('Password reset mail sent succesfully'),'type' => 'success' ]);
        }else{
            return back()->with(['message' => __('Password reset mail sent succesfully'),'type' => 'success' ]);
        }
    }

    public function passwordResetCallback($password_reset_code){
        $user = User::where('password_reset_code', $password_reset_code)->firstOrFail();

        return view('panel.authentication.password_reset_final', compact('user'));
    }

    public function passwordResetCallbackSave(Request $request){

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        $user = User::where('password_reset_code', $request->password_reset_code)->firstOrFail();

        $new = $request->password;
        $newC = $request->password_confirmation;

        if ($new == $newC){
            $user->password = Hash::make($new);
            $user->save();
            Auth::login($user);
            return  response()->json([], 200);
        }else{
            return  response()->json([], 449);
        }

    }

}