<?php

namespace App\Vito\Plugins\Cp6\VitoDeployCaptchaPlugin\Http\Controllers;

use Illuminate\Http\Response;
use Mews\Captcha\Captcha;

final class CaptchaImageController
{
    public function __invoke(Captcha $captcha): Response
    {
        /** @var Response $response */
        $response = $captcha->create('vito-login');

        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        return $response;
    }
}
