<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        Gate::authorize('haveaccess','user.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $users = User::select('id','name','username','email','status','created_at','updated_at')
        ->search($search)->Date($from,$to)
        ->with('roles')
        ->orderBy('id', 'desc')
        ->paginate(5)
        ->withQueryString();
        return view('user.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('haveaccess','user.create');
        $roles = Role::all();
        $accounts = Account::all();
        return view('user.create',compact('roles','accounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        Gate::authorize('haveaccess','user.create');
        $fields = $request->validated();
        $user = new User();
        $user->name = $fields['name'];
        $user->username = $fields['username'];
        $user->email = $fields['email'];
        $user->password = bcrypt($fields['password']);
        $fields['status'] = $request->status ? "1" : "0";
        $user->status = $fields['status'];
        $user->save();
        $user->roles()->attach($fields['role']);
        if ($request->has('accounts')) {
            $user->accounts()->sync($fields['accounts']);
        }
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('user.index');
        
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('haveaccess','user.edit');
        $user->load('accounts');
        $roles = Role::orderBy('name')->get();
        $accounts = Account::all();
        return view('user.edit', compact('roles', 'user', 'accounts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        Gate::authorize('haveaccess','user.edit');
        $fields = $request->validated();
        $user->name = $fields['name'];
        $user->username = $fields['username'];
        $user->email = $fields['email'];
        if(!empty(trim($fields['password']))){
            $user->password = bcrypt($fields['password']);
        }
        $fields['status'] = $request->status ? "1" : "0";
        $user->status = $fields['status'];
        $user->save();

        $user->roles()->sync($request->get('role'));
        if ($request->has('accounts')) {
            $user->accounts()->sync($request->accounts);
        } else {
            $user->accounts()->detach();
        }
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('user.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('haveaccess','user.destroy');
        if($user->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('user.index');
    }
}
