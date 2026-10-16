<?php

require __DIR__ . '/../../vendor/autoload.php';

// 1. Scan Routes
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$routes = Route::getRoutes();

$routeList = [];
foreach ($routes as $route) {
    $uri = $route->uri();
    $methods = implode('|', $route->methods());
    $actionName = $route->getActionName();
    $name = $route->getName();
    if (strpos($methods, 'GET') !== false && !str_starts_with($uri, '_') && !str_starts_with($uri, 'api/')) {
        $routeList[] = [
            'uri' => '/' . ltrim($uri, '/'),
            'name' => $name,
            'action' => $actionName,
            'middleware' => implode(', ', $route->gatherMiddleware()),
        ];
    }
}

// 2. Scan Blade views for modals, popups, drawers, dialogs
$viewsDir = resource_path('views');
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$modals = [];

foreach ($files as $file) {
    if ($file->isDir() || !str_ends_with($file->getFilename(), '.blade.php')) continue;
    $path = $file->getPathname();
    $relPath = str_replace($viewsDir . DIRECTORY_SEPARATOR, '', $path);
    $content = file_get_contents($path);

    // Look for modal/dialog/drawer IDs or classes
    preg_match_all('/(?:id|class|x-ref|data-modal)\s*=\s*["\']([^"\']*(?:modal|popup|dialog|drawer|dropdown-menu|flyout)[^"\']*)["\']/i', $content, $m);
    if (!empty($m[1])) {
        foreach (array_unique($m[1]) as $item) {
            $modals[$relPath][] = trim($item);
        }
    }
}

echo "=== SCREENS (GET ROUTES) ===\n";
foreach ($routeList as $r) {
    echo "URI: {$r['uri']} | Name: {$r['name']} | Action: {$r['action']}\n";
}

echo "\n=== VIEWS WITH MODALS/POPUPS/DRAWERS ===\n";
foreach ($modals as $view => $items) {
    echo "View: {$view}\n";
    foreach (array_slice($items, 0, 8) as $it) {
        echo "  - {$it}\n";
    }
}
