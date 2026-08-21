<?php

namespace App\Vito\Plugins\Cp6\VitoDeployCaptchaPlugin\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class ValidateLoginCaptcha
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('vito-captcha.enabled', true)) {
            return $next($request);
        }

        $request->validate(
            ['captcha' => ['bail', 'required', 'string', 'captcha']],
            [
                'captcha.required' => 'Enter the security code shown in the image.',
                'captcha.captcha' => (string) config(
                    'vito-captcha.error_message',
                    'The security code is incorrect. Please try the new code.'
                ),
            ],
            ['captcha' => 'security code'],
        );

        return $next($request);
    }
}
