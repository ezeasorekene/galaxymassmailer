<?php

namespace ezeasorekene\App\Core\API;

/**
 * Generate and verify JWT access token
 * @author Ekene Ezeasor <ezeasorekene@gmail.com>
 * @copyright 2023 Ekene Ezeasor
 */
class Headers
{

    public string $public_key;

    public string $private_key;

    public int $client_id;


    /**
     * Fetch the Bearer token from authorization header
     * @return string The encoded token fetched
     */
    public static function getBearerToken(): string
    {
        // Get the authorization header from the request
        $headers = getallheaders();
        $authorizationHeader = isset($headers['Authorization']) ? $headers['Authorization'] : '';

        // Extract the bearer token from the authorization header
        $matches = array();
        $pattern = "/Bearer\s(\S+)/";
        preg_match($pattern, $authorizationHeader, $matches);
        $bearerToken = isset($matches[1]) ? $matches[1] : '';
        return $bearerToken;
    }

    /**
     * Fetch the API Private Key from header
     * @return string The raw private key fetched
     */
    public static function getPrivateKey(): string
    {
        // Get the custom header value
        $apiPrivateKeyHeader = $_SERVER['HTTP_X_API_SECRET'] ?? '';

        return $apiPrivateKeyHeader;
    }


    /**
     * Fetch the API Public Key from header
     * @return string The raw public key fetched
     */
    public static function getPublicKey(): string
    {
        // Get the custom header value
        $apiPupblicKeyHeader = $_SERVER['HTTP_X_API_KEY'] ?? '';

        return $apiPupblicKeyHeader;
    }


    /**
     * Fetch the API Client ID from header
     * @return string The client id fetched
     */
    public static function getClientId(): string
    {
        // Get the custom header value
        $clientIdHeader = $_SERVER['HTTP_X_CLIENT_ID'] ?? '';

        return $clientIdHeader;
    }

}