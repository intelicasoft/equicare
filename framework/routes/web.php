<?php
use App\Http\Controllers\LaravelWebInstaller;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\HospitalController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\BreakdownController;
use App\Http\Controllers\PreventiveController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\CalibrationController;
use App\Http\Controllers\CostController;
use App\Http\Controllers\StickerController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\QrController;
use App\Http\Controllers\QrScanController;
use App\Http\Controllers\DataBrandController;
use App\Http\Controllers\DataAccesoriesController;
use App\Http\Controllers\DataLotsController;
use App\Http\Controllers\DataModelsController;
use App\Http\Controllers\DataProvidersController;
use App\Http\Controllers\DataZonesController;
use App\Http\Controllers\ServiceOrdersController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\ReviewsController;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

Auth::routes();
Route::get('/clear-optimize-cache', function () {
    Artisan::call('optimize:clear');
    return 'Optimization cache cleared successfully';
});
 Route::get('php-info', function() {
   		 return phpinfo();
	});

Route::get('install', [LaravelWebInstaller::class,'index']);
Route::post('installed', [LaravelWebInstaller::class,'install']);
Route::get('installed', [LaravelWebInstaller::class,'index']);
Route::get('migrate', [LaravelWebInstaller::class,'db_migration']);
Route::get('migration', [LaravelWebInstaller::class,'migration']);
Route::get('/equipments/history/{equipment}', [EquipmentController::class,'history'])->name('equipments.history');
Route::get('/scan/qr/{equipment}', [EquipmentController::class,'history_qr']);

//last_id hq
Route::get('scan/qr/hq/{id}',[EquipmentController::class,'hq_history_qr']);


