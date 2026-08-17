<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Livewire\Admin\DropdownIndex;
use App\Livewire\AdminDashboard;
use App\Livewire\Admin\SystemSettings\ManageSettings;
use App\Livewire\Sager\SagEditor;
use App\Livewire\Sager\KreditorSagEditor;
use App\Livewire\Sager\KreditorSagerIndex;
use App\Livewire\Sager\Klientinformation;
use App\Livewire\Sager\KreditorSagView;
use App\Livewire\Counter;
use App\Livewire\Users\ManageUsers;
use App\Livewire\ManageRoles;
use App\Livewire\Admin\SagFieldManager;
use App\Livewire\Users\AssignMedarbejder;
use App\Livewire\forms\UserForm;
use App\Livewire\Sager\ShowSager;
use App\Livewire\Sager\ShowKreditorSager;
use App\Livewire\kreditorer\CreateKreditor;
use App\Livewire\kreditorer\ManageKreditor;
use App\Livewire\kreditorer\ManageKreditorer;
use App\Livewire\Users\ManageUser;
use App\Livewire\Sager\StatusPage;
use App\Livewire\kreditorer\UpdateKreditor;
use App\Livewire\Debitorer\ManageDebitorer;
use App\Livewire\Debitorer\UpdateDebitor;
use App\Livewire\Users\CreateUser;
use App\Livewire\Users\Showkreditorusers;
use App\Http\Controllers\sager\ImportExecuteController;
use App\Livewire\Users\UpdateUser;
use App\Livewire\Users\CreateMedarbejderUser;
use App\Livewire\Users\Createkreditoruser;
use App\Livewire\Sagervalgliste\CreateSagervalgliste;
use App\Livewire\Sagervalgliste\ShowSagervalgliste;
use App\Livewire\Sagervalgliste\UpdateSagervalgliste;
use App\Livewire\Sagervalglistetype\CreateSagervalglistetype;
use App\Livewire\Sagervalglistetype\ShowSagervalglistetyper;
use App\Livewire\Sagervalglistetype\ShowSagervalglistetype;
use App\Livewire\Sagervalglistetype\UpdateSagervalglistetype;
use App\Livewire\Sager\AdminSagerFilter;
use App\Livewire\Sager\KreditorCreateSag;
use App\Livewire\Admin\Sager\ImportLogIndex;
use App\Livewire\ManageKreditorFieldSettings;
use App\Livewire\ManageDebitorFieldSettings;
use App\Livewire\ManageKonsulenterFieldSettings;
use App\Livewire\Konsulenter\ManageKonsulenter;
use App\Livewire\Konsulenter\CreateKonsulent;
use App\Livewire\ManageSagsbehandlerFieldSettings;
use App\Livewire\ManageSagerFieldSettings;
use App\Models\Sager;
use App\Models\Debitorer;
use App\Models\Kreditorer;
use App\Models\Konsulenter;
use App\Models\Medarbejdere;
use App\Models\User;
use App\Models\Sagsbehandler;
use App\Livewire\Autotekster\ShowAutotekster;
use App\Livewire\Autotekster\UpdateAutotekst;
use App\Livewire\Autotekster\CreateAutotekst;
use App\Livewire\Status\Index; 
use App\Livewire\Status\UpdateStatus;
use App\Livewire\Status\CreateStatus;
use App\Livewire\KTR\KTRindex;
use App\Livewire\KTR\CreateKTR;
use App\Livewire\KTR\UpdateKTR;
use App\Livewire\KTR\ShowKTR;
use App\Livewire\Bemaerkning\Bemaerkningindex;
use App\Livewire\Bemaerkning\CreateBemaerkning;
use App\Livewire\Bemaerkning\UpdateBemaerkning;
use App\Livewire\Bemaerkning\ShowBemaerkning;
use App\Livewire\Udlaeg\Udlaegindex;
use App\Livewire\Udlaeg\CreateUdlaeg;
use App\Livewire\Udlaeg\UpdateUdlaeg;
use App\Livewire\Udlaeg\ShowUdlaeg;
use App\Livewire\Afslutning\Afslutningindex;
use App\Livewire\Afslutning\CreateAfslutning;
use App\Livewire\Afslutning\UpdateAfslutning;
use App\Livewire\Afslutning\ShowAfslutning;
use App\Http\Controllers\sagerSortController;
use App\Livewire\Admin\FormBuilder;
use App\Livewire\Generated\DynamicFormRenderer;
use App\Livewire\Sager\MergeBrev;
use App\Http\Controllers\sager\ImportPreviewController;
use App\Http\Controllers\sager\ImportUploadController;
use App\Http\Controllers\sager\ImportFormController;
use App\Http\Controllers\sager\ImportSessionController;
use App\Http\Controllers\DokumenterController;
use App\Livewire\Dashboard\MedarbejderDashboard;
use App\Livewire\Kreditor\Dashboard;
use App\Models\Status;
use App\Livewire\SearchConstructor;
use App\Livewire\SavedSearchResults;
use App\Livewire\SagSearch;
use App\Models\FormLayout;
use App\Livewire\Gdpr\SagerRetentionDashboard;
use App\Http\Controllers\GdprScanController;
use App\Livewire\Admin\SagDoctorDashboard;
use App\Livewire\BackupManager;
use App\Livewire\Admin\SystemSecurity;
use App\Livewire\Auth\TwoFactorLogin;
use App\Livewire\Auth\TwoFactorSetupRequired;
use App\Livewire\Tekster\ShowTekster;
use App\Livewire\Kreditor\Search;
use App\Livewire\Autotekster\AutotekstIndex;

