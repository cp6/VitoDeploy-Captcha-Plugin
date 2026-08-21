<?php

namespace App\Vito\Plugins\Cp6\VitoDeployCaptchaPlugin;

use App\Plugins\AbstractPlugin;
use App\Plugins\RegisterViews;
use App\Vito\Plugins\Cp6\VitoDeployCaptchaPlugin\Http\Controllers\CaptchaImageController;
use App\Vito\Plugins\Cp6\VitoDeployCaptchaPlugin\Http\Middleware\ValidateLoginCaptcha;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Fortify;
use Mews\Captcha\Captcha;
use Mews\Captcha\CaptchaServiceProvider;
use RuntimeException;

class Plugin extends AbstractPlugin
{
    protected string $name = 'Login CAPTCHA';

    protected string $description = 'Protects the VitoDeploy login form with a self-hosted image CAPTCHA.';

    public function boot(): void
    {
        $this->assertRequirements();
        $this->registerConfiguration();

        if (! config('vito-captcha.enabled', true)) {
            return;
        }

        $this->ensureCaptchaProviderIsRegistered();

        RegisterViews::make('vito-captcha')
            ->path(__DIR__.'/resources/views')
            ->register();

        $this->registerCaptchaRoute();
        $this->protectLoginRoute();

        Fortify::loginView(fn () => view()->file(__DIR__.'/resources/views/login.blade.php'));
    }

    public function install(): void
    {
        $this->assertRequirements();
    }

    public function enable(): void
    {
        $this->assertRequirements();
    }

    private function assertRequirements(): void
    {
        if (! class_exists(Captcha::class)) {
            throw new RuntimeException(
                'The mews/captcha dependency is missing. Run `composer require mews/captcha:^3.5` in the VitoDeploy application before installing this plugin.'
            );
        }

        if (! extension_loaded('gd')) {
            throw new RuntimeException('The PHP GD extension is required by mews/captcha.');
        }
    }

    private function registerConfiguration(): void
    {
        $defaults = require __DIR__.'/config/vito-captcha.php';
        $configuration = $this->normalizeConfiguration(
            array_replace_recursive($defaults, config('vito-captcha', []))
        );

        config(['vito-captcha' => $configuration]);
        config([
            'captcha.characters' => str_split((string) $configuration['characters']),
            'captcha.vito-login' => $configuration['image'],
        ]);
    }

    private function ensureCaptchaProviderIsRegistered(): void
    {
        if (! app()->bound('captcha')) {
            app()->register(CaptchaServiceProvider::class);
        }

        if (! app()->bound('captcha')) {
            throw new RuntimeException('mews/captcha was found but its Laravel service provider could not be registered.');
        }
    }

    private function registerCaptchaRoute(): void
    {
        $throttle = 'throttle:'.config('vito-captcha.rate_limit', 30).',1';

        Route::middleware(['web', $throttle])
            ->get('/vito-captcha/image', CaptchaImageController::class)
            ->name('vito-captcha.image');

        // mews/captcha registers two generic image endpoints. Apply the same
        // throttle so they cannot be used to bypass this plugin's CPU limit.
        foreach (Route::getRoutes()->getRoutes() as $route) {
            if (in_array($route->uri(), ['captcha/{config?}', 'captcha/api/{config?}'], true)) {
                $route->middleware($throttle);
            }
        }

        // Plugins boot after VitoDeploy's routes have been loaded. Laravel may
        // already have built its route-name lookup by then, so refresh it after
        // registering this late route.
        Route::getRoutes()->refreshNameLookups();
        Route::getRoutes()->refreshActionLookups();
    }

    private function protectLoginRoute(): void
    {
        $loginRoute = Route::getRoutes()->getByName('login.store');

        if ($loginRoute === null) {
            throw new RuntimeException('The VitoDeploy login route could not be found. This VitoDeploy version may not be supported.');
        }

        $loginRoute->middleware(ValidateLoginCaptcha::class);
    }

    /**
     * @param  array<string, mixed>  $configuration
     * @return array<string, mixed>
     */
    private function normalizeConfiguration(array $configuration): array
    {
        $characters = (string) $configuration['characters'];
        if (strlen($characters) < 2) {
            throw new RuntimeException('VITO_CAPTCHA_CHARACTERS must contain at least two characters.');
        }

        /** @var array<string, mixed> $image */
        $image = $configuration['image'];
        $image['length'] = $image['math'] ? 9 : max(3, min(9, (int) $image['length']));
        $image['width'] = max(120, min(480, (int) $image['width']));
        $image['height'] = max(36, min(120, (int) $image['height']));
        $image['quality'] = max(50, min(100, (int) $image['quality']));
        $image['expire'] = max(30, min(600, (int) $image['expire']));
        $image['angle'] = max(0, min(30, (int) $image['angle']));
        $image['lines'] = max(0, min(10, (int) $image['lines']));

        $configuration['rate_limit'] = max(5, min(120, (int) $configuration['rate_limit']));
        $configuration['image'] = $image;

        return $configuration;
    }
}
