<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class NewGeneradorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Gate::allows('haveaccess', 'generador.imagen') || Gate::allows('haveaccess', 'generador.video')) {
    // El usuario tiene al menos uno de los permisos requeridos
} else {
    abort(403); // No autorizado
}

        return view('asistenteGrafica.newgenerador');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Nuevo método para mostrar el generador de videos
    public function videos()
    {
        return view('asistenteGrafica.videogenerador');
    }
}
