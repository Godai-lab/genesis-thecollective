<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\Account;
use Illuminate\Http\Request;
use App\Http\Requests\SiteRequest;
use Illuminate\Support\Facades\Gate;
use App\Services\ProcessFileContentService;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Account $account = null)
    {
        Gate::authorize('haveaccess','site.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $sites = Site::fullaccess()
        ->search($search)
        ->Date($from,$to)
        ->byAccount($account ? $account->id : null)
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();
        return view('site.index',compact('sites', 'account'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Account $account)
    {
        Gate::authorize('haveaccess','site.create');
        return view('site.create', compact('account'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteRequest $request, Account $account)
    {
        Gate::authorize('haveaccess','site.create');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['read_from_db'] = $request->read_from_db ? "1" : "0";

        $fields['account_id'] = $account->id;
       
        $siteContent = ProcessFileContentService::processUrl($fields['url']);
        
        if($siteContent === null){
            // Redirigir al usuario de vuelta a la URL anterior
            toast()->success('¡Error al procesar el archivo. Por favor, asegúrate de que el tipo de archivo sea compatible.!')->push();
            return redirect()->back();
        }

        $fields['content'] = $siteContent;

        Site::create($fields);
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('account.site.index',$account->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(Site $site)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account, Site $site)
    {
        Gate::authorize('haveaccess','site.edit');
        return view('site.edit',compact('account','site'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiteRequest $request, Account $account, Site $site)
    {
        Gate::authorize('haveaccess','site.edit');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['read_from_db'] = $request->read_from_db ? "1" : "0";
        $site->update($fields);
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('account.site.index',$account->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account, Site $site)
    {
        Gate::authorize('haveaccess','site.destroy');
        if($site->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('account.site.index',$account->id);
    }
}
