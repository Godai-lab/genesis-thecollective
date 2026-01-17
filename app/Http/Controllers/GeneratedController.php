<?php

namespace App\Http\Controllers;

use App\Models\Generated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use App\Models\Account;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class GeneratedController extends Controller
{
    public function getGeneratedBrief(Request $request){
        $validator = Validator::make($request->all(), [
            'accountID' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $accountId = $request->input('accountID');

        $Briefs = Generated::where('account_id',$accountId)->where('key','Brief')
        ->where('status','completed')
        ->where('metadata','!=',null)
        ->get();

        $Briefs = collect([
            (object) [
                'id' => '',
                'name' => 'Ninguno'
            ]
        ])->concat(
            $Briefs->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })
        );

        return response()->json($Briefs);
    }
    public function getGeneratedBriefV2(Request $request){
        $validator = Validator::make($request->all(), [
            'accountID' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $accountId = $request->input('accountID');

        $Briefs = Generated::where('account_id',$accountId)
        ->where('key','Brief')
        ->where('status','completed')
        ->where('metadata','!=',null)
        ->get();

        $Briefs = collect([
            (object) [
                'id' => '',
                'name' => 'Ninguno'
            ]
        ])->concat(
            $Briefs->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })
        );

        return response()->json($Briefs);
    }
    public function getGeneratedGenesis(Request $request){
        $validator = Validator::make($request->all(), [
            'accountID' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $accountId = $request->input('accountID');  
        $Genesis = Generated::where('account_id',$accountId)->where('key','Genesis')
        ->where('status','completed')
        ->where('metadata','!=',null)
        ->get();

        $Genesis = collect([
            (object) [
                'id' => '',
                'name' => 'Ninguno'
            ]
        ])->concat(
            $Genesis->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })
        );

        return response()->json($Genesis);
    }

    public function getGeneratedGenesisV2(Request $request){
        $validator = Validator::make($request->all(), [
            'accountID' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $accountId = $request->input('accountID');

        $Genesis = Generated::where('account_id',$accountId)
        ->where('key','Genesis')
        ->where('status','completed')
        ->where('metadata','!=',null)
        ->get();

        $Genesis = collect([
            (object) [
                'id' => '',
                'name' => 'Ninguno'
            ]
        ])->concat(
            $Genesis->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })
        );

        return response()->json($Genesis);
    }

    public function getGeneratedInvestigation(Request $request){
        $validator = Validator::make($request->all(), [
            'accountID' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()]);
        }
        $accountId = $request->input('accountID');

        $investigation = Generated::where('account_id',$accountId)
        ->where('key','Investigacion')
        ->where('status','completed')
        ->where('metadata','!=',null)
        ->get();

        $investigation = collect([
            (object) [
                'id' => '',
                'name' => 'Ninguno'
            ]
        ])->concat(
            $investigation->map(function ($item) {
                return (object) [
                    'id' => $item->id,
                    'name' => $item->name
                ];
            })
        );

        return response()->json($investigation);
    }

    public function index(Request $request){
        Gate::authorize('haveaccess','generated.index');

        $accounts = collect([
            (object) [
                'id' => '',
                'name' => 'Todos'
            ]
        ])->concat(
            Account::fullaccess()->select('id', 'name')->get()
        );

        $type=$request->type;
        $account=$request->account;
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $generateds = Generated::fullaccess()
        ->type($type)
        ->account($account)
        ->search($search)
        ->Date($from,$to)
        ->with('account')
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();

        $types = collect([
            (object) [
                'id' => '',
                'name' => 'Todos'
            ]
        ])->concat(
            Generated::fullaccess()->distinct()->pluck('key')
                ->map(function ($key) {
                    return (object) [
                        'id' => $key,
                        'name' => $key
                    ];
                })
        );

        return view('generated.index',compact('generateds','accounts','types'));
    }

    public function create(){
        // return redirect()->route('dashboard');
        $accounts = Account::fullaccess()->get();
        return view('generated.create',compact('accounts'));
    }

    public function store(Request $request)
{
    // Validar la entrada
    $validator = Validator::make($request->all(), [
        'account' => 'required|exists:accounts,id', // Verifica que la cuenta exista
        'brief' => 'required|string', // Validación del contenido
        'file_name' => 'required|string',
        'rating' => 'required|integer|min:1|max:5',
        'country' => 'required|string',
        'name' => 'required|string',
        'slogan' => 'nullable|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    $country = $request->input('country');
    $name = $request->input('name');
    $slogan = $request->input('slogan');

    $metadata = [
        'country' => $country,
        'name' => $name,
        'slogan' => $slogan,
        'extraccionIA' => null,
        'brief' => $request->input('brief'),
        'started_at' => now()->toISOString(),
        'step' => 10,
    ];

    // Crear el registro
    Generated::create([
        'account_id' => $request->input('account'),
        'key' => 'Brief',
        'name' => $request->input('file_name'),
        'value' => $request->input('brief'),
        'rating' => $request->input('rating'),
        'status' => 'completed',
        'metadata' => json_encode($metadata),
    ]);

    // Manejo de respuesta dependiendo del tipo de solicitud
    if ($request->ajax()) {
        return response()->json(['success' => 'Archivo creado correctamente']);
    }

    toast()->success('¡Brief creado exitosamente!')->push();
    return redirect()->route('generated.index')->with('success', 'Archivo creado correctamente');
}

    


    public function edit(Generated $generated){
        Gate::authorize('haveaccess','generated.edit');
        return view('generated.edit',compact('generated'));
    }

    public function update(Request $request, Generated $generated){
        Gate::authorize('haveaccess','generated.edit');
        $generated->update($request->all());
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('generated.index');
    }

    public function destroy(Generated $generated){
        Gate::authorize('haveaccess','generated.destroy');
        if($generated->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        return redirect()->route('generated.index');
    }

    public function download(Generated $generated)
    {

        $fields = [
            'generated' => $generated->value,
        ];

        // Cargar la vista Blade que contiene la plantilla PDF
        $pdf = Pdf::setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => true
        ])->loadView('generated.pdf.template', array_merge($fields));
        
        // Obtén la fecha y hora actual
        $now = Carbon::now();

        // Formatea la fecha y hora como una cadena en el formato deseado (por ejemplo, "YYYYMMDD_HHMMSS")
        $timestamp = $now->format('Ymd_His');

        // Descargar el PDF
        return $pdf->download('Generated_' . $timestamp . '.pdf');

    }

    public function continue(Generated $generated)
    {
        // Redirigir según el tipo de contenido
        switch($generated->key) {
            case 'Brief':
                return redirect()->route('herramienta1.index', ['generated' => $generated->id]);
            case 'Genesis':
                return redirect()->route('herramienta2.index', ['generated' => $generated->id]);
            case 'Investigacion':
                return redirect()->route('investigacion.index', ['generated' => $generated->id]);
            case 'Concepto':
                return redirect()->route('validar-concepto.index', ['generated' => $generated->id]);
            default:
                return redirect()->route('herramienta1.index', ['generated' => $generated->id]);
        }
    }
}
