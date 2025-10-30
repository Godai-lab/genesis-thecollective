<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Generated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ValidarConceptoController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('haveaccess','genesis.index');
        $accounts = Account::fullaccess()->get();
        $data_generated = [];
        $id_generated = $request->query('generated');

        if ($id_generated) {
            $generated = Generated::find($id_generated);
            if ($generated && $generated->key === 'Concepto') {
                $metadata = $generated->metadata ? json_decode($generated->metadata, true) : [];
                $step = isset($metadata['step']) ? $metadata['step'] : null;
                $data_generated = [
                    'name' => $generated->name,
                    'value' => $generated->value,
                    'rating' => $generated->rating,
                    'status' => $generated->status,
                    'id_generated' => $generated->id,
                    'account_id' => $generated->account_id,
                    'step' => $step,
                    'metadata' => $metadata
                ];
            }
        }
        return view('validarConcepto.index', compact('accounts', 'data_generated'));
    }
    public function getValidarConceptoForm(Request $request)
    {
        try{
            // Validar las URLs y archivos
            $validator = Validator::make($request->all(), [
                'concepto_pais' => 'required|string',
                'concepto_nombre_marca' => 'required|string',
                'concepto_categoria' => 'required|string',
                'concepto_periodo_campania' => 'required|string',
                'concepto_concepto' => 'required|string',
                'id_account' => 'required|integer',
                'id_generated' => 'nullable|integer', //permitir null
            ]);
            if ($validator->fails()) {
                Log::error('Validación fallida en getValidarConceptoForm', [
                    'errors' => $validator->errors()
                ]);
                throw new \Exception('Validación fallida en getValidarConceptoForm');
                // return response()->json(['success' => false, 'error' => $validator->errors()]);
            }
            $concepto_pais = $request->input('concepto_pais');
            $concepto_nombre_marca = $request->input('concepto_nombre_marca');
            $concepto_categoria = $request->input('concepto_categoria');
            $concepto_periodo_campania = $request->input('concepto_periodo_campania');
            $concepto_concepto = $request->input('concepto_concepto');
            $id_account = $request->input('id_account');
            $id_generated = $request->input('id_generated') ?? null;
            if($id_generated){
                $generated = Generated::find($id_generated);
            }else{
                $metadata = [
                    'account_id' => $id_account,
                    'concepto_pais' => $concepto_pais,
                    'concepto_nombre_marca' => $concepto_nombre_marca,
                    'concepto_categoria' => $concepto_categoria,
                    'concepto_periodo_campania' => $concepto_periodo_campania,
                    'concepto_concepto' => $concepto_concepto,
                    'step' => 3,
                ];
                $generated = Generated::create([
                    'account_id' => $id_account,
                    'key' => 'Concepto',
                    'name' => 'Validar Concepto en proceso...',
                    'value' => $concepto_concepto,
                    'rating' => null,
                    'status' => 'processing',
                    'metadata' => json_encode($metadata),
                ]);
            }

            $metadata = $generated->metadata ? json_decode($generated->metadata, true) : [];
            
            $options = [
                'prompt' => [
                    'id' => 'pmpt_68a2319d991c8190a4152ca9c8ae51e705034b13c3fd9d8e',
                    'variables' => [
                        "concepto" => $concepto_concepto,
                        "marca" => $concepto_nombre_marca,
                        "categoria" => $concepto_categoria,
                        "pais" => $concepto_pais,   
                        "periodo" => $concepto_periodo_campania
                    ]
                ],
                'background' => true
            ];

            $response = \App\Services\OpenAiService::createModelResponse($options);

            if (isset($response['error'])) {
                Log::error('Error en la llamada a OpenAiService::createModelResponse (Validar Concepto)', [
                    'error' => $response['error']
                ]);
                throw new \Exception('Error en la llamada a OpenAiService::createModelResponse (Validar Concepto)');
                // return response()->json(['success' => false, 'error' => $response['error']]);
            }

            $metadata['id_generacion_concepto'] = $response['data']['id'];
            $metadata['generacion_concepto_data'] = $response['data'];
            $metadata['generacion_concepto_status'] = 'processing';
            $metadata['step'] = 4;

            $generated->update([
                'name' => 'Validar Concepto en proceso...',
                'metadata' => json_encode($metadata)
            ]);

            return response()->json(['success' => true, 'data' => $response['data'], 'function' => 'getValidarConceptoForm', 'id_generated' => $generated->id]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getValidarConceptoGenesis(Request $request)
    {
        try{
            // Validar las URLs y archivos
            $validator = Validator::make($request->all(), [
                // 'concepto_pais' => 'required|string',
                // 'concepto_nombre_marca' => 'required|string',
                'concepto_categoria' => 'required|string',
                'concepto_periodo_campania' => 'required|string',
                // 'concepto_concepto' => 'required|string',
                'id_account' => 'required|integer',
                'id_generated' => 'nullable|integer',
                'id_genesis' => 'required|integer',
            ]);
            if ($validator->fails()) {
                Log::error('Validación fallida en getValidarConceptoForm', [
                    'errors' => $validator->errors()
                ]);
                throw new \Exception('Validación fallida en getValidarConceptoForm');
                // return response()->json(['success' => false, 'error' => $validator->errors()]);
            }
            // $concepto_pais = $request->input('concepto_pais');
            // $concepto_nombre_marca = $request->input('concepto_nombre_marca');
            $concepto_categoria = $request->input('concepto_categoria');
            $concepto_periodo_campania = $request->input('concepto_periodo_campania');
            // $concepto_concepto = $request->input('concepto_concepto');
            $id_account = $request->input('id_account');
            $id_generated = $request->input('id_generated') ?? null;
            $id_genesis = $request->input('id_genesis');
            $genesis = Generated::find($id_genesis);
            if (!$genesis) {
                return response()->json([
                    'success' => false,
                    'error' => 'Generación no encontrada'
                ]);
            }
            $metadataGenesis = $genesis->metadata ? json_decode($genesis->metadata, true) : [];
            $concepto_concepto = $metadataGenesis['construccionescenario'];
            $id_brief = $metadataGenesis['id_brief'];
            $brief = Generated::find($id_brief);
            if (!$brief) {
                return response()->json([
                    'success' => false,
                    'error' => 'Generación no encontrada'
                ]);
            }
            $metadataBrief = $brief->metadata ? json_decode($brief->metadata, true) : [];
            $concepto_pais = $metadataBrief['country'];
            $concepto_nombre_marca = $metadataBrief['name'];

            if($id_generated){
                $generated = Generated::find($id_generated);
            }else{
                $metadata = [
                    'account_id' => $id_account,
                    'concepto_pais' => $concepto_pais,
                    'concepto_nombre_marca' => $concepto_nombre_marca,
                    'concepto_categoria' => $concepto_categoria,
                    'concepto_periodo_campania' => $concepto_periodo_campania,
                    'concepto_concepto' => $concepto_concepto,
                    'id_brief' => $id_brief,
                    'id_genesis' => $id_genesis,
                    'step' => 3,
                ];
                $generated = Generated::create([
                    'account_id' => $id_account,
                    'key' => 'Concepto',
                    'name' => 'Validar Concepto en proceso...',
                    'value' => $concepto_concepto,
                    'rating' => null,
                    'status' => 'processing',
                    'metadata' => json_encode($metadata),
                ]);
            }

            $metadata = $generated->metadata ? json_decode($generated->metadata, true) : [];
            
            $options = [
                'prompt' => [
                    'id' => 'pmpt_68a2319d991c8190a4152ca9c8ae51e705034b13c3fd9d8e',
                    'variables' => [
                        "concepto" => $concepto_concepto,
                        "marca" => $concepto_nombre_marca,
                        "categoria" => $concepto_categoria,
                        "pais" => $concepto_pais,   
                        "periodo" => $concepto_periodo_campania
                    ]
                ],
                'background' => true
            ];

            $response = \App\Services\OpenAiService::createModelResponse($options);

            if (isset($response['error'])) {
                Log::error('Error en la llamada a OpenAiService::createModelResponse (Validar Concepto)', [
                    'error' => $response['error']
                ]);
                throw new \Exception('Error en la llamada a OpenAiService::createModelResponse (Validar Concepto)');
                // return response()->json(['success' => false, 'error' => $response['error']]);
            }

            $metadata['id_generacion_concepto'] = $response['data']['id'];
            $metadata['generacion_concepto_data'] = $response['data'];
            $metadata['generacion_concepto_status'] = 'processing';
            $metadata['step'] = 4;

            $generated->update([
                'name' => 'Validar Concepto en proceso...',
                'metadata' => json_encode($metadata)
            ]);

            return response()->json(['success' => true, 'data' => $response['data'], 'function' => 'getValidarConceptoForm', 'id_generated' => $generated->id]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function get_concepto($generationId){
        try {
            $generated = Generated::find($generationId);
            
            if (!$generated) {
                return response()->json([
                    'success' => false,
                    'error' => 'Generación no encontrada'
                ], 404);
            }
    
            $metadata = $generated->metadata ? json_decode($generated->metadata, true) : [];
    
            $content = '';
            $sources = [];
            $statusgenerated = 'processing';
    
            $response = \App\Services\OpenAiService::getModelResponse($metadata['id_generacion_concepto']);
    
            if(isset($response['success']) && !$response['success']){
                return response()->json([
                    'success' => false,
                    'error' => $response['error']
                ], 500);
            }
    
            if($response['data']['status'] === 'completed'){
                $statusgenerated = 'completed';
                if (isset($response['data']['output'])) {
                    foreach ($response['data']['output'] as $output_item) {
                        if ($output_item['type'] === 'message') {
                            if (isset($output_item['content'][0]['text'])) {
                                $content = $output_item['content'][0]['text'];
                            }
                            if (isset($output_item['content'][0]['annotations'])) {
                                foreach($output_item['content'][0]['annotations'] as $annotation){
                                    if($annotation['type'] === 'url_citation'){
                                        $sources[] = $annotation['url'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
    
    
            if($statusgenerated === 'completed'){
    
                $metadata['generacion_concepto_content'] = $content;
                $metadata['generacion_concepto_sources'] = $sources;
                $metadata['generacion_concepto_status'] = 'completed';
    
                $generated->update([
                    'metadata' => json_encode($metadata)
                ]);
    
                return response()->json([
                    'success' => true,
                    'status' => 'completed',
                    'generation_id' => $generated->id,
                    'data' => $content,
                    'sources' => $sources
                ]);
            }else{
                return response()->json([
                    'success' => true,
                    'status' => $generated->status,
                    'generation_id' => $generated->id
                ]);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error al consultar estado: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveValidarConcepto(Request $request){
        try{
            // Validar las URLs y archivos
            $validator = Validator::make($request->all(), [
                'validarConcepto' => 'required|string',
                'id_account' => 'required|integer',
                'rating' => 'required|integer',
                'file_name' => 'required|string',
                'id_generated' => 'required|integer',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }
        
            $validarConcepto = $request->input('validarConcepto');
            $id_account = $request->input('id_account');
            $id_generated = $request->input('id_generated');
            $rating = $request->input('rating');
            $file_name = $request->input('file_name');
        
            $generated = Generated::find($id_generated);
        
            if (!$generated) {
                return response()->json([
                    'success' => false,
                    'error' => 'Generación no encontrada'
                ]);
            }
        
            $metadata = $generated->metadata ? json_decode($generated->metadata, true) : [];
        
            $metadata['validar_concepto'] = $validarConcepto;
            $metadata['step'] = 5;
        
            $generated->update([
                'name' => $file_name,
                'value' => $validarConcepto,
                'rating' => $rating,
                'status' => 'completed',
                'metadata' => json_encode($metadata)
            ]);
        
            return response()->json(['success' => true, 'data' => $validarConcepto, 'function' => 'saveValidarConcepto', 'id_generated' => $generated->id]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function mejorarConcepto(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'validarConcepto' => 'required|string',
                'id_account' => 'required|integer',
                'id_generated' => 'required|integer',
            ]);
        
            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }
        
            $id_account = $request->input('id_account');
            $id_generated = $request->input('id_generated');
            $validarConcepto = $request->input('validarConcepto');
        
            $generated = Generated::find($id_generated);
            if (!$generated) {
                return response()->json([
                    'success' => false,
                    'error' => 'Generación no encontrada'
                ]);
            }
        
            $metadata = $generated->metadata ? json_decode($generated->metadata, true) : [];

            $id_genesis = $metadata['id_genesis'];
            $genesis = Generated::find($id_genesis);
            if (!$genesis) {
                return response()->json([
                    'success' => false,
                    'error' => 'Genesis no encontrada'
                ]);
            }
            $metadataGenesis = $genesis->metadata ? json_decode($genesis->metadata, true) : [];
            $id_brief = $metadata['id_brief'];
            $brief = Generated::find($id_brief);
            if (!$brief) {
                return response()->json([
                    'success' => false,
                    'error' => 'Brief no encontrada'
                ]);
            }
            $metadataBrief = $brief->metadata ? json_decode($brief->metadata, true) : [];
            $creatividad = $metadataGenesis['construccionescenario'];
        
            $options = [
                'prompt' => [
                    'id' => 'pmpt_68cc1dd185588193a98bf0d237f72c0206d213a923b14fc6',
                    'variables' => [
                        "creatividad" => $creatividad,
                        "concepto_validado" => $validarConcepto,
                    ]
                ],
                'background' => true
            ];
        
            // Llamar al nuevo endpoint de deep research
            $response = \App\Services\OpenAiService::createModelResponse($options);
        
            if (isset($response['error'])) {
                Log::error('Error en la llamada a OpenAiService::createModelResponse (Mejorar Concepto)', [
                    'error' => $response['error']
                ]);
                return response()->json(['success' => false, 'error' => $response['error']]);
            }

            $metadataGenesis['id_generacion_mejorar_concepto'] = $response['data']['id'];
            $metadataGenesis['generacion_mejorar_concepto_data'] = $response['data'];
            $metadataGenesis['generacion_mejorar_concepto_status'] = 'pending';
        
            $metadataGenesis['step'] = 9;

            $newGenesisGenerated = Generated::create([
                'account_id' => $id_account,
                'key' => 'Genesis',
                'name' => 'Mejorando concepto en proceso...',
                'value' => '<div class="text-center p-4"><div class="spinner-border" role="status"></div><p class="mt-2">Generando mejorar concepto...</p></div>',
                'rating' => null,
                'status' => 'processing', // Nuevo campo para el estado
                'metadata' => json_encode($metadataGenesis)
            ]);
        
            return response()->json(['success' => true, 'data' => $response['data'], 'function' => 'mejorarConcepto', 'id_generated' => $newGenesisGenerated->id]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
