<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$routes = app('router')->getRoutes();
$count = 0;
foreach ($routes as $route) {
    if (str_starts_with($route->uri(), 'app-api/member')) {
        $count++;
        echo $route->methods()[0] . ' ' . $route->uri() . ' => ' . $route->action['controller'] . PHP_EOL;
    }
}
echo PHP_EOL . 'Total member routes: ' . $count . PHP_EOL;