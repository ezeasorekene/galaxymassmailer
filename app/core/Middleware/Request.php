<?php

namespace ezeasorekene\App\Core\Middleware;

use ezeasorekene\App\Core\System\Behaviour;

class Request
{

    /**
     * Fetches the request from the client and checks if it is a head method
     * @return bool Returns `true` if the `REQUEST_METHOD` matches, otherwise false
     */
    public static function head(): bool
    {
        if (isset($_REQUEST['__method'])) {
            if ($_REQUEST['__method'] == 'HEAD') {
                self::setResponseHeaders(['method' => 'HEAD']);
                return true;
            } else {
                return false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'HEAD') {
            self::setResponseHeaders(['method' => 'HEAD']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches the request from the client and checks if it is a options method
     * @return bool Returns `true` if the `REQUEST_METHOD` matches, otherwise false
     */
    public static function options(): bool
    {
        if (isset($_REQUEST['__method'])) {
            if ($_REQUEST['__method'] == 'OPTIONS') {
                self::setResponseHeaders(['method' => 'OPTIONS']);
                return true;
            } else {
                return false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            self::setResponseHeaders(['method' => 'OPTIONS']);
            return true;
        } else {
            return false;
        }
    }


    /**
     * Fetches the request from the client and checks if it is a get method
     * @return bool Returns `true` if the `REQUEST_METHOD` matches, otherwise false
     */
    public static function get(): bool
    {
        if (isset($_REQUEST['__method'])) {
            if ($_REQUEST['__method'] == 'GET') {
                self::setResponseHeaders(['method' => 'GET']);
                return true;
            } else {
                return false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'GET') {
            self::setResponseHeaders(['method' => 'GET']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches the request from the client and checks if it is a post method
     * @return bool Returns `true` if the `REQUEST_METHOD` matches, otherwise false
     */
    public static function post($strict = false): bool
    {
        if (isset($_REQUEST['__method'])) {
            if ($_REQUEST['__method'] == 'POST') {
                self::setResponseHeaders(['method' => 'POST']);
                return true;
            } else {
                return false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
            self::setResponseHeaders(['method' => 'POST']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches the request from the client and checks if it is a put method
     * @return bool Returns `true` if the `REQUEST_METHOD` matches, otherwise false
     */
    public static function put($strict = false): bool
    {
        if (isset($_REQUEST['__method'])) {
            if ($_REQUEST['__method'] == 'PUT') {
                self::setResponseHeaders(['method' => 'PUT']);
                return true;
            } else {
                return false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
            self::setResponseHeaders(['method' => 'PUT']);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fetches the request from the client and checks if it is a delete method
     * @return bool Returns `true` if the `REQUEST_METHOD` matches, otherwise false
     */
    public static function delete($strict = false): bool
    {
        if (isset($_REQUEST['__method'])) {
            if ($_REQUEST['__method'] == 'DELETE') {
                self::setResponseHeaders(['method' => 'DELETE']);
                return true;
            } else {
                return false;
            }
        } elseif ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            self::setResponseHeaders(['method' => 'DELETE']);
            return true;
        } else {
            return false;
        }
    }

    public static function setResponseHeaders(array $options = []): void
    {
        $responseType = self::getResponseContentType();
        $code = isset($options['code']) ? $options['code'] : 200;
        $origin = isset($options['origin']) ? $options['origin'] : '*';
        $methods = isset($options['methods']) ? $options['methods'] : 'GET, POST, PUT, DELETE';
        $max_age = isset($options['max_age']) ? $options['max_age'] : '3600';
        $content_type = isset($options['content_type']) ? $options['content_type'] : 'application/json; charset=UTF-8';
        $allow_headers = isset($options['allow_headers']) ? $options['allow_headers'] : 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With';

        if ($responseType === 'json') {
            header("Access-Control-Allow-Origin: $origin");
            header("Content-Type: $content_type");
            header("Access-Control-Allow-Methods: $methods");
            header("Access-Control-Max-Age: $max_age");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, $allow_headers");
            if (isset($options['headers']) && is_array($options['headers']) && !empty($options['headers'])) {
                foreach ($options['headers'] as $header => $value) {
                    header("$header: $value");
                }
            }
        } else {
            // default to text/html if no response type is specified or invalid
            header("Access-Control-Allow-Origin: $origin");
            header("Content-Type: text/html; charset=UTF-8");
            header("Access-Control-Allow-Methods: $methods");
            header("Access-Control-Max-Age: $max_age");
            header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, $allow_headers");
            if (isset($options['headers']) && is_array($options['headers']) && !empty($options['headers'])) {
                foreach ($options['headers'] as $header => $value) {
                    header("$header: $value");
                }
            }
        }
    }

    public static function getResponseContentType(): string
    {
        if (isset($_SERVER['HTTP_RESPONSE_CONTENT_TYPE']) && strtolower($_SERVER['HTTP_RESPONSE_CONTENT_TYPE']) == 'json') {
            return 'json';
        }

        if (isset($_REQUEST['content_type']) && strtolower($_REQUEST['content_type']) == 'json') {
            return 'json';
        }

        return 'html';
    }

    public static function response(array $data, int $code): void
    {
        http_response_code($code);
        if (self::getResponseContentType() == 'json') {
            echo json_encode($data);
            return;
        }
    }

    public static function apiResponse(array $data, array $options = []): void
    {
        $code = isset($options['code']) ? $options['code'] : 200;
        $origin = isset($options['origin']) ? $options['origin'] : '*';
        $methods = isset($options['methods']) ? $options['methods'] : 'GET, POST, PUT, DELETE';
        $max_age = isset($options['max_age']) ? $options['max_age'] : '3600';
        $content_type = isset($options['content_type']) ? $options['content_type'] : 'application/json; charset=UTF-8';
        $allow_headers = isset($options['allow_headers']) ? $options['allow_headers'] : 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With';

        header("Access-Control-Allow-Origin: $origin");
        header("Content-Type: $content_type");
        header("Access-Control-Allow-Methods: $methods");
        header("Access-Control-Max-Age: $max_age");
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With, $allow_headers");
        if (isset($options['headers']) && is_array($options['headers']) && !empty($options['headers'])) {
            foreach ($options['headers'] as $header => $value) {
                header("$header: $value");
            }
        }

        http_response_code($code);

        echo json_encode($data);
        return;
    }

    public static function methodNotAllowed(string $allowed_methods = 'GET'): void
    {
        if (isset($_REQUEST['url'])) {
            $endpoint = '/' . $_REQUEST['url'];
            unset($_REQUEST['url']);
        }
        self::apiResponse([
            'code' => 405,
            'message' => 'Request method not allowed on this endpoint',
            'requestData' => [
                'method' => $_SERVER['REQUEST_METHOD'],
                'endpoint' => $endpoint,
            ]
        ], [
            'code' => 405,
            'methods' => $allowed_methods,
        ]);
        return;
    }


    public static function rebuildQuery(string $url, string $parameter, mixed $newParamString = ""): string
    {
        // Parse the URL
        $urlParts = parse_url($url);

        // Check if the query string is present
        if (isset($urlParts['query'])) {
            // Parse the query string into an associative array
            parse_str($urlParts['query'], $queryParams);

            // Check if $parameter parameter exists
            if (isset($queryParams[$parameter])) {
                // Update the $parameter parameter
                $queryParams[$parameter] = $newParamString;
            } else {
                // Add $parameter parameter if it doesn't exist
                $queryParams[$parameter] = $newParamString;
            }

            // Rebuild the query string
            $newQueryString = http_build_query($queryParams);

            // Update the query string in the URL
            $urlParts['query'] = $newQueryString;
        } else {
            // If there was no query string, create a new one
            $urlParts['query'] = $parameter . '=' . $newParamString;
        }

        if (isset($urlParts['port'])) {
            if ($urlParts['port'] == 80 || $urlParts['port'] == 443) {
                $port = '';
            } else {
                $port = ':' . $urlParts['port'];
            }
        } else {
            $port = '';
        }

        $scheme = empty($urlParts['scheme']) ? 'http' : $urlParts['scheme'];

        // Rebuild the URL
        $newUrl = $scheme . '://' . $urlParts['host'] . $port . $urlParts['path'] . '?' . $urlParts['query'];

        return $newUrl;
    }


    public static function requestURI()
    {
        $scheme = empty($_SERVER['REQUEST_SCHEME']) ? 'http' : $_SERVER['REQUEST_SCHEME'];
        return $scheme . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public static function getQueryValue(string $query, $queryString = '')
    {
        return str_replace(['/', '?', $queryString, '='], "", $query);
    }


    public static function checkContentLength($maxContentLength = null): void
    {
        $contentLength = isset($_SERVER['CONTENT_LENGTH']) ? (int) $_SERVER['CONTENT_LENGTH'] : 0;
        $maxContentLength = $maxContentLength ?? Behaviour::getMaxContentLength(); // Set your desired maximum content length (in bytes)

        if ($contentLength > $maxContentLength) {
            self::apiResponse([
                'code' => 413,
                'message' => 'Request entity is too large.',
            ], [
                'code' => 413,
                'methods' => "POST, PUT",
            ]);
            return;
        }
    }

}