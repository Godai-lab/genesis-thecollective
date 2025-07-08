<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Account;
use Spatie\PdfToText\Pdf;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use App\Services\GeminiService;
use PhpOffice\PhpWord\IOFactory;
use App\Http\Requests\FileRequest;
use App\Services\AnthropicService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Http;
use PhpOffice\PhpWord\Element\TextRun;
use Illuminate\Support\Facades\Storage;
use App\Services\ProcessFileContentService;

class FileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Account $account = null)
    {
        Gate::authorize('haveaccess','file.index');
        $search=$request->search;
        $from=date($request->from);
        $to=date($request->to);
        $files = File::fullaccess()
        ->search($search)
        ->Date($from,$to)
        ->byAccount($account ? $account->id : null)
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->withQueryString();
        return view('file.index',compact('files', 'account'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Account $account)
    {
        Gate::authorize('haveaccess','file.create');
        // $accounts = Account::fullaccess()->get();
        return view('file.create', compact('account'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FileRequest $request, Account $account)
    {
        Gate::authorize('haveaccess','file.create');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['read_from_db'] = $request->read_from_db ? "1" : "0";

        $file = $request->file('file');
        $fileName = time() . '-' . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
        $fileType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        $filePath = "uploads/$fileName";
        $request->file('file')->move(public_path("uploads"), $fileName);

        $fields['account_id'] = $account->id;
        $fields['file_path'] = $filePath;
        $fields['file_type'] = $fileType;
        $fields['file_size'] = $fileSize;

        // Procesar el archivo según su tipo MIME
        $fileContent = null;
        if ($fileType === 'application/pdf') {
            $fileContent = ProcessFileContentService::processPdf($filePath);
        } elseif ($fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' || 
                $fileType === 'application/msword') {
            $fileContent = ProcessFileContentService::processWord($filePath);
        } elseif ($fileType === 'application/vnd.ms-excel' || 
            $fileType === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            $fileContent = ProcessFileContentService::processExcel($filePath);
        } elseif ($fileType === 'text/csv') {
            $fileContent = ProcessFileContentService::processCSV($filePath);
        } elseif ($fileType === 'text/plain') {
            $fileContent = ProcessFileContentService::processTxt($filePath);
        }

        if($fileContent === null){
            // Redirigir al usuario de vuelta a la URL anterior
            toast()->success('¡Error al procesar el archivo. Por favor, asegúrate de que el tipo de archivo sea compatible.!')->push();
            return redirect()->back();
        }

        $fields['content'] = $fileContent;

        File::create($fields);
        toast()->success('¡Registro exitoso!')->push();
        return redirect()->route('account.file.index',$account->id);
    }

    /**
     * Display the specified resource.
     */
    public function show(File $file)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Account $account, File $file)
    {
        Gate::authorize('haveaccess','file.edit');
        return view('file.edit',compact('account','file'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FileRequest $request, Account $account, File $file)
    {
        Gate::authorize('haveaccess','file.edit');
        $fields = $request->validated();
        $fields['status'] = $request->status ? "1" : "0";
        $fields['read_from_db'] = $request->read_from_db ? "1" : "0";
        $file->update($fields);
        toast()->success('¡Actualización exitosa!')->push();
        return redirect()->route('account.file.index',$account->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Account $account, File $file)
    {
        Gate::authorize('haveaccess','file.destroy');
        if($file->delete()){
            toast()->success('¡Eliminación exitosa!')->push();
        }else{
            toast()->danger('¡Eliminación erronea!')->push();
        }
        
        return redirect()->route('account.file.index',$account->id);
    }
}
