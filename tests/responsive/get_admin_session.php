<?php

require __DIR__ . '/../../vendor/autoload.php';
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$consoleKernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$consoleKernel->bootstrap();

$admin = \App\Models\User::where('role', 'admin')->first();
if (!$admin) {
    $admin = \App\Models\Admin::first();
}

$request = \Illuminate\Http\Request::create('/dashboard', 'GET');
$request->setLaravelSession($app['session']->driver());

$response = (new \Illuminate\Pipeline\Pipeline($app))
    ->send($request)
    ->through([
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
        \Illuminate\Session\Middleware\StartSession::class,
    ])
    ->then(function ($req) use ($admin) {
        \Illuminate\Support\Facades\Auth::guard('web')->login($admin, true);
        \Illuminate\Support\Facades\Auth::guard('admin')->login($admin, true);
        $req->session()->put('user_id', $admin->id);
        $req->session()->put('admin_id', $admin->id);
        $req->session()->save();
        return response('OK');
    });

$cookieList = [];
foreach ($response->headers->getCookies() as $cookie) {
    $cookieList[] = [
        'name' => $cookie->getName(),
        'value' => $cookie->getValue(),
        'domain' => '127.0.0.1',
        'path' => '/',
        'httpOnly' => $cookie->isHttpOnly(),
        'secure' => false,
    ];
}

echo json_encode([
    'user' => [
        'id' => $admin->id,
        'name' => $admin->name ?? $admin->full_name,
        'email' => $admin->email,
    ],
    'cookies' => $cookieList,
], JSON_PRETTY_PRINT);