Route::group(['middleware' => ['installed_or_not', 'auth']], function () {
    Route::get('/', [HomeController::class,'index'])->name('home');
    Route::get('/home', function () {
        return redirect('/admin/dashboard');
    });
    Route::get('/admin/dashboard', [HomeController::class,'index'])->name('home');

    Route::get('admin/change-password', [ChangePasswordController::class,'changePassword'])->name('change-password');
    Route::post('admin/change-password', [ChangePasswordController::class,'updatePassword'])->name('change-password.store');

    Route::resource('admin/users', UserController::class);
    Route::resource('admin/roles', RoleController::class);
    Route::resource('admin/permissions', PermissionController::class);
    Route::resource('admin/hospitals', HospitalController::class);

    Route::resource('admin/brands', DataBrandController::class);

    Route::resource('admin/accesories', DataAccesoriesController::class);

    Route::resource('admin/lots', DataLotsController::class);

    Route::resource('admin/models', DataModelsController::class);

    Route::resource('admin/providers', DataProvidersController::class);

    Route::resource('admin/zones', DataZonesController::class);

    Route::resource('admin/orders', ServiceOrdersController::class);
    Route::get('orders/reportes_pdf',[ServiceOrdersController::class,'reportes_pdf'])->name('orders.reportes_pdf');
    Route::get('orders/pdf/{id}',[ServiceOrdersController::class,'pdf'])->name('orders.pdf');
    Route::get('orders/updateStatus/{id}', [ServiceOrdersController::class, 'updateStatus'])->name('orders.updateStatus');

    Route::resource('admin/tickets', TicketsController::class);

    Route::resource('admin/reviews', ReviewsController::class);

    Route::get('/admin/equipment/qr/{id}', [EquipmentController::class,'qr'])->name('equipments.qr');
    Route::get('/admin/equipment/qr-image/{id}', [EquipmentController::class,'qr_image'])->name('equipments.qrimage');
    Route::get('/admin/equipments/qr/regen', [EquipmentController::class,'regenerate_all_qr'])->name('equipments.regen');

    // Route::get('/admin/equipments/render_etiqueta/{id}', [EquipmentController::class,'render_etiqueta'])->name('equipments.render_etiqueta');

    Route::get('/admin/equipments', [EquipmentController::class,'index'])->name('equipments.index');
    Route::post('/admin/equipments', [EquipmentController::class,'store'])->name('equipments.store');
    Route::get('/admin/equipments/create', [EquipmentController::class,'create'])->name('equipments.create');
    Route::delete('/admin/equipments/{equipment}', [EquipmentController::class,'destroy'])->name('equipments.destroy');
    Route::patch('/admin/equipments/{equipment}', [EquipmentController::class,'update'])->name('equipments.update');
    Route::get('/admin/equipments/{equipment}/edit', [EquipmentController::class,'edit'])->name('equipments.edit');
    
    Route::get('/admin/equipments/{equipment}', [EquipmentController::class,'etiqueta'])->name('equipments.etiqueta');


    Route::resource('admin/departments', DepartmentController::class);
    Route::resource('admin/call/breakdown_maintenance', BreakdownController::class);
    Route::get('admin/call/breakdown_maintenance/attend/{id}', [BreakdownController::class,'attend_call_get']);
    Route::post('admin/call/breakdown_maintenance/attend', [BreakdownController::class,'attend_call'])->name('breakdown_attend_call');
    Route::get('admin/call/breakdown_maintenance/call_complete/{id}', [BreakdownController::class,'call_complete_get']);
    Route::post('admin/call/breakdown_maintenance/call_complete', [BreakdownController::class,'call_complete'])->name('breakdown_call_complete');

    Route::get('admin/call/preventive_maintenance/attend/{id}', [PreventiveController::class,'attend_call_get']);
    Route::post('admin/call/preventive_maintenance/attend', [PreventiveController::class,'attend_call'])->name('preventive_attend_complete');;
    Route::get('admin/call/preventive_maintenance/call_complete/{id}', [PreventiveController::class,'call_complete_get']);
    Route::post('admin/call/preventive_maintenance/call_complete', [PreventiveController::class,'call_complete'])->name('preventive_call_complete');
    

    # Breakdown call routes
    Route::get('unique_id_breakdown', [BreakdownController::class,'ajax_unique_id']);
    Route::get('hospital_breakdown', [BreakdownController::class,'ajax_hospital_change']);
    Route::get('department_breakdown', [BreakdownController::class,'ajax_department_change']);

    # preventive call routes
    Route::get('unique_id_preventive', [PreventiveController::class,'ajax_unique_id']);
    Route::get('hospital_preventive', [PreventiveController::class,'ajax_hospital_change']);
    Route::get('department_preventive', [PreventiveController::class,'ajax_department_change']);
    Route::get('call_complete_preventive_new_item', [PreventiveController::class,'ajax_new_item']);
    Route::post('call_complete_preventive_new_item', [PreventiveController::class,'ajax_new_item_post']);

    Route::get('admin/reports/time_indicator', [ReportController::class,'time_indicator']);
    Route::get('admin/reports/time_indicator/filter', [ReportController::class,'time_indicator_filter']);
    Route::get('admin/reports/time_indicator/ajax_equipment_based_on_hospital', [ReportController::class,'ajax_to_get_equipment']);
    Route::get('admin/reports/equipments', [ReportController::class,'equipment_report']);
    Route::get('admin/reports/equipments/filter', [ReportController::class,'equipment_report_post'])->name('equipment_report_post');
    Route::get('admin/reminder/preventive_maintenance', [ReminderController::class,'preventive_reminder']);
    Route::get('admin/reminder/calibration', [ReminderController::class,'calibrations_reminder']);
    Route::resource('admin/calibration', CalibrationController::class);
    Route::resource('admin/call/preventive_maintenance', PreventiveController::class);
    Route::get('/payments/excel',
        [
            'as' => 'excel',
            'uses' => 'ReportController@excel_export_equipment',
        ]);
    Route::resource('admin/maintenance_cost', CostController::class);
    Route::post('admin/maintenance_cost/get_info', [CostController::class,'get_info'])->name('maintenance_cost.get_info');
    Route::get('get_equipment', [CostController::class,'get_equipment']);
    Route::get('admin/settings', [SettingController::class,'index']);
    Route::post('admin/settings', [SettingController::class,'post']);
    Route::post('admin/mail-settings', [SettingController::class,'mailSettings']);
    Route::get('admin/delete_logo/{logo}', [SettingController::class,'deleteLogo']);
    Route::get('admin/calibrations_sticker/get_equipment', [StickerController::class,'get_equipment_ajax']);
    Route::get('admin/calibrations_sticker', [StickerController::class,'index'])->name('admin.calibration.index');
    Route::get('admin/calibrations_sticker2', [StickerController::class,'post'])->name('admin/calibrations_sticker2');
    Route::get('admin/calibrations_sticker/{id}', [StickerController::class,'single_sticker']);
    //date settings
    Route::post('admin/date-settings',[SettingController::class,'date_settings'])->name('date_settings');
    Route::get('admin/qr',[QrController::class,'index'])->name('qr.index');
    Route::get('admin/qr-assigned',[QrController::class,'qr_assigned'])->name('qr-assigned');
    Route::get('admin/qr-create',[QrController::class,'create'])->name('qr.create');
    Route::delete('admin/qr-delete/{id}',[QrController::class,'destroy'])->name('qr.delete');
    Route::post('admin/qr-generate',[QrController::class,'qr_generate'])->name('qr_generate');
    Route::get('admin/qr-scan',[QrScanController::class,'index'])->name('qr_scan.index');
    Route::get('admin/qr-scan/{id}',[QrScanController::class,'assign_equipment'])->name('qr_scan.index');
    Route::get('admin/qr-sticker/{type}',[QrController::class,'qr_sticker'])->name('qr_sticker');
    Route::get('admin/breakdown-export/{type}', [BreakdownController::class, 'export'])->name('breakdown.export');
    Route::get('admin/preventive-export/{type}', [PreventiveController::class, 'export'])->name('preventive.export');
    Route::post('admin/role-permissions',[RoleController::class, 'role_permissions'])->name('role_permissions');
    Route::post('admin/user-permissions',[UserController::class, 'user_permissions'])->name('user_permissions');
  
   
});
