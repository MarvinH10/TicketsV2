<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        try {
            $user_google = Socialite::driver('google')->user();

            $se_registro = User::where('email', $user_google->email)
                ->where('activo', '!=', 0)
                ->first();

            if (empty($se_registro)) {
                $isUDHEmail = str_ends_with($user_google->email, '@udh.edu.pe');
                if (!$isUDHEmail) {
                    return redirect()->route('login')
                        ->with('error', 'Ingrese un correo institucional de la UDH.');
                }

                $user = new User();
                $user->email = $user_google->email;
                $user->google_id = $user_google->id;
                $user->name = $user_google->user['given_name'] ?? $user_google->name;
                $user->apellidos = $user_google->user['family_name'] ?? '';
                $user->password = bcrypt(usuarioCorreo($user_google->email));
                $user->email_verified_at = now();
                $user->assignRole('Usuario');
                $user->save();

                Auth::login($user);

                return redirect()->route('elegirsede')->with('success', 'Por favor elige una sede.');
            } else {
                if ($se_registro->estado == 2) {
                    return redirect()->route('login')->with('error', 'Su cuenta se encuentra suspendida.');
                }

                Auth::login($se_registro);
            }

            if (Auth::user()->sed_id == null) {
                return redirect()->route('elegirsede')
                    ->with('success', 'Por favor elige una sede.');
            }

            $roleName = Auth::user()->rol->rol_nombre;

            if ($roleName === 'Admin') {
                return redirect()->intended(RouteServiceProvider::$ADMINHOME)
                    ->with('success', 'Ha iniciado sesión como administrador.');
            } elseif ($roleName === 'Soporte') {
                return redirect()->intended(RouteServiceProvider::$SUPPORTHOME)
                    ->with('success', 'Ha iniciado sesión como soporte técnico.');
            } else {
                return redirect()->intended(RouteServiceProvider::$USERHOME)
                    ->with('success', 'Ha iniciado sesión como usuario.');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Ocurrió un problema al iniciar sesión.');
        }
    }
}
