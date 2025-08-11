<?php

namespace App\Providers;
use Illuminate\Support\ServiceProvider;
use App\Models\ApprovalEntry;
use Illuminate\Support\Facades\Schema;
use App\Traits\WebServicesTrait;
use App\CustomClasses\NTLM\NTLMSoapClient;

class AppServiceProvider extends ServiceProvider
{
    use WebServicesTrait;
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        // view()->composer(['inc.staff.sidebar'],function($view) {
        //     $data = array(
        //     'pendingApprovalsCount' => $this->odataClient()->from(ApprovalEntry::wsName())->where('Status','Open')->where('Approver_ID',session('authUser')['userID'])->count(),
        //     );
        //     $view->with($data);
        // });
    }
}
