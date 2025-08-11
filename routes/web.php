<?php
use Illuminate\Support\Facades\Route;
//Admin classes
//General classes
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\Staff\ApprovalsController;
use App\Http\Controllers\Staff\LeaveController;
use App\Http\Controllers\Staff\ProfileController;
use App\Http\Controllers\Farmer\ProfileController as FarmerProfileController;
use App\Http\Controllers\Staff\ImprestsController;
use App\Http\Controllers\Staff\ImprestsSurrenderController;
use App\Http\Controllers\Staff\StoreRequisitionsController;
use App\Http\Controllers\Staff\PurchaseRequisitionsController;
use App\Http\Controllers\Staff\TransportRequisitionsController;
use App\Http\Controllers\Staff\ClaimsController;
use App\Http\Controllers\Staff\HODController;
use App\Http\Controllers\Staff\CEOController;
use App\Http\Controllers\Staff\TrainingController;
use App\Http\Controllers\Staff\AttendanceController;
use App\Http\Controllers\Staff\AppraisalController;
use App\Http\Controllers\Staff\GeneralController as StaffGeneralController;
//Farmer
use App\Http\Controllers\Farmer\GeneralController as FarmerGeneralController;

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
Route::get('/', function(){
    if(session('authUser') != null){
        return redirect('/dashboard');
    }
    return redirect('/login');
});
Route::get('/dashboard',[GeneralController::class,'dashboard']);
Route::post('/send-sms',[GeneralController::class,'sendSMS']);
Route::group(['prefix' => 'staff'], function(){
    Route::get('/dashboard/statistics',[StaffGeneralController::class,'dashboardStatistics']);
    //APPROVAL
    Route::get('/approval/open',[ApprovalsController::class,'openDocuments']);
    // Route::get('/approval/open/{type}',[ApprovalsController::class,'openDocumentsType']);
    Route::get('/approval/approved',[ApprovalsController::class,'approvedDocuments']);
    Route::get('/approval/rejected',[ApprovalsController::class,'rejectedDocuments']);
    Route::get('/approval/view/{docNo}',[ApprovalsController::class,'viewDocument']);
    Route::post('/approval',[ApprovalsController::class,'documentApproval'])->name('documentApproval');
    Route::get('/approval/approvals-count/{type}/{status}',[ApprovalsController::class,'approvalsCount']);
    //LEAVE
    Route::get('/leave',[LeaveController::class,'index']);
    Route::get('/leave/create',[LeaveController::class,'create']);
    Route::post('/leave/store',[LeaveController::class,'store'])->name('storeLeave');
    Route::get('/leave/balance/{type}',[LeaveController::class,'getLeaveBalance']);
    Route::get('/leave/dates/{type}/{days}/{startDate}',[LeaveController::class,'getLeaveDates']);
    Route::get('/leave/show/{no}',[LeaveController::class,'show']);
    Route::get('/leave/edit/{no}',[LeaveController::class,'edit']);
    Route::put('/leave/update',[LeaveController::class,'update'])->name('updateLeave');
    Route::post('/leave/cancel',[LeaveController::class,'cancel'])->name('cancelLeave');
    Route::post('/leave/approval',[LeaveController::class,'approval'])->name('reqLeaveApproval');
    Route::get('/leave/statement',[LeaveController::class,'leaveStatement']);
    Route::post('/leave/statement',[LeaveController::class,'generateLeaveStatement'])->name('generateLeaveStatement');
    //GENERAL
    Route::get('/payslip',[StaffGeneralController::class,'payslip']);
    Route::post('/payslip/generate',[StaffGeneralController::class,'generatePayslip'])->name('generatePayslip');
    Route::get('/payroll-period/year-month/{year}',[StaffGeneralController::class,'prPeriodYearMonths']);
    Route::get('/p-nine',[StaffGeneralController::class,'pNine']);
    Route::post('/p-nine/generate',[StaffGeneralController::class,'generatePNine'])->name('generatePNine');
    //PROFILE
    Route::get('/profile',[ProfileController::class,'show']);
    //REQUISITIONS
    Route::group(['prefix' => 'requisition'], function(){
        /**
         * IMPREST REQUISITION
         */
        //Imprest Header
        Route::get('/imprest',[ImprestsController::class,'index']);
        Route::get('/imprest/create/header',[ImprestsController::class,'createHeader']);
        Route::post('/imprest/store/header',[ImprestsController::class,'storeHeader'])->name('storeImprestHeader');
        Route::get('/imprest/show/header/{no}',[ImprestsController::class,'showHeader']);
        Route::get('/imprest/edit/header/{no}',[ImprestsController::class,'editHeader']);
        Route::put('/imprest/update/header',[ImprestsController::class,'storeHeader'])->name('updateImprestHeader');
        Route::post('/imprest/approval/request',[ImprestsController::class,'requestApproval'])->name('requestImprestApproval');
        Route::post('/imprest/approval/cancel',[ImprestsController::class,'cancel'])->name('cancelImprest');
        Route::get('/imprest-header-details/{imprest}',[ImprestsController::class,'imprestHeaderDesc']);
        //Imprest lines
        Route::get('/imprest/line/{id}',[ImprestsController::class,'imprest-lines']);
        Route::get('/imprest/create/line/{header}',[ImprestsController::class,'createLine']);
        Route::post('/imprest/store/line',[ImprestsController::class,'storeLine'])->name('storeImprestLine');
        Route::delete('/imprest/delete/line',[ImprestsController::class,'deleteLine'])->name('deleteImprestLine');
        //Imprest Surrender
        Route::get('/imprest-surrender',[ImprestsSurrenderController::class,'index']);
        Route::get('/imprest-surrender/create/header',[ImprestsSurrenderController::class,'createHeader']);
        Route::get('/imprest-surrender/show/header/{no}',[ImprestsSurrenderController::class,'showHeader']);
        Route::post('/imprest-surrender/create/header',[ImprestsSurrenderController::class,'storeHeader'])->name('storeImprestSurrenderHeader');
        Route::put('/imprest-surrender/create/header',[ImprestsSurrenderController::class,'updateLines'])->name('updateImprestSurrenderLines');
        Route::post('imprest-surrender/approval/request',[ImprestsSurrenderController::class,'requestApproval'])->name('requestImprestSurrenderApproval');
        Route::post('imprest-surrender/approval/cancel',[ImprestsSurrenderController::class,'cancel'])->name('cancelImprestSurrender');
        /**
         * STORE REQUISITION
         */
        //Store Requisition Header
        Route::get('/store',[StoreRequisitionsController::class,'index']);
        Route::get('/store/create/header',[StoreRequisitionsController::class,'createHeader']);
        Route::post('/store/store/header',[StoreRequisitionsController::class,'storeHeader'])->name('storeStoreReqHeader');
        Route::get('/store/show/header/{no}',[StoreRequisitionsController::class,'showHeader']);
        Route::get('/store/edit/header/{no}',[StoreRequisitionsController::class,'editHeader']);
        Route::put('/store/update/header',[StoreRequisitionsController::class,'storeHeader'])->name('updateStoreReqHeader');
        Route::post('/store/approval/request',[StoreRequisitionsController::class,'requestApproval'])->name('requestStoreReqApproval');
        Route::post('/store/approval/cancel',[StoreRequisitionsController::class,'cancel'])->name('cancelStoreReq');
        //Store Requisition lines
        Route::get('/store/line/{id}',[StoreRequisitionsController::class,'store-lines']);
        Route::get('/store/create/line/{header}',[StoreRequisitionsController::class,'createLine']);
        Route::post('/store/store/line',[StoreRequisitionsController::class,'storeLine'])->name('storeStoreReqLine');
        // Route::get('/store/edit/line/{no}',[StoreRequisitionsController::class,'editLine']);
        // Route::put('/store/update/line',[StoreRequisitionsController::class,'storeLine'])->name('updateStoreReqLine');
        Route::delete('/store/delete/line',[StoreRequisitionsController::class,'deleteLine'])->name('deleteStoreReqLine');
        /**
         * PURCHASE REQUISITION
         */
        //Purchase Requisition Header
        Route::get('/purchase',[PurchaseRequisitionsController::class,'index']);
        Route::get('/purchase/create/header',[PurchaseRequisitionsController::class,'createHeader']);
        Route::post('/purchase/store/header',[PurchaseRequisitionsController::class,'storeHeader'])->name('storePurchaseReqHeader');
        Route::get('/purchase/show/header/{no}',[PurchaseRequisitionsController::class,'showHeader']);
        Route::get('/purchase/edit/header/{no}',[PurchaseRequisitionsController::class,'editHeader']);
        Route::put('/purchase/update/header',[PurchaseRequisitionsController::class,'storeHeader'])->name('updatePurchaseReqHeader');
        Route::post('/purchase/approval/request',[PurchaseRequisitionsController::class,'requestApproval'])->name('requestPurchaseReqApproval');
        Route::post('/purchase/cancel',[PurchaseRequisitionsController::class,'cancel'])->name('cancelPurchaseReq');
        //Purchase Requisition lines
        Route::get('/purchase/line/{id}',[PurchaseRequisitionsController::class,'store-lines']);
        Route::get('/purchase/create/line/{header}',[PurchaseRequisitionsController::class,'createLine']);
        Route::post('/purchase/store/line',[PurchaseRequisitionsController::class,'storeLine'])->name('storePurchaseReqLine');
        // Route::get('/purchase/edit/line/{no}',[PurchaseRequisitionsController::class,'editLine']);
        // Route::put('/purchase/update/line',[PurchaseRequisitionsController::class,'storeLine'])->name('updatePurchaseReqLine');
        Route::delete('/purchase/delete/line',[PurchaseRequisitionsController::class,'deleteLine'])->name('deletePurchaseReqLine');
        /**
         * TRANSPORT REQUISITION
         */
        //Transport Requisition Header
        Route::get('/transport',[TransportRequisitionsController::class,'index']);
        Route::get('/transport/create',[TransportRequisitionsController::class,'create']);
        Route::post('/transport/store',[TransportRequisitionsController::class,'store'])->name('storeTransportReqHeader');
        Route::get('/transport/show/{no}',[TransportRequisitionsController::class,'show']);
        Route::get('/transport/edit/{no}',[TransportRequisitionsController::class,'edit']);
        Route::put('/transport/update',[TransportRequisitionsController::class,'store'])->name('updateTransportReqHeader');
        Route::post('/transport/approval/request',[TransportRequisitionsController::class,'requestApproval'])->name('requestTransportReqApproval');
        Route::post('/transport/approval/cancel',[TransportRequisitionsController::class,'cancel'])->name('cancelTransportReq');
        //CLAIM HEADER
        Route::get('/claim',[ClaimsController::class,'index']);
        Route::get('/claim/create/header',[ClaimsController::class,'createHeader']);
        Route::post('/claim/store/header',[ClaimsController::class,'storeHeader'])->name('storeClaimHeader');
        Route::get('/claim/show/header/{no}',[ClaimsController::class,'showHeader']);
        Route::get('/claim/edit/header/{no}',[ClaimsController::class,'editHeader']);
        Route::put('/claim/update/header',[ClaimsController::class,'storeHeader'])->name('updateClaimHeader');
        Route::post('/claim/approval/request',[ClaimsController::class,'requestApproval'])->name('requestClaimApproval');
        Route::post('/claim/approval/cancel',[ClaimsController::class,'cancel'])->name('cancelClaim');
        Route::get('/claim-header-details/{claim}',[ClaimsController::class,'claimHeaderDesc']);
        Route::get('/claim/receipt/{header}/{line}',[ClaimsController::class,'LineReceipt']);
        //CLAIM LINES
        Route::get('/claim/line/{id}',[ClaimsController::class,'claim-lines']);
        Route::get('/claim/create/line/{header}',[ClaimsController::class,'createLine']);
        Route::post('/claim/store/line',[ClaimsController::class,'storeLine'])->name('storeClaimLine');
        Route::delete('/claim/delete/line',[ClaimsController::class,'deleteLine'])->name('deleteClaimLine');
        //TRAINING HEADER
        Route::get('/training',[TrainingController::class,'index']);
        Route::get('/training/create/header',[TrainingController::class,'createHeader']);
        Route::post('/training/store/header',[TrainingController::class,'storeHeader'])->name('storeTrainingHeader');
        Route::get('/training/show/header/{no}',[TrainingController::class,'showHeader']);
        Route::get('/training/edit/header/{no}',[TrainingController::class,'editHeader']);
        Route::put('/training/update/header',[TrainingController::class,'storeHeader'])->name('updateTrainingHeader');
        Route::post('/training/approval/request',[TrainingController::class,'requestApproval'])->name('requestTrainingApproval');
        Route::post('/training/approval/cancel',[TrainingController::class,'cancel'])->name('cancelTraining');
        Route::get('/training-header-details/{training}',[TrainingController::class,'trainingHeaderDesc']);

    });
    //REQUISITIONS
    Route::group(['prefix' => 'hod'], function(){
        Route::get('/employee',[HODController::class,'staff']);
        Route::get('/employee/{no}',[HODController::class,'employeeDetails']);
    });
    Route::get('/get-items',[StaffGeneralController::class,'getItems']);
    Route::get('/get-store-items/{store}',[StaffGeneralController::class,'getStoreItems']);
    Route::get('/get-services',[StaffGeneralController::class,'getServices']);
    Route::get('/get-assets',[StaffGeneralController::class,'getAssets']);
    Route::get('/get-item-balance/{item}/{store}',[StaffGeneralController::class,'getItemBalance']);
    //CEO
    Route::group(['prefix' => 'ceo'], function(){
        Route::get('/master-roll',[CEOController::class,'masterRollReport']);
        Route::post('/master-roll/generate',[CEOController::class,'generateMasterRollReport'])->name('generateMasterRollReport');
    });
    //GENERAL
    Route::get('/attendance',[AttendanceController::class,'index']);
    Route::post('/check-in-out',[AttendanceController::class,'checkInCheckoutToday'])->name('checkinCheckout');
    //Appraisal
    Route::group(['prefix' => 'appraisal'], function(){
        Route::get('/',[AppraisalController::class,'index']);
        Route::get('/show/header/{no}',[AppraisalController::class,'showHeader']);
        Route::get('/edit/score/{no}/{area}/{measure}',[AppraisalController::class,'editScore']);
        Route::post('/store/score',[AppraisalController::class,'storeAppraisalScore'])->name('storeAppraisalScore');
        Route::post('/submit/appraisal',[AppraisalController::class,'submitAppraisal'])->name('submitAppraisal');
    });
});
Route::get('/logout',[GeneralController::class,'logout']);
Route::get('/send-emails',[GeneralController::class,'sendEmails']);
//clearing cache
Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    return view('auth.login');
});
//create a symlink to storage folder
Route::get('/storage-link', function() {
    Artisan::call('storage:link');
    return view('auth.login');
});
require __DIR__.'/auth.php';
