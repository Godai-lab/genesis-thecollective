<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Logging\TestDox\PlainTextRenderer;

class PlanController extends Controller
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
        $plans = Plan::fullaccess()
        ->search($search)->Date($from,$to)
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();
        return view('planes.index',compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('planes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $data = $request->all();
    $data['status'] = $request->has('status') ? 1 : 0; // Si está presente, es 1; si no, es 0

    Plan::create($data);
    toast()->success('¡Registro exitoso!')->push();
    
    return redirect()->route('plans.index');
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
    public function edit(Plan $plan)
    {
        Gate::authorize('haveaccess','subscription.edit');
        return view('planes.edit',compact('plan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
{
   
    $plan = Plan::findOrFail($id);
    $request->validate([
       'name' => 'required|string|max:50',
      'duration_days' => 'required|integer|max:200',
         
     ]);

   
    $plan->update([
        'name' => $request->name,
        'duration_days' => $request->duration_days,
        'status' =>$request->has('status') ? 1 : 0,
    ]);
    toast()->success('Modificación exitosa!')->push();
    // Redirigir con mensaje de éxito
    return redirect()->route('plans.index');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
