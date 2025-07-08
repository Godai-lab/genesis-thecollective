<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
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
        Gate::authorize('haveaccess','role.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $roles = Role::search($search)->Date($from,$to)->with('permissions')->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('role.index',compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('haveaccess','role.create');
        $permissions = Permission::get();
        return view('role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        Gate::authorize('haveaccess','role.create');
        $fields = $request->validated();
        $role = new Role();
        $role->name = $fields['name'];
        $role->slug = $fields['slug'];
        $role->description = $fields['description'];
        $fields['full_access'] = $request->full_access ? "1" : "0";
        $role->full_access = $fields['full_access'];
        $role->save();
        $role->permissions()->sync($request->get('permissions'));
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('role.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        Gate::authorize('haveaccess','role.edit');
        $permissions = Permission::get();
        $role = $role->load('permissions');
        return view('role.edit', compact('role','permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role)
    {
        Gate::authorize('haveaccess','role.edit');
        $fields = $request->validated();
        $role->name = $fields['name'];
        $role->slug = $fields['slug'];
        $role->description = $fields['description'];
        if($request->full_access){
            $fields['full_access'] = "1";
        }else{
            $fields['full_access'] = "0";
        }
        $role->full_access = $fields['full_access'];
        $role->save();
        $role->permissions()->sync($request->get('permissions'));
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('role.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        Gate::authorize('haveaccess','role.destroy');
        if($role->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('role.index');
    }
}
