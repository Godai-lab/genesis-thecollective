<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubscriptionValidateRequest;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use Stripe\Exception\InvalidRequestException;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('haveaccess','subscription.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $subscriptions = Subscription::fullaccess()
        ->with('user')
        ->with('plan')
        ->search($search)->Date($from,$to)
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();
        return view('subscription.index',compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('haveaccess','subscription.create');
        $users = User::where('status',1)->get();
        $plans = Plan::where('status',1)->get();
        return view('subscription.create',compact('users','plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    
     
     public function store(Request $request)
{
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'plan_id' => 'required|exists:plans,id',
    ]);

    $user = User::findOrFail($request->user_id);
    $plan = Plan::findOrFail($request->plan_id);

    // Verifica si el usuario ya tiene una suscripción activa
    if ($user->subscription && $user->subscription->expires_at > now()) {
        toast()->danger('El usuario ya tiene una suscripción activa.')->push();
        return back()->with('error', 'El usuario ya tiene una suscripción activa.');
    }

    Subscription::Create(
        [
         'user_id' => $user->id,
         'plan_id' => $plan->id,
         'expires_at' => now()->addDays($plan->duration_days),
        ]
    );
    toast()->success('Suscripción asignada correctamente.')->push();
    return redirect()->route('subscription.index');
}   
    
     public function storeold(SubscriptionValidateRequest $request)
    {
        Gate::authorize('haveaccess','subscription.create');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['start_date'] = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();;
        $fields['end_date'] = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        Subscription::create($fields);
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('subscription.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        $plans = [
            'plan_basic' => [
                'name' => 'Plan 1',
                'price' => '$10',
                'interval' => 'Por 1000 créditos',
                'stripe_plan_id' => 'price_1Q7d5EQcRGgyXq6iRQ1hA7JP',
            ],
            'plan_basic2' => [
                'name' => 'Plan 2',
                'price' => '$15',
                'interval' => 'Por 2000 créditos',
                'stripe_plan_id' => 'price_1Q7eU3QcRGgyXq6iucFMyx9C',
            ],
            'plan_basic3' => [
                'name' => 'Plan 3',
                'price' => '$20',
                'interval' => 'Por 3000 créditos',
                'stripe_plan_id' => 'price_1Q7eUTQcRGgyXq6iIn61nRKS',
            ]
        ];
        return view('subscription.show', compact('plans'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        // Gate::authorize('haveaccess','subscription.edit');
        // $users = User::all();
        // $plans = Plan::all();
        // return view('subscription.edit',compact('subscription', 'users','plans'));
        toast()->warning('No se puede editar las subscripciones')->push();
        return back();
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateold(SubscriptionValidateRequest $request, Subscription $subscription)
    {
        Gate::authorize('haveaccess','subscription.edit');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['start_date'] = Carbon::createFromFormat('Y-m-d', $request->start_date)->startOfDay();;
        $fields['end_date'] = Carbon::createFromFormat('Y-m-d', $request->end_date)->endOfDay();
        $subscription->update($fields);
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('subscription.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        Gate::authorize('haveaccess','subscription.destroy');
        if($subscription->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('subscription.index');
    }

    public function createSubscription(Request $request)
    {
        $user = $request->user();
        $planId = $request->input('plan_id'); // El ID del plan de Stripe seleccionado

        try {
            // Crea la suscripción
            $user->newSubscription('default', $planId)
                ->create($request->paymentMethodId);

            toast()->success('¡Suscripción creada exitosamente!')->push();
            return redirect()->route('subscription.show');

        } catch (CardException $e) {
            // Errores relacionados con la tarjeta (como falta de fondos, tarjeta declinada, etc.)
            toast()->danger($e->getMessage())->push();
            Log::error('Error de tarjeta: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema con tu tarjeta: ' . $e->getMessage());

        } catch (InvalidRequestException $e) {
            // Errores de solicitud inválida, como ID de plan incorrecto o parámetros faltantes
            toast()->danger($e->getMessage())->push();
            Log::error('Solicitud inválida: ' . $e->getMessage());
            return back()->with('error', 'Hubo un problema al procesar tu suscripción. Por favor verifica el plan seleccionado o tus datos.');

        } catch (\Exception $e) {
            // Cualquier otro tipo de error
            toast()->danger($e->getMessage())->push();
            Log::error('Error inesperado al crear la suscripción: ' . $e->getMessage());
            return back()->with('error', 'Ocurrió un error inesperado al crear tu suscripción. Por favor, intenta nuevamente más tarde.');
        }
    }


    public function mensajeSubscripcion(Subscription $subscription){
        return view('subscription.message');

    }

}
