<?php
namespace Modules\DoniaPay\Providers;
use Illuminate\Support\ServiceProvider;
class DoniaPayServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/doniapay.php', 'doniapay');
    }
    public function boot()
    {
        if (file_exists(__DIR__.'/../routes.php')) {
            $this->loadRoutesFrom(__DIR__.'/../routes.php');
        }
    }
}
