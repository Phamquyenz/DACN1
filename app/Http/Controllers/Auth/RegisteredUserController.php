<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Logic tặng voucher cho thành viên mới
        $newVouchers = \App\Models\Voucher::where('is_for_new_user', true)
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', \Carbon\Carbon::now());
            })
            ->get();
            
        $rewardedVouchers = [];
        foreach($newVouchers as $v) {
            $user->vouchers()->attach($v->id);
            $rewardedVouchers[] = $v;
        }

        if(count($rewardedVouchers) > 0) {
            session()->flash('rewarded_vouchers', collect($rewardedVouchers)->map(function($v) {
                return [
                    'code' => $v->code,
                    'type' => $v->type,
                    'discount_value' => $v->discount_value
                ];
            })->toArray());
        }

        return redirect(RouteServiceProvider::HOME);
    }
}
