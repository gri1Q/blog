<?php

use MyProject\Exceptions\NotFoundException;
use MyProject\View\View;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
try {
    spl_autoload_register(function (string $className) {
        require_once __DIR__ . '/../src/' . $className . '.php';
    });
    $route = $_GET['route'];
    // var_dump($_GET);
    $routes = require __DIR__ . '/../src/route.php';
    $isRouteFound = false;
    foreach ($routes as $patter => $controllerAndAction) {

        preg_match($patter, $route, $mathches);
        // var_dump($mathches);
        if (!empty($mathches)) {
            $isRouteFound = true;
            break;
        }
    }
    // ini_set('error_reporting', E_ALL);
    // ini_set('display_errors', 1);
    // ini_set('display_startup_errors', 1);

    if (!$isRouteFound) {
        // echo "Страница не найдена";
        throw new \MyProject\Exceptions\NotFoundException();
        return;
    }

    unset($mathches[0]);
    $controller = new $controllerAndAction[0];
    $action = $controllerAndAction[1];
    $controller->$action(...$mathches);
} catch (\MyProject\Exceptions\DbException $e) {
    // echo $e->getMessage();
    $view = new View(__DIR__ . '/../templates/errors');
    $view->renderHtml('500.php', ['error' => $e->getMessage()], 500);
    // var_dump(__DIR__);
} catch (NotFoundException $e) {
    $view = new View(__DIR__ . '/../templates/errors');
    $view->renderHtml('404.php', [], 404);
}
