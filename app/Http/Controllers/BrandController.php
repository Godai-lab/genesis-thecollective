<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Requests\BrandRequest;
use Illuminate\Support\Facades\Gate;

class BrandController extends Controller
{
    var $countries = [
        ['id' => 'Bolivia', 'name' => 'Bolivia'],
        ['id' => 'Guatemala', 'name' => 'Guatemala'],
        ['id' => 'México', 'name' => 'México']
    ];
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('haveaccess','brand.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $brands = Brand::fullaccess()
        ->search($search)
        ->Date($from,$to)
        ->orderBy('id', 'desc')
        ->paginate(5)
        ->withQueryString();
        return view('brand.index',compact('brands'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('haveaccess','brand.create');
        $accounts = Account::fullaccess()->get();
        $countries = $this->countries;
        return view('brand.create',compact('accounts','countries'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $request)
    {
        Gate::authorize('haveaccess','brand.create');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        Brand::create($fields);
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('brand.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Brand $brand)
    {
        return;
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Brand $brand)
    {
        Gate::authorize('haveaccess','brand.edit');
        $accounts = Account::fullaccess()->get();
        $countries = $this->countries;
        return view('brand.edit',compact('brand','accounts','countries'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, Brand $brand)
    {
        Gate::authorize('haveaccess','brand.edit');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $brand->update($fields);
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('brand.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        //
    }
}
