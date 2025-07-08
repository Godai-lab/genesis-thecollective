<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Account;
use App\Models\Role;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;



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
        // Crear el validador con reglas y mensajes personalizados
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9_]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ],
        [
            'name.required' => 'Este campo es requerido',
            'name.regex' => 'El nombre de usuario debe tener una sola palabra'
        ]);
        
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'status' => true,
            ]);

            $role = Role::where('name', 'Basic')->first();
            if ($role) {
                $user->roles()->attach($role);
            }

            $categoriaSeleccionada = $request->categoria === 'Otra' ? $request->otra_categoria : $request->categoria;
            $account = Account::create([
                'name' => $user->name,
                'category' => $categoriaSeleccionada,
                'status' => true,
            ]);
            $user->accounts()->attach($account->id);

            $plan = Plan::firstOrCreate(
                ['name' => 'Basic'],
                ['duration_days' => 14, 'status' => 1]
            );

            Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'expires_at' => now()->addDays($plan->duration_days),
            ]);

            event(new Registered($user));

            Auth::login($user);

            DB::commit();

            toast()->success('¡Gracias por registrarte. Bienvenido!')->push();

            return redirect(RouteServiceProvider::HOME);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error en el registro de usuario: ' . $e->getMessage());
            toast()->warning('Ha ocurrido un error: ' . $e->getMessage())->push();
            return back()->withErrors(['error' => 'Ocurrió un problema durante el registro. Por favor, inténtalo nuevamente.']);
        }
    } 
     

}
