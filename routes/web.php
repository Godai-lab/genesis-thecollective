<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AsistenteCreativoController;
use App\Http\Controllers\AsistenteExperimentalController;
use App\Http\Controllers\AsistenteGraficaController;
use App\Http\Controllers\AsistenteInnovacionController;
use App\Http\Controllers\AsistenteSocialMediaController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FileProcessingController;
use App\Http\Controllers\GeneratedController;
use App\Http\Controllers\Herramienta1Controller;
use App\Http\Controllers\Herramienta2Controller;
use App\Http\Controllers\InvestigacionController;
use App\Http\Controllers\NewGeneradorController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PlanServiceLimitsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/intro', function () {
    return view('intro');
})->name('intro');

Route::get('/terms_and_conditions', function () {
    return view('termsConditions');
})->name('termsConditions');

// Route::get('/', function () {
//     // Verifica si el usuario está autenticado
//     if (Auth::check()) {
//         // Si está autenticado, redirige al dashboard
//         return redirect()->route('dashboard');
//     } else {
//         // Si no está autenticado, redirige al formulario de login
//         return redirect()->route('login');
//     }
// });

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth','verified','subscription'])->name('dashboard');

Route::middleware(['auth','subscription'])->group(function () {
    Route::get('/asistentes', function () {
        return view('asistentes');
    })->name('asistentes');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('account', AccountController::class)->except(['show']);
    Route::get('account/{account}/config', [ConfigController::class, 'edit'])->name('config.edit');
    Route::put('account/{account}/config', [ConfigController::class, 'update'])->name('config.update');

    Route::prefix('account/{account}')->group(function () {
        Route::resource('file', FileController::class)->except(['show'])->names([
            'index' => 'account.file.index',
            'create' => 'account.file.create',
            'store' => 'account.file.store',
            'edit' => 'account.file.edit',
            'update' => 'account.file.update',
            'destroy' => 'account.file.destroy',
        ]);
        Route::resource('site', SiteController::class)->except(['show'])->names([
            'index' => 'account.site.index',
            'create' => 'account.site.create',
            'store' => 'account.site.store',
            'edit' => 'account.site.edit',
            'update' => 'account.site.update',
            'destroy' => 'account.site.destroy',
        ]);
    });

    // Route::resource('file', FileController::class)->except(['show']);

    Route::resource('brand', BrandController::class)->except(['show']);

    Route::resource('user', UserController::class)->except(['show']);

    Route::resource('role', RoleController::class)->except(['show']);

    Route::resource('subscription', SubscriptionController::class)->except(['show']);

    Route::resource('generated', GeneratedController::class)->except(['show']);
    Route::get('generated/{generated}/download', [GeneratedController::class, 'download'])->name('generated.download');

    Route::resource('demo', FileController::class)->except(['show']);
    Route::post('/upload', [FileController::class, 'upload'])->name('upload');

    Route::get('/herramienta1', [Herramienta1Controller::class, 'index'])->name('herramienta1.index');
    Route::post('/herramienta1/{step}/step', [Herramienta1Controller::class, 'step'])->name('herramienta1.step');
    Route::post('/herramienta1', [Herramienta1Controller::class, 'step1'])->name('herramienta1.step1');
    Route::post('/herramienta1/rellenaria', [Herramienta1Controller::class, 'rellenaria'])->name('herramienta1.rellenaria');
    Route::post('/herramienta1/rellenariasave', [Herramienta1Controller::class, 'rellenariasave'])->name('herramienta1.rellenariasave');
    Route::post('/herramienta1/savefields', [Herramienta1Controller::class, 'savefields'])->name('herramienta1.savefields');
    Route::post('/herramienta1/datosextras', [Herramienta1Controller::class, 'datosextras'])->name('herramienta1.datosextras');
    Route::post('/herramienta1/datosextrassave', [Herramienta1Controller::class, 'datosextrassave'])->name('herramienta1.datosextrassave');
    Route::post('/herramienta1/saveBrief', [Herramienta1Controller::class, 'saveBrief'])->name('herramienta1.saveBrief');
    Route::post('/herramienta1/generatebriefia', [Herramienta1Controller::class, 'GenerarBriefGenerateIA'])->name('herramienta1.generatebriefia');

    Route::get('/herramienta2', [Herramienta2Controller::class, 'index'])->name('herramienta2.index');
    Route::post('/herramienta2/generarGenesis', [Herramienta2Controller::class, 'generarGenesis'])->name('herramienta2.generarGenesis');
    Route::post('/herramienta2/regenerateGenesis', [Herramienta2Controller::class, 'regenerateGenesis'])->name('herramienta2.regenerateGenesis');
    Route::post('/herramienta2/construccionescenario', [Herramienta2Controller::class, 'construccionescenario'])->name('herramienta2.construccionescenario');
    Route::post('/herramienta2/regenerarConstruccionEscenario', [Herramienta2Controller::class, 'regenerarConstruccionEscenario'])->name('herramienta2.regenerarConstruccionEscenario');
    Route::post('/herramienta2/eleccioncampania', [Herramienta2Controller::class, 'eleccioncampania'])->name('herramienta2.eleccioncampania');
    Route::post('/herramienta2/saveeleccioncampania', [Herramienta2Controller::class, 'saveeleccioncampania'])->name('herramienta2.saveeleccioncampania');
    Route::post('/herramienta2/saveEstrategiaCreatividadInnovacion', [Herramienta2Controller::class, 'saveEstrategiaCreatividadInnovacion'])->name('herramienta2.saveEstrategiaCreatividadInnovacion');
    Route::get('/herramienta2/download', [Herramienta2Controller::class, 'download'])->name('herramienta2.download');
    Route::post('/herramienta2/generateNewCreatividadEstrategiaInnovacion', [Herramienta2Controller::class, 'generateNewCreatividadEstrategiaInnovacion'])->name('herramienta2.generateNewCreatividadEstrategiaInnovacion');
    Route::post('/generar-insight', [Herramienta2Controller::class, 'GenerarInsight'])->name('generar.insight');

    
    Route::get('/asistente-grafica', [AsistenteGraficaController::class, 'index'])->name('asistenteGrafica.index');
    Route::post('/asistente-grafica/generarLogo', [AsistenteGraficaController::class, 'generarLogo'])->name('asistenteGrafica.generarLogo');
    Route::post('/asistente-grafica/generarConceptArt', [AsistenteGraficaController::class, 'generarConceptArt'])->name('asistenteGrafica.generarConceptArt');
    Route::post('/asistente-grafica/generarExperimental', [AsistenteGraficaController::class, 'generarExperimental'])->name('asistenteGrafica.generarExperimental');

    Route::get('/asistente-experimental', [AsistenteExperimentalController::class, 'index'])->name('asistenteExperimental.index');
    Route::post('/asistente-experimental/generarExperimental', [AsistenteExperimentalController::class, 'generarExperimental'])->name('asistenteExperimental.generarExperimental');


    Route::get('/asistente-creativo', [AsistenteCreativoController::class, 'index'])->name('asistenteCreativo.index');
    Route::post('/asistente-creativo/generarPrompt', [AsistenteCreativoController::class, 'generarPrompt'])->name('asistenteCreativo.generarPrompt');
    Route::post('/asistente-creativo/download', [AsistenteCreativoController::class, 'download'])->name('asistente-creativo.download');
    Route::post('/asistente-creativo/guardar', [AsistenteCreativoController::class, 'guardarGenerado'])->name('asistenteCreativo.guardar');

    Route::get('/asistente-social-media', [AsistenteSocialMediaController::class, 'index'])->name('asistenteSocialMedia.index');
    Route::post('/asistente-social-media/generarPrompt', [AsistenteSocialMediaController::class, 'generarPrompt'])->name('asistenteSocialMedia.generarPrompt');
    Route::post('/asistente-social-media/download', [AsistenteSocialMediaController::class, 'download'])->name('asistente-social-media.download');
    Route::post('/asistente-social-media/guardar', [AsistenteSocialMediaController::class, 'guardarGenerado'])->name('asistenteSocialMedia.guardar');

    Route::get('/asistente-innovacion', [AsistenteInnovacionController::class, 'index'])->name('asistenteInnovacion.index');
    Route::post('/asistente-innovacion/generarPrompt', [AsistenteInnovacionController::class, 'generarPrompt'])->name('asistenteInnovacion.generarPrompt');
    Route::post('/asistente-innovacion/download', [AsistenteInnovacionController::class, 'download'])->name('asistente-innovacion.download');

    Route::post('/process-file', [FileProcessingController::class, 'processFile'])->name('process.file');

    Route::post('/getGeneratedBrief', [GeneratedController::class, 'getGeneratedBrief'])->name('getGeneratedBrief');
    Route::post('/getGeneratedGenesis', [GeneratedController::class, 'getGeneratedGenesis'])->name('getGeneratedGenesis');

    Route::resource('plans', PlanController::class);
    Route::get('/chatimage', [PlanController::class, 'mostrarChatimage'])->name('chatimage');

    Route::get('/asistente-generador', [NewGeneradorController::class, 'index'])->name('asistenteGenerador.index');
    Route::get('/generar-videos', [NewGeneradorController::class, 'videos'])->name('generar-videos');
    Route::resource('planServiceLimits', PlanServiceLimitsController::class);
});

