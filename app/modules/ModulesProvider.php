<?php
namespace App\Modules;

/**
 * ModulesProvider
 */
class ModulesProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * boot
     *
     * @access public
     * @return void
     */
    public function boot(): void
    {
        $modules = config("module.modules");
        while (list(,$module) = each($modules)) {
            if (file_exists(__DIR__.'/'.$module.'/routes.php')) {
                include __DIR__.'/'.$module.'/routes.php';
            }
            if (is_dir(__DIR__.'/'.$module.'/Views')) {
                $this->loadViewsFrom(__DIR__.'/'.$module.'/Views', $module);
            }
        }
    }

    /**
     * register
     */
    public function register()
    {
    }
}
