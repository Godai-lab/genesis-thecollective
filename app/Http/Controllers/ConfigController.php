<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Config;
use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\RedirectResponse;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index(): Response
    {
        // Gate::authorize('haveaccess','config.index');
        // $accounts = Account::fullaccess()
        //     ->orderBy('id', 'desc')
        //     ->paginate(10)
        //     ->withQueryString();
        // return Inertia::render('Config/Index', [
        //     'accounts' => $accounts
        // ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(Config $config)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account)
    {
        Gate::authorize('haveaccess','account.edit');
        $configs = $account->configs;

        return view('account.config',compact('account','configs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Account $account)
    {
        Gate::authorize('haveaccess','account.edit');
        // Valida los datos del formulario
        $request->validate([
            'openai_api_key' => 'required|string',
            'model_openai' => 'required|string',
        ]);

        // Descripciones preestablecidas para cada clave
        $descriptions = [
            'openai_api_key' => 'LLave de la api de OpenAI',
            'model_openai' => 'Modelo de OpenAI /v1/chat/completions',
            'model_embeddings_openai' => 'Modelo de OpenAI para generar embeddings',
            'phone_number_id' => 'ID del número de WhatsApp',
            'prompt_base' => 'Prompt base para el asistente',
            'whatsapp_key' => 'LLave de la api de Whatsapp key',
        ];
        
        // Actualiza cada configuración
        foreach ($request->all() as $key => $value) {
            if (array_key_exists($key, $descriptions)) {
                Config::updateOrCreate(
                    ['account_id' => $account->id, 'key' => $key],
                    ['value' => $value, 'description' => $descriptions[$key]]
                );
            }
        }

        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('account.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Config $config)
    {
        //
    }
}
