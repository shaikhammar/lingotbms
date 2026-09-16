<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ReflectionClass;
use RuntimeException;

class BaseModuleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $modulePath = $this->get_module_path();

        if (! $this->app->routesAreCached()) {
            $routesPath = $modulePath.'/routes.php';

            if (file_exists($routesPath)) {
                Route::middleware('web')->group($routesPath);
            }
        }

        $migrationsPath = $modulePath.'/Database/Migrations';

        if (is_dir($migrationsPath)) {
            $this->loadMigrationsFrom($migrationsPath);
        }
    }

    public function register(): void
    {

        $modulePath = $this->get_module_path();
        $configPath = $modulePath.'/config';

        if (is_dir($configPath)) {

            $configFiles = glob($configPath.'/*.php');

            if ($configFiles === false) {
                throw new RuntimeException('Cannot determine config path for '.static::class);
            }

            // Iterate through all PHP files in the config directory
            foreach ($configFiles as $configFile) {
                // Use the file name (without .php) as the config key
                $configKey = basename($configFile, '.php');
                $this->mergeConfigFrom($configFile, $configKey);
            }
        }
    }

    private function get_module_path(): string
    {
        $reflector = new ReflectionClass($this);
        $fileName = $reflector->getFileName();

        if ($fileName === false) {
            throw new RuntimeException('Cannot determine module path for '.static::class);
        }

        return dirname($fileName, 2);

    }
}
