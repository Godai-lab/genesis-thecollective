<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\PlanServiceLimits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;


class PlanServiceLimitsController extends Controller
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
        $planServiceLimits = PlanServiceLimits::fullaccess()
        ->search($search)->Date($from,$to)
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();
        return view('serviciosPlanes.index',compact('planServiceLimits'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('haveaccess','subscription.create');
     
        $plans = Plan::where( 'status',1)->get();
        return view('serviciosPlanes.create',compact('plans'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       
    // $request->validate([
    //     'service_name' => 'required|exists:users,id',
    //     'plan_id' => 'required|exists:plans,id',
    // ]);
    //dd($request);
      $plan = Plan::findOrFail($request->plan_id);
       PlanServiceLimits::Create(
        [
         'plan_id' => $plan->id,
         'service_name'=>$request->service_name,
         'monthly_limit' => $request->monthly_limit,
        ]
    );
    toast()->success('Registrado correctamente.')->push();
    return redirect()->route('planServiceLimits.index');

    }

    /**
     * Display the specified resource.
     */
    public function show(PlanServiceLimits $planServiceLimits)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $planes = Plan::where( 'status',1)->get();
        $planservice = PlanServiceLimits::find($id);
        return view('serviciosPlanes.edit',compact('planes','planservice'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
         $planservice = PlanServiceLimits::find($id);
    $request->validate([
       'plan_id' => 'required|integer|max:50',
      'service_name' => 'required|string|max:50',
      'monthly_limit'=>'required|integer|max:20',
         
     ]);
   
    $planservice->update([
        'plan_id' => $request->plan_id,
        'service_name' => $request->service_name,
        'monthly_limit' =>$request->monthly_limit
    ]);
        
        toast()->success('Modificado correctamente.')->push();
    return redirect()->route('planServiceLimits.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PlanServiceLimits $planServiceLimits)
    {
        // dd($planServiceLimits);
        // Gate::authorize('haveaccess','subscription.destroy');
        if($planServiceLimits->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('planServiceLimits.index');
    }
}