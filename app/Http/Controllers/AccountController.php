<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\AccountRequest;


class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        Gate::authorize('haveaccess','account.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $accounts = Account::fullaccess()
        ->search($search)
        ->Date($from,$to)
        ->orderBy('id', 'desc')
        ->paginate(5)
        ->withQueryString();
        return view('account.index',compact('accounts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Gate::authorize('haveaccess','account.create');
        return view('account.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
{
    Gate::authorize('haveaccess', 'account.create');
    // dd($request->all());

    
    $fields = $request->validated();
    $fields['status'] = $request->has('status') ? "1" : "0";

    if ($request->category === "Otra") {
        $fields['category'] = $request->other_category;
    } else {
        $fields['category'] = $request->category;
    }

    Account::create($fields);

    toast()->success('¡Registro exitoso!')->push();
    
    return redirect()->route('account.index');
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        Gate::authorize('haveaccess','account.edit');
        return view('account.edit',compact('account'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(AccountRequest $request, Account $account)
    {
        // $this->authorize('update', [$account, 'account.edit']);

        Gate::authorize('haveaccess','account.edit');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['category'] = $request->category;
        $account->update($fields);
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('account.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        Gate::authorize('haveaccess','account.destroy');
        if($account->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('account.index');
    }
}
