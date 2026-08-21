<?php

use App\Vito\Plugins\Cp6\VitoDeployCaptchaPlugin\Plugin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    (new Plugin)->boot();
});

test('the VitoDeploy login screen contains a CAPTCHA challenge', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('name="captcha"', false)
        ->assertSee(route('vito-captcha.image'), false);
});

test('the CAPTCHA image is generated with private no-cache headers', function () {
    $this->get(route('vito-captcha.image'))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/jpeg')
        ->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('Cache-Control', 'must-revalidate, no-cache, no-store, private');
});

test('an invalid CAPTCHA prevents authentication', function () {
    $this->get(route('vito-captcha.image'))->assertOk();

    $response = $this->from(route('login'))->post(route('login.store'), [
        'email' => $this->user->email,
        'password' => 'password',
        'captcha' => 'definitely-wrong',
    ]);

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHasErrors('captcha');

    $this->assertGuest();
});

test('a correct CAPTCHA passes control to VitoDeploy authentication', function () {
    $this->get(route('vito-captcha.image'))->assertOk();

    $key = session('captcha.key');
    $answer = Cache::get('captcha_'.md5($key));
    $answer = is_array($answer) ? implode('', $answer) : (string) $answer;

    $response = $this->post(route('login.store'), [
        'email' => $this->user->email,
        'password' => 'password',
        'captcha' => $answer,
    ]);

    $response->assertSessionDoesntHaveErrors('captcha');
    $this->assertAuthenticatedAs($this->user);
});
