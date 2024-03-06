<?php

namespace ezeasorekene\App\Core\API;

use ezeasorekene\App\Models\APIKeys;
use ezeasorekene\App\Core\Controller;
use ezeasorekene\App\Core\API\Headers;
use ezeasorekene\App\Core\API\JWTToken;
use ezeasorekene\App\Core\System\Encryption;
use ezeasorekene\App\Core\Middleware\Request;
use ezeasorekene\App\Core\Middleware\AppLogger;

class APIAuth extends Controller
{

    protected string $accessToken;

    protected string $refreshToken;

    protected string $client_id;

    protected string $public_key;

    protected string $private_key;


    public function __construct()
    {
        $this->accessToken = Headers::getBearerToken() ?? null;
        $this->refreshToken = Headers::getBearerToken() ?? null;
    }

    public static function verifyAccessToken(): void
    {
        try {
            $token = Headers::getBearerToken() ?? null;

            if (empty($token)) {
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Token not supplied',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            }

            // Split the token into its components
            $tokenParts = explode(".", $token);

            // Check if the token has exactly three parts
            if (count($tokenParts) !== 3) {
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Token has been tampered with',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            } else {
                list($header, $payload, $signature) = explode(".", $token);
                $payload = base64_decode($payload);
                $payload = json_decode($payload);
                $client_id = $payload->client_id ?? null;
            }

            if (empty($client_id)) {
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Client ID not supplied',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            }

            try {
                $client_credentials = APIKeys::where('client_id', $client_id)->first();
                $private_key = Encryption::decrypt($client_credentials->private_key, $client_id);
            } catch (\Throwable $th) {
                $log = new AppLogger('apiauth.verify');
                $log->logError($th->getMessage(), ['client_id' => $client_id]);
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Client ID is invalid',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            }


            $jwt = new JWTToken;
            $jwt->private_key = $private_key;
            if ($jwt->verifyToken($token)) {
                $jwtData = JWTToken::getTokenData($token);
                $log = new AppLogger('apiauth.verify');
                $log->logInfo("Successfully verified token", ['client_id' => $client_id, 'jwt_data' => $jwtData]);
            } else {
                $log = new AppLogger('apiauth.verify');
                $log->logError("Token verification failed", ['client_id' => $client_id, 'token' => $token]);
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Verification encountered an error. Please try again',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
                ;
            }
        } catch (\Throwable $th) {
            $log = new AppLogger('apiauth.verify');
            $log->logError($th->getMessage(), ['client_id' => $client_id, 'token' => $token ?? null]);
            Request::apiResponse([
                'code' => $th->getCode() == 0 ? 401 : $th->getCode(),
                'status' => 'error',
                'message' => $th->getMessage(),
            ], [
                'code' => 401,
                'methods' => 'GET, POST, PUT, DELETE',
            ]);
            exit;
        }
    }

    public static function verifyRefreshToken(): void
    {
        try {
            $json = file_get_contents('php://input');
            $data = json_decode($json);
            if (json_last_error() === 0) {
                //Get content as raw json data
                $client_id = empty(Headers::getClientId()) ? $data->client_id ?? null : Headers::getClientId();
                $token = empty(Headers::getBearerToken()) ? $data->refresh_token ?? null : Headers::getBearerToken();
            } else {
                //Get content as raw post data
                $client_id = empty(Headers::getClientId()) ? $_POST['client_id'] ?? null : Headers::getClientId();
                $token = empty(Headers::getBearerToken()) ? $_POST['refresh_token'] ?? null : Headers::getBearerToken();
            }

            if (empty($client_id)) {
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Client ID not supplied',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            }

            try {
                $client_credentials = APIKeys::find((string) $client_id)->first();
                $private_key = Encryption::decrypt($client_credentials->private_key, $client_id);
            } catch (\Throwable $th) {
                $log = new AppLogger('apiauth.verify.refresh');
                $log->logError($th->getMessage(), ['client_id' => $client_id]);
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Client ID is invalid',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            }

            $token = Encryption::decrypt($token, $private_key);
            if (!$token) {
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Refresh token is invalid',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                return;
            }

            // Split the token into its components
            $tokenParts = explode(".", $token);

            // Check if the token has exactly three parts
            if (count($tokenParts) !== 3) {
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Token has been tampered with',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
            } else {
                list($header, $payload, $signature) = explode(".", $token);
                $payload = base64_decode($payload);
            }

            $jwt = new JWTToken;
            $jwt->private_key = $private_key;
            if ($jwt->verifyToken($token)) {
                $jwtData = JWTToken::getTokenData($token);
                $log = new AppLogger('apiauth.verify.refresh');
                $log->logInfo("Successfully verified token", ['client_id' => $client_id, 'jwt_data' => $jwtData]);
            } else {
                $log = new AppLogger('apiauth.verify.refresh');
                $log->logError("Token verification failed", ['client_id' => $client_id, 'token' => $token]);
                Request::apiResponse([
                    'code' => 401,
                    'status' => 'error',
                    'message' => 'Verification encountered an error. Please try again',
                ], [
                    'code' => 401,
                    'methods' => 'GET, POST, PUT, DELETE',
                ]);
                exit;
                ;
            }
        } catch (\Throwable $th) {
            $log = new AppLogger('apiauth.verify');
            $log->logError($th->getMessage(), ['client_id' => $client_id, 'token' => $token ?? null]);
            Request::apiResponse([
                'code' => $th->getCode() == 0 ? 401 : $th->getCode(),
                'status' => 'error',
                'message' => $th->getMessage(),
            ], [
                'code' => 401,
                'methods' => 'GET, POST, PUT, DELETE',
            ]);
            exit;
        }
    }

    public static function getClientId($token = null): mixed
    {
        $bearer_token = $token ?? Headers::getBearerToken();
        if (!empty($bearer_token)) {
            // Split the token into its components
            $tokenParts = explode(".", $bearer_token ?? '');

            // Check if the token has exactly three parts
            if (count($tokenParts) !== 3) {
                return null;
            } else {
                list($header, $payload, $signature) = explode(".", $bearer_token);
                $payload = base64_decode($payload);
                $payload = json_decode($payload);
                $client_id = $payload->client_id;
                return $client_id;
            }
        }
        return null;
    }

}