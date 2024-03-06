<?php

namespace ezeasorekene\App\Core;

use ReflectionMethod;
use ReflectionException;
use ezeasorekene\App\Core\System\Behaviour;
use ezeasorekene\App\Core\Middleware\Request;
use ezeasorekene\App\Core\Middleware\AppLogger;

class App
{

    protected $controller = 'errors';

    protected $method = 'index';

    protected $api = 'api';

    protected $dir;

    protected $params = [];

    public function __construct()
    {
        $url = Behaviour::parseUrl();

        $this->maintenance();

        if ($url[0] == 'api' && isset($url[1])) {
            if (is_dir(dirname(__DIR__) . '/controllers/' . $url[0] . '/' . $url[1])) {
                $url[2] = isset($url[2]) ? $url[2] : $this->method;

                if (file_exists(dirname(__DIR__) . '/controllers/' . $url[0] . '/' . $url[1] . '/' . $url[2] . '.php')) {
                    $this->api = $url[0];
                    $this->dir = $url[1];
                    $this->controller = $url[2];
                    unset($url[0]);
                    unset($url[1]);
                    unset($url[2]);
                } else {
                    $log = new AppLogger('core.app.api');
                    $log->logInfo("Controller file does not exist", ['folderName' => $url[1], 'fileName' => $url[2]]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }
                    Request::apiResponse([
                        'code' => 404,
                        'message' => 'Requested endpoint does not exist',
                        'requestData' => [
                            'method' => $_SERVER['REQUEST_METHOD'],
                            'endpoint' => $endpoint,
                        ]
                    ], [
                        'code' => 404,
                    ]);
                    return;
                }

                require_once(dirname(__DIR__) . '/controllers/' . $this->api . '/' . $this->dir . '/' . $this->controller . '.php');

                try {
                    $this->controller = new $this->controller;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app.api');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }
                    Request::apiResponse([
                        'code' => 503,
                        'message' => 'Service unavailable at the moment. Please contact support',
                        'method' => $_SERVER['REQUEST_METHOD'],
                        'endpoint' => $endpoint,
                    ], [
                        'code' => 503,
                    ]);
                    return;
                }

                if (isset($url[3])) {
                    if (method_exists($this->controller, $url[3])) {
                        $this->method = $url[3];
                        unset($url[3]);
                    } else {
                        $log = new AppLogger('core.app.api');
                        $log->logInfo("Method does not exist", ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $url[3]]);
                        $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                        if (isset($_REQUEST['url'])) {
                            unset($_REQUEST['url']);
                        }
                        Request::apiResponse([
                            'code' => 404,
                            'message' => 'Requested endpoint does not exist..',
                            'requestData' => [
                                'method' => $_SERVER['REQUEST_METHOD'],
                                'endpoint' => $endpoint,
                            ]
                        ], [
                            'code' => 404,
                        ]);
                        return;
                    }
                }

                try {
                    $reflection = new ReflectionMethod($this->controller, $this->method);
                    if ($reflection->isPublic()) {
                        $this->params = $url ? array_values($url) : [];
                        call_user_func_array([$this->controller, $this->method], $this->params);
                    } else {
                        $log = new AppLogger('core.app.api');
                        $log->logInfo("Method is not a public method", ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                        $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                        if (isset($_REQUEST['url'])) {
                            unset($_REQUEST['url']);
                        }

                        Request::apiResponse([
                            'code' => 403,
                            'message' => 'Access to the requested resource is forbidden on this server',
                            'requestData' => [
                                'method' => $_SERVER['REQUEST_METHOD'],
                                'endpoint' => $endpoint,
                            ]
                        ], [
                            'code' => 403,
                        ]);
                        return;
                    }
                } catch (ReflectionException $th) {
                    $log = new AppLogger('core.app.api');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }

                    Request::apiResponse([
                        'code' => 404,
                        'message' => 'Requested endpoint does not exist',
                        'requestData' => [
                            'method' => $_SERVER['REQUEST_METHOD'],
                            'endpoint' => $endpoint,
                        ]
                    ], [
                        'code' => 403,
                    ]);
                    return;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app.api');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }

                    Request::apiResponse([
                        'code' => 503,
                        'message' => 'Service unavailable at the moment. Please contact support',
                        'requestData' => [
                            'method' => $_SERVER['REQUEST_METHOD'],
                            'endpoint' => $endpoint,
                        ]
                    ], [
                        'code' => 503,
                    ]);
                    return;
                }

            } else {

                if (file_exists(dirname(__DIR__) . '/controllers/' . $url[0] . '/' . $url[1] . '.php')) {
                    $this->api = $url[0];
                    $this->controller = $url[1];
                    unset($url[0]);
                    unset($url[1]);
                } else {
                    $log = new AppLogger('core.app.api');
                    $log->logInfo("Controller file does not exist", ['fileName' => $url[1]]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }
                    Request::apiResponse([
                        'code' => 404,
                        'message' => 'Requested endpoint does not exist',
                        'requestData' => [
                            'method' => $_SERVER['REQUEST_METHOD'],
                            'endpoint' => $endpoint,
                        ]
                    ], [
                        'code' => 404,
                    ]);
                    return;
                }

                require_once(dirname(__DIR__) . '/controllers/' . $this->api . '/' . $this->controller . '.php');

                try {
                    $this->controller = new $this->controller;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app.api');
                    $log->logError($th->getMessage(), ['controller' => $this->controller]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }
                    Request::apiResponse([
                        'code' => 503,
                        'message' => 'Service unavailable at the moment. Please contact support',
                        'method' => $_SERVER['REQUEST_METHOD'],
                        'endpoint' => $endpoint,
                    ], [
                        'code' => 503,
                    ]);
                    return;
                }

                if (isset($url[2])) {
                    if (method_exists($this->controller, $url[2])) {
                        $this->method = $url[2];
                        unset($url[2]);
                    } else {
                        $log = new AppLogger('core.app.api');
                        $log->logInfo("Method does not exist", ['controller' => $this->controller, 'method' => $url[2]]);
                        $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                        if (isset($_REQUEST['url'])) {
                            unset($_REQUEST['url']);
                        }
                        Request::apiResponse([
                            'code' => 405,
                            'message' => 'Requested method not yet supported on this server',
                            'requestData' => [
                                'method' => $_SERVER['REQUEST_METHOD'],
                                'endpoint' => $endpoint,
                            ]
                        ], [
                            'code' => 405,
                        ]);
                        return;
                    }
                }

                try {
                    $reflection = new ReflectionMethod($this->controller, $this->method);
                    if ($reflection->isPublic()) {
                        $this->params = $url ? array_values($url) : [];
                        call_user_func_array([$this->controller, $this->method], $this->params);
                    } else {
                        $log = new AppLogger('core.app');
                        $log->logInfo("Method is not a public method", ['controller' => $this->controller, 'method' => $this->method]);
                        $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                        if (isset($_REQUEST['url'])) {
                            unset($_REQUEST['url']);
                        }

                        Request::apiResponse([
                            'code' => 403,
                            'message' => 'Access to the requested resource is forbidden on this server',
                            'requestData' => [
                                'method' => $_SERVER['REQUEST_METHOD'],
                                'endpoint' => $endpoint,
                            ]
                        ], [
                            'code' => 403,
                        ]);
                        return;
                    }
                } catch (ReflectionException $th) {
                    $log = new AppLogger('core.app.api');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }

                    Request::apiResponse([
                        'code' => 404,
                        'message' => 'Requested endpoint does not exist',
                        'requestData' => [
                            'method' => $_SERVER['REQUEST_METHOD'],
                            'endpoint' => $endpoint,
                        ]
                    ], [
                        'code' => 404,
                    ]);
                    return;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app.api');
                    $log->logError($th->getMessage(), ['controller' => $this->controller, 'method' => $this->method]);
                    $endpoint = isset($_REQUEST['url']) ? "/" . $_REQUEST['url'] : "/api";
                    if (isset($_REQUEST['url'])) {
                        unset($_REQUEST['url']);
                    }

                    Request::apiResponse([
                        'code' => 503,
                        'message' => 'Service unavailable at the moment. Please contact support',
                        'requestData' => [
                            'method' => $_SERVER['REQUEST_METHOD'],
                            'endpoint' => $endpoint,
                        ]
                    ], [
                        'code' => 503,
                    ]);
                    return;
                }
            }
        } else {
            if (is_dir(dirname(__DIR__) . '/controllers/web/' . $url[0])) {
                $url[1] = isset($url[1]) ? $url[1] : $this->method;

                if (file_exists(dirname(__DIR__) . '/controllers/web/' . $url[0] . '/' . $url[1] . '.php')) {
                    $this->dir = $url[0];
                    $this->controller = $url[1];
                    unset($url[0]);
                    unset($url[1]);

                    require_once(dirname(__DIR__) . '/controllers/web/' . $this->dir . '/' . $this->controller . '.php');
                } else {
                    $log = new AppLogger('core.app');
                    $log->logInfo("Controller file does not exist", ['folderName' => $url[0], 'fileName' => $url[1]]);
                    $this->internalError("notfound");
                    return;
                }

                try {
                    $this->controller = new $this->controller;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller]);
                    $this->internalError("internalerror");
                    return;
                }

                if (isset($url[2])) {
                    if (method_exists($this->controller, $url[2])) {
                        $this->method = $url[2];
                        unset($url[2]);
                    } else {
                        $log = new AppLogger('core.app');
                        $log->logInfo("Method does not exist", ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $url[2]]);
                        $this->internalError("notfound");
                        return;
                    }
                }

                try {
                    $reflection = new ReflectionMethod($this->controller, $this->method);
                    if ($reflection->isPublic()) {
                        $this->params = $url ? array_values($url) : [];
                        call_user_func_array([$this->controller, $this->method], $this->params);
                    } else {
                        $log = new AppLogger('core.app');
                        $log->logInfo("Method is not a public method", ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                        $this->internalError("forbidden");
                        return;
                    }
                } catch (ReflectionException $th) {
                    $log = new AppLogger('core.app');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                    $this->internalError("notfound");
                    return;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                    $this->internalError("internalerror");
                    return;
                }
            } else {
                if (file_exists(dirname(__DIR__) . '/controllers/web/' . $url[0] . '.php')) {
                    $this->controller = $url[0];
                    unset($url[0]);

                    require_once(dirname(__DIR__) . '/controllers/web/' . $this->controller . '.php');
                } else {
                    $log = new AppLogger('core.app');
                    $log->logInfo("Controller file does not exist", ['fileName' => $url[0]]);
                    $this->internalError("notfound");
                    return;
                }

                try {
                    $this->controller = new $this->controller;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app');
                    $log->logError($th->getMessage(), ['controller' => $this->controller]);
                    $this->internalError("internalerror");
                    return;
                }

                if (isset($url[1])) {
                    if (method_exists($this->controller, $url[1])) {
                        $this->method = $url[1];
                        unset($url[1]);
                    } else {
                        $log = new AppLogger('core.app');
                        $log->logInfo("Method does not exist", ['controller' => $this->controller, 'method' => $url[1]]);
                        $this->internalError("notfound");
                        return;
                    }
                }

                try {
                    $reflection = new ReflectionMethod($this->controller, $this->method);
                    if ($reflection->isPublic()) {
                        $this->params = $url ? array_values($url) : [];
                        call_user_func_array([$this->controller, $this->method], $this->params);
                    } else {
                        $log = new AppLogger('core.app');
                        $log->logInfo("Method is not a public method", ['controller' => $this->controller, 'method' => $this->method]);
                        $this->internalError("forbidden");
                        return;
                    }
                } catch (ReflectionException $th) {
                    $log = new AppLogger('core.app');
                    $log->logError($th->getMessage(), ['folderName' => $this->dir, 'controller' => $this->controller, 'method' => $this->method]);
                    $this->internalError("notfound");
                    return;
                } catch (\Throwable $th) {
                    $log = new AppLogger('core.app');
                    $log->logError($th->getMessage(), ['controller' => $this->controller, 'method' => $this->method]);
                    $this->internalError("internalerror");
                    return;
                }
            }

        }
    }

    private function internalError($error = "notfound")
    {
        try {
            $this->controller = 'errors';
            require_once(dirname(__DIR__) . '/controllers/core/' . $this->controller . '.php');

            $this->controller = new $this->controller;
            $this->method = $error;
            call_user_func_array([$this->controller, $this->method], $this->params);
        } catch (\Throwable $th) {
            echo "GalaxyPHP Misconfiguration: " . $th->getMessage();
            exit;
        }
    }

    private function maintenance()
    {
        if ($_ENV['APP_MAINTENANCE_STATUS'] == "On" || $_ENV['APP_MAINTENANCE_STATUS'] == "true" || $_ENV['APP_MAINTENANCE_STATUS'] === 1) {
            try {
                $this->controller = 'errors';
                require_once(dirname(__DIR__) . '/controllers/core/' . $this->controller . '.php');

                $this->controller = new $this->controller;
                $this->method = 'maintenance';

                call_user_func_array([$this->controller, $this->method], $this->params);
            } catch (\Throwable $th) {
                echo "GalaxyPHP Misconfiguration: " . $th->getMessage();
                exit;
            }
            return;
        }
    }
}