/*
|--------------------------------------------------------------------------
| FORSIDE / ROOT
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect('/login');
});

/*
|--------------------------------------------------------------------------
| FÆLLES AUTHENTICATED ROUTES (GÆLDER ALLE LOGGEDE BRUGERE)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    Route::get('/admin/dropdowns', DropdownIndex::class)->name('dropdowns.index');
    
    Route::get('/dashboard', function () {
        $user = auth()->user();

        return match ($user->getRoleNames()->first()) {
            'Admin'       => redirect()->route('admin.dashboard'),
            'Medarbejder' => redirect()->route('medarbejder.dashboard'),
            'Kreditor'    => redirect()->route('kreditor.dashboard'),
            default       => abort(403),
        };
    })->name('dashboard');

    Route::get('/dashboard/admin', AdminDashboard::class)
        ->name('admin.dashboard');

    Route::get('/dashboard/medarbejder', MedarbejderDashboard::class)
        ->name('medarbejder.dashboard');

    Route::post('/keep-alive', function () {
        session(['last_activity' => time()]);
        return response()->json(['status' => 'session extended']);
    });

    Route::post('/re-authenticate', function (Request $request) {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = Auth::user();

        if (! $user && session()->has('last_user_id')) {
            $user = User::find(session('last_user_id'));
        }

        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'success' => true,
                'message' => 'Session genoprettet',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Forkert adgangskode.',
        ], 422);
    });

});

/*
|--------------------------------------------------------------------------
| ADMIN-ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Admin'])
    ->group(function () {

        /* SAGER */
        Route::get('/sager', ShowSager::class)->name('showsager');
        Route::get('/sager/create', SagEditor::class)->name('sager.create');
        Route::get('/sager/{sag}/edit', SagEditor::class)
            ->whereNumber('sag')
            ->name('sager.edit');
        Route::get('/sager/search', SagSearch::class)->name('sager.search');
        Route::get('/admin/sager/import-log', ImportLogIndex::class)->name('sager.import.log');
        
        Route::post('/sager/import/{kreditor}/mapping', [ImportExecuteController::class, 'previewMapping'])
            ->name('sager.import.mapping');

        Route::post('/sager/import/{kreditor}/run', [ImportExecuteController::class, 'run'])
            ->name('sager.import.run');

        Route::get('/sager/import/session/{session}/retry', [ImportExecuteController::class, 'retrySession'])
            ->name('sager.import.session.retry');

        Route::put('/sager/import/templates/{template}', [ImportExecuteController::class, 'updateTemplate'])
                ->name('sager.import.templates.update');

        Route::delete('/sager/import/templates/{template}', [ImportExecuteController::class, 'destroyTemplate'])
            ->name('sager.import.templates.destroy');
        
        /* SYSTEM & SECURITY */
        Route::get('/system-security', SystemSecurity::class)
            ->name('system-security');

        Route::get('/admin/system-settings', ManageSettings::class)
            ->name('admin.system-settings.index');

        /* FIELD SETTINGS */
        Route::prefix('fields')->group(function () {
            Route::get('/kreditor', ManageKreditorFieldSettings::class)->name('fields.kreditor');
            Route::get('/debitor', ManageDebitorFieldSettings::class)->name('fields.debitor');
            Route::get('/konsulenter', ManageKonsulenterFieldSettings::class)->name('fields.konsulenter');
            Route::get('/sagsbehandler', ManageSagsbehandlerFieldSettings::class)->name('fields.sagsbehandler');
            Route::get('/sager', ManageSagerFieldSettings::class)->name('fields.sager');
        });

        /* KREDITORER */
        Route::prefix('kreditorer')->group(function () {
            Route::get('/', ManageKreditorer::class)->name('kreditorer.index');
            Route::get('/create', CreateKreditor::class)->name('kreditorer.create');
            Route::get('/{kreditor}/edit', UpdateKreditor::class)->name('kreditorer.edit');
            Route::get('/{kreditor}', ManageKreditor::class)->name('kreditor.manage')->withTrashed(); 
            Route::get('/{kreditor}/sager', ShowKreditorSager::class)->name('kreditorer.sager');
        });

        /* DEBITORER */
        Route::prefix('debitorer')->group(function () {
            Route::get('/', ManageDebitorer::class)->name('debitorer.index');
            Route::get('/{debitor}/edit', UpdateDebitor::class)->name('debitorer.edit');
        });

        Route::get('/autotekster', AutotekstIndex::class)->name('autotekster.index');

        /* STATUS ROUTES */
        Route::prefix('admin/sager/status')->name('admin.sager.status.')->group(function () {
            Route::get('/', Index::class)->name('index');             
            Route::get('/create', CreateStatus::class)->name('create');      
            Route::get('/{status}', StatusPage::class)->name('show');        
            Route::get('/{status}/edit', UpdateStatus::class)->name('edit'); 
        });

        Route::prefix('/ktr')->name('ktr.')->group(function () {
            Route::get('/', KTRindex::class)->name('index');
            Route::get('/create', CreateKTR::class)->name('create');
            Route::get('/{ktr}', ShowKTR::class)->name('show');
            Route::get('/{ktr}/edit', UpdateKTR::class)->name('edit');
        });

        Route::get('/bemaerkning', BemaerkningIndex::class)->name('bemaerkning.index');
        Route::get('/bemaerkning/create', CreateBemaerkning::class)->name('bemaerkning.create');
        Route::get('/bemaerkning/{bemaerkning}', ShowBemaerkning::class)->name('bemaerkning.show');
        Route::get('/bemaerkning/{bemaerkning}/edit', UpdateBemaerkning::class)->name('bemaerkning.edit');

        Route::get('/udlaeg', UdlaegIndex::class)->name('udlaeg.index');
        Route::get('/udlaeg/create', CreateUdlaeg::class)->name('udlaeg.create');
        Route::get('/udlaeg/{udlaeg}/edit', UpdateUdlaeg::class)->name('udlaeg.edit');

        Route::get('/afslutning', AfslutningIndex::class)->name('afslutning.index');
        Route::get('/afslutning/create', Createafslutning::class)->name('afslutning.create');
        Route::get('/afslutning/{afslutning}/edit', Updateafslutning::class)->name('afslutning.edit');

        Route::prefix('users')->as('users.')->group(function () {
            Route::get('/', ManageUsers::class)->name('index');
            Route::get('/manage-users', ManageUsers::class)->name('manage-users');
            Route::get('/create', CreateUser::class)->name('create');
            Route::get('/{user}', ManageUser::class)->name('user.manage')->withTrashed();
        });

        Route::prefix('konsulenter')->as('konsulenter.')->group(function () {
            Route::get('/', ManageKonsulenter::class)->name('index');
            Route::get('/manage-konsulenter', ManageKonsulenter::class)->name('manage-konsulenter');
            Route::get('/create', CreateKonsulent::class)->name('create');
        });

        Route::get('/manage-roles', ManageRoles::class)->name('roles.index');

        Route::get('/backups', BackupManager::class)->name('backups.index');
        Route::get('/admin/backups', BackupManager::class)->name('admin.backups');
        Route::get('/tekster', ShowTekster::class)->name('tekster.index');
        Route::get('/gdpr/sager-retention', SagerRetentionDashboard::class)->name('gdpr.sager.retention');
        Route::get('/search-constructor', SearchConstructor::class)->name('search-constructor');

        Route::get('/admin/sager/search', function () {
            return view('livewire.admin.sager.search');
        })->name('lukkede.sager.search');

        Route::get('/saved-search/{saved}/results', SavedSearchResults::class)->name('saved-search.results');

        Route::prefix('sager/import')
            ->name('sager.import.')
            ->middleware(['auth', 'verified'])
            ->group(function () {
                Route::get('/{kreditor}', [ImportFormController::class, 'show'])->name('form');
                Route::post('/upload/{kreditor}', [ImportUploadController::class, 'upload'])->name('upload');
                Route::get('/preview/{kreditor}', [ImportPreviewController::class, 'show'])->name('preview');
                Route::post('/execute/{kreditor}', [ImportExecuteController::class, 'run'])->name('execute');
                Route::get('/session/{session}', [ImportSessionController::class, 'show'])->name('session');
                Route::post('/session/{session}/rollback', [ImportExecuteController::class, 'rollback'])->name('session.rollback');
            });

        Route::get('/admin/doctor-norton', SagDoctorDashboard::class)->name('sager.doctor');
        Route::get('/sager/breve/opret', function () {
            return view('admin.breve.opret');
        })->name('sager.breve.opret');
    });

