<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Validator;
use Socialite;
use Exception;
use Auth;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function callback($provider)
    {
        try {
            $user = Socialite::driver($provider)->user();
            $isUser = User::where('provider_id', $user->id)->first();

            if ($isUser) {
                Auth::login($isUser);
                return redirect('/dashboard');
            } else {
                $createUser = User::create([
                    'name' => $user->getName() ?? $user->getEmail(),
                    'email' => $user->getEmail(),
                    'provider_id' => $user->getId(),
                    'password' => encrypt('admin@123')
                ]);

                Auth::login($createUser);
                return redirect('/dashboard');
            }
        } catch (Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
