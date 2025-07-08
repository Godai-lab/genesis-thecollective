<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\FluxService;
use App\Services\GeminiService;
use App\Services\LeonardoService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AsistenteGraficaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('haveaccess','asistentegrafica.index');
        $accounts = Account::fullaccess()->get();

        return view('asistenteGrafica.index', compact('accounts'));
    }

    public function generarLogo(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'style' => 'required|string',
                'asistenteGraficaPrompt' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            $style = $request->input('style');
            $asistenteGraficaPrompt = $request->input('asistenteGraficaPrompt');

            $prompt = '';

            switch (strtolower($style)) {
                case 'minimalist':
                    $prompt = "A minimalist, conceptual logo design, $asistenteGraficaPrompt with abstract geometric shapes, playing with negative space and symmetry. The design features a single bold color on a neutral white background, creating a clean and modern look. The logo uses subtle, flowing lines and evokes a sense of simplicity and innovation.";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "cadc8cd6-7838-4c99-b645-df76be8ba8d8";
                    break;

                case 'classic':
                    $prompt = "A minimalist, classic logo design, $asistenteGraficaPrompt, placed on a neutral white background. The style is clean, symmetrical, and sophisticated, evoking a sense of heritage and tradition.";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "111dc692-d470-4eec-b791-3475abac4c46";
                    break;

                case 'modern':
                    $prompt = "A modern logo design for $asistenteGraficaPrompt, incorporating clean soft gradient colors on a white background. The design is dynamic and futuristic, combining minimalist elements with a sense of motion and innovation. The logo is simple yet striking, perfect for a contemporary brand";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "6fedbf1f-4a17-45ec-84fb-92fe524a29ef";
                    break;

                default:
                    throw new Exception('Invalid style selected');
            }

            $modelId = "6b645e3a-d64f-4341-a6d8-7a3690fbf042";
            $width = 512;
            $height = 512;
            $num_images = 1;
            $transparency = "disabled";
            $contrast = 3.5;
            $alchemy = false;
            $enhancePrompt = false;
            $public = false;
            $negative_prompt = null;
            $seed = null;

            // $imageGenerationCreate =  LeonardoService::imageGenerationCreate($prompt, $modelId, $presetStyle, $width, $height, $num_images, $transparency, $contrast, $alchemy, $enhancePrompt, $public, $negative_prompt, $photoReal, $styleUUID, $seed);

            $imageGenerationCreate = FluxService::GenerateImageFlux($prompt, $width, $height);

            if (isset($imageGenerationCreate['error'])) throw new Exception($imageGenerationCreate['error']);

            $generationId = $imageGenerationCreate['data'];

            // $imageGenerationURL =  LeonardoService::waitForImageGeneration($generationId);

            $imageGenerationURL =  FluxService::waitForImageGeneration($generationId);

            if (isset($imageGenerationURL['error'])) throw new Exception($imageGenerationURL['error']);

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $imageGenerationURL, 'goto' => 3, 'function' => 'imageGenerationCreate']);

        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }
    }

    public function generarConceptArt(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'style' => 'required|string',
                'asistenteGraficaPrompt' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            $style = $request->input('style');
            $asistenteGraficaPrompt = $request->input('asistenteGraficaPrompt');

            $prompt = '';

            switch (strtolower($style)) {
                case 'arte_conceptual':
                    $prompt = "$asistenteGraficaPrompt featuring a beautifully balanced composition. The scene is illuminated with soft, natural light, capturing realistic textures and details. The color palette consists of warm, harmonious tones, creating a calming and aesthetically pleasing image. The image is in the style of Unsplash photography, with natural lighting and high definition, very realistic, cover photo, photo contest winner";
                    $modelId = "6b645e3a-d64f-4341-a6d8-7a3690fbf042";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "97c20e5c-1af6-4d42-b227-54d03d8f0727";
                    $seed = null;
                    $negative_prompt = "text, six fingers";
                    break;

                case 'storyboard':
                    $prompt = "An illustrated minimalist storyboard scene of $asistenteGraficaPrompt, The character and environment drawn in a semi-realistic illustrative style. The color palette is cohesive, with soft tones and defined contrasts, capturing emotions and movement, giving a clear sense of narrative progression";
                    $modelId = "2067ae52-33fd-4a82-bb92-c2c55e7d2786";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "645e4195-f63d-4715-a3f2-3fb1e6eb8c70";
                    $seed = "8244318734";
                    $negative_prompt = "Text, watermark, Bad anatomy, Bad proportions, Bad quality, Blurry, Collage, Cropped, Deformed, Dehydrated, Disconnected limbs, Disfigured, Disgusting, Error, Extra arms, Extra hands, Extra limbs, Fused fingers, Grainy, Gross proportions, Jpeg, Jpeg artifacts, Long neck, Low quality, Low res, Malformed limbs, Missing arms, Missing fingers, Mutated, Mutated hands, Mutated limbs, Out of focus, Out of frame, Picture frame, Pixel, Pixelated, Poorly drawn face, Poorly drawn hands, Signature, Text, Ugly";
                    break;

                default:
                    throw new Exception('Invalid style selected');
            }

            $width = 1440;
            $height = 768;
            $num_images = 1;
            $transparency = "disabled";
            $contrast = 3.5;
            $alchemy = true;
            $enhancePrompt = true;
            $public = false;
            

            // $imageGenerationCreate =  LeonardoService::imageGenerationCreate($prompt, $modelId, $presetStyle, $width, $height, $num_images, $transparency, $contrast, $alchemy, $enhancePrompt, $public, $negative_prompt, $photoReal, $styleUUID, $seed);

            $imageGenerationCreate = FluxService::GenerateImageFlux($prompt, $width, $height);

            if (isset($imageGenerationCreate['error'])) throw new Exception($imageGenerationCreate['error']);

            $generationId = $imageGenerationCreate['data'];

            // $imageGenerationURL =  LeonardoService::waitForImageGeneration($generationId);
            $imageGenerationURL =  FluxService::waitForImageGeneration($generationId);


            if (isset($imageGenerationURL['error'])) throw new Exception($imageGenerationURL['error']);

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $imageGenerationURL, 'goto' => 5, 'function' => 'imageGenerationCreateConceptArt']);

        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }
    }

    public function generarExperimental(Request $request){
        try {

            $validator = Validator::make($request->all(), [
                '_token' => 'required',
                'style' => 'required|string',
                'asistenteExperimentalPrompt' => 'required|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()]);
            }

            $style = $request->input('style');
            $asistenteExperimentalPrompt = $request->input('asistenteExperimentalPrompt');

            $prompt = '';

            switch (strtolower($style)) {
                case 'arte_conceptual':
                    $prompt = "$asistenteExperimentalPrompt featuring a beautifully balanced composition. The scene is illuminated with soft, natural light, capturing realistic textures and details. The color palette consists of warm, harmonious tones, creating a calming and aesthetically pleasing image. The image is in the style of Unsplash photography, with natural lighting and high definition, very realistic, cover photo, photo contest winner";
                    $modelId = "6b645e3a-d64f-4341-a6d8-7a3690fbf042";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "97c20e5c-1af6-4d42-b227-54d03d8f0727";
                    $seed = null;
                    $negative_prompt = "text, six fingers";
                    break;

                case 'storyboard':
                    $prompt = "An illustrated minimalist storyboard scene of $asistenteExperimentalPrompt, The character and environment drawn in a semi-realistic illustrative style. The color palette is cohesive, with soft tones and defined contrasts, capturing emotions and movement, giving a clear sense of narrative progression";
                    $modelId = "2067ae52-33fd-4a82-bb92-c2c55e7d2786";
                    $presetStyle = null;
                    $photoReal = false;
                    $styleUUID = "645e4195-f63d-4715-a3f2-3fb1e6eb8c70";
                    $seed = "8244318734";
                    $negative_prompt = "Text, watermark, Bad anatomy, Bad proportions, Bad quality, Blurry, Collage, Cropped, Deformed, Dehydrated, Disconnected limbs, Disfigured, Disgusting, Error, Extra arms, Extra hands, Extra limbs, Fused fingers, Grainy, Gross proportions, Jpeg, Jpeg artifacts, Long neck, Low quality, Low res, Malformed limbs, Missing arms, Missing fingers, Mutated, Mutated hands, Mutated limbs, Out of focus, Out of frame, Picture frame, Pixel, Pixelated, Poorly drawn face, Poorly drawn hands, Signature, Text, Ugly";
                    break;

                default:
                    throw new Exception('Invalid style selected');
            }

            $model = "gemini-2.0-flash-exp-image-generation";
            $temperature = 1.0;
            $response_mime_type = null;
            $response_modalities = ["Text","Image"];

            $imageGenerationCreate = GeminiService::TextOnlyEntry($prompt, $model, $temperature, $response_mime_type, $response_modalities);

            if (isset($imageGenerationCreate['error'])) throw new Exception($imageGenerationCreate['error']);

            if (isset($imageGenerationCreate['data']['candidates'][0]['content']['parts'][0]['inlineData'])) {
                $imageGenerationCreate = $imageGenerationCreate['data']['candidates'][0]['content']['parts'][0]['inlineData']['data'];
            }else{
                throw new Exception('Error al generar la imagen');
            }

            return response()->json(['success' => 'Datos procesados correctamente.', 'details' => $imageGenerationCreate, 'goto' => 7, 'function' => 'imageGenerationCreateExperimental']);

        } catch (Exception $e) {
            return response()->json(['error' => $e]);
        }
    }
    
}