Route::get('subscribe', [SubscriptionController::class, 'show'])->name('subscription.show');
//Route::post('subscribe', [SubscriptionController::class, 'createSubscription'])->name('subscription.create');

//Ruta para dashboard de Brief e Investigacion
Route::get('/dashboard/herramienta1', [DashboardController::class, 'indexHerramienta1'])->name('dashboard.herramienta1');
Route::resource('investigacion', InvestigacionController::class);
Route::post('/investigaciongenerada',[InvestigacionController::class,'generarInvestigacion'])->name('investigacion.generarInvestigacion');
Route::get('/investigacion/estado/{generationId}', [InvestigacionController::class, 'consultarEstadoGeneracion'])->name('investigacion.estado');
Route::get('/investigacion/ejecutar/{generationId}', [InvestigacionController::class, 'ejecutarGeneracion'])->name('investigacion.ejecutar');
Route::get('investigacion/{generated}/download', [InvestigacionController::class, 'download'])->name('investigacion.download');

// Ruta para descargar la última investigación de una cuenta
Route::get('investigacion/account/{accountId}/download-last', [InvestigacionController::class, 'downloadLast'])
    ->name('investigacion.downloadLast');

//Ruta para mensaje de subscripcion finalizada
Route::get('/suscripcion/finalizada', [SubscriptionController::class, 'mensajeSubscripcion'])->name('subsfinal');

Route::post('/api/search-perplexity-web', [App\Http\Controllers\API\PerplexityController::class, 'search'])
    ->name('api.search-perplexity-web');

    

require __DIR__.'/auth.php';