/*
|--------------------------------------------------------------------------
| MEDARBEJDER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:Medarbejder'])
    ->prefix('medarbejder')
    ->name('medarbejder.')
    ->group(function () {
        Route::get('/dashboard', MedarbejderDashboard::class)->name('dashboard');
        Route::get('/sager', ShowSager::class)->name('sager.index');
        Route::get('/sager/create', SagEditor::class)->name('sager.create');
        Route::get('/sager/search', SagSearch::class)->name('sager.search');
        Route::get('/sager/{sag}/edit', SagEditor::class)->whereNumber('sag')->name('sager.edit');

        Route::get('/sager/{sag}/bogholderi', \App\Livewire\Sager\Bogholderi::class)->name('sager.bogholderi');
        Route::get('/sager/{sag}/historik', \App\Livewire\Sager\Historik::class)->name('sager.historik');
        Route::get('/sager/{sag}/klientinformation', Klientinformation::class)->name('sager.klientinformation');

        Route::get('/sager/{sag}/dokumenter', [DokumenterController::class, 'index'])->name('sager.dokumenter.index');
        Route::post('/sager/{sag}/dokumenter', [DokumenterController::class, 'store'])->name('sager.dokumenter.store');
    });

/*
|--------------------------------------------------------------------------
| KREDITOR ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'role:Kreditor'])
    ->prefix('kreditor')
    ->name('kreditor.')
    ->group(function () {
        Route::get('/dashboard', Dashboard::class)->name('dashboard');
        Route::get('/sag/opret', KreditorSagEditor::class)->name('sag.create');
        Route::get('/sager', KreditorSagerIndex::class)->name('sager.index');
        Route::get('/sager/{sag}', KreditorSagView::class)->whereNumber('sag')->name('sag.view');
        Route::get('/sager/{sag}/klientinformation', Klientinformation::class)->name('sager.klientinformation');
        Route::get('/sager/{sag}/dokumenter', [DokumenterController::class, 'index'])->name('sager.dokumenter.index');
        Route::post('/sager/{sag}/dokumenter', [DokumenterController::class, 'store'])->name('sager.dokumenter.store');
        Route::get('/search', Search::class)->name('search');
    });

/*
|--------------------------------------------------------------------------
| DIVERSE HJÆLPE- OG AUTH-ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/release-sag-lock/{id}', function ($id) {
    Cache::forget("sag_lock_$id");
});

Route::get('/sager/papirkurv', \App\Livewire\Sager\Papirkurv::class)->name('sager.trash');

Route::get('/two-factor-login', TwoFactorLogin::class)->name('two-factor.login');
Route::get('/two-factor-setup-required', TwoFactorSetupRequired::class)->name('two-factor.setup-required');

Route::get('/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
});