<?php

namespace ezeasorekene\App\Core\API;

use Exception;
use ezeasorekene\App\Core\System\Encryption;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTimeImmutable;

/**
 * Generate and verify JWT token
 * @author Ekene Ezeasor <ezeasorekene@gmail.com>
 * @copyright 2023 Ekene Ezeasor
 */
class JWTToken extends JWT
{

    public string $public_key;

    public string $private_key;

    private DateTimeImmutable $issuedAt;

    private int $expiresAt;

    private int $validFrom;

    private string $issuer;

    private string $alg = "HS512";

    private array $supported_algorithms = [
        'HS256',
        'HS384',
        'HS512'
    ];

    /**
     * Instantiate the class
     * @param string $private_key
     * @param array $options
     */
    public function __construct(string $private_key = "", array $options = [])
    {
        if (!empty($private_key)) {
            $this->private_key = $private_key;
        }

        $issuer = getenv("APP_URL") ?? "GalaxyPHP";
        $this->issuer = $options['issuer'] ?? $issuer;
    }

    /**
     * Generate and encode JWT access token. Throws an exception if any error.
     * @param array $userData The custom data that identifies the user
     * @param array $options Other options that can be passed. Current values are ``$alg`` is used to set algorithm.
     * At the moment, only `HS256`, `HS384` and `HS512` are supported. 
     * ``$expiry`` is used to set the expiry in minutes.
     * @return string The encoded access token generated
     */
    public function generateAccessToken(array $userData, array $options = []): string
    {
        if (empty($this->private_key)) {
            throw new Exception("Private key not supplied", 400);
        }

        $minutes = intval($options['expiry'] ?? 5);
        $alg = $options['alg'] ?? $this->alg;

        if (!in_array($alg, $this->supported_algorithms)) {
            throw new Exception("Unsupported algorithm supplied", 400);
        }

        $this->issuedAt = new DateTimeImmutable();
        $this->expiresAt = $this->issuedAt->modify("+{$minutes} minutes")->getTimestamp(); // Add minutes into the future

        $data = [
            'iat' => $this->issuedAt->getTimestamp(),
            // Issued at: time when the token was generated
            'iss' => $this->issuer,
            // Issuer
            'nbf' => $this->issuedAt->getTimestamp(),
            // Not before
            'exp' => $this->expiresAt,
            // Expires at
        ];

        if (!is_array($userData)) {
            throw new Exception("User data supplied is invalid", 400);
        }

        $data = array_merge($data, $userData);

        $accessToken = JWT::encode(
            $data,
            $this->private_key,
            $alg
        );

        return $accessToken;
    }


    /**
     * Generate and encode JWT refresh token. Throws an exception if any error.
     * @param array $userData The custom data that identifies the user
     * @param array $options Other options that can be passed. Current values are ``$alg`` is used to set algorithm. At the moment, only `HS256`, `HS384` and `HS512` are supported. 
     * ``$expiry`` is used to set the expiry in days. ``$validfrom`` is used to set the time in minutes when the token validity will start counting
     * @return string The encoded refresh token generated
     */
    public function generateRefreshToken(array $userData, array $options = []): string
    {
        if (empty($this->private_key)) {
            throw new Exception("Private key not supplied", 400);
        }

        $expiry = intval($options['expiry'] ?? 15);
        $validfrom = intval($options['validfrom'] ?? 5);
        $alg = $options['alg'] ?? $this->alg;

        if (!in_array($alg, $this->supported_algorithms)) {
            throw new Exception("Unsupported algorithm supplied", 400);
        }

        $this->issuedAt = new DateTimeImmutable();
        $this->expiresAt = $this->issuedAt->modify("+{$expiry} minutes")->getTimestamp(); // Add minutes into the future
        $this->validFrom = $this->issuedAt->modify("+{$validfrom} minutes")->getTimestamp(); // Add minutes into the future

        $data = [
            'iat' => $this->issuedAt->getTimestamp(),
            // Issued at: time when the token was generated
            'iss' => $this->issuer,
            // Issuer
            'nbf' => $this->validFrom,
            // Not valid before
            'exp' => $this->expiresAt,
            // Expires at
        ];

        if (!is_array($userData)) {
            throw new Exception("User data supplied is invalid", 400);
        }

        $data = array_merge($data, $userData);

        $refreshToken = JWT::encode(
            $data,
            $this->private_key,
            $alg
        );

        $refreshToken = Encryption::encrypt($refreshToken, $this->private_key);

        return $refreshToken;
    }


    /**
     * Verify an encoded JWT token. Throws an exception if any error.
     * @param string $token The encoded token
     * @param string $alg Algorithm used. If not supplied, the default ``HS512`` will be used
     * @return bool If the token is valid return ``true`` else return ``false``
     */
    public function verifyToken(string $token, string $alg = null): bool
    {
        if (empty($this->private_key)) {
            throw new Exception("Private key not supplied", 400);
        }

        $alg = $alg ?? $this->alg;
        $separator = '.';
        if (2 !== substr_count($token, $separator)) {
            throw new Exception("Invalid token format", 401);
        }

        $token = JWT::decode($token, new Key($this->private_key, $alg));
        $now = new DateTimeImmutable();

        if ($token->iss !== $this->issuer) {
            throw new Exception("Issuer mismatch", 401);
        }

        if ($token->nbf > $now->getTimestamp()) {
            throw new Exception("Token supplied is not yet valid", 401);
        }

        if ($token->exp < $now->getTimestamp()) {
            throw new Exception("Token supplied has expired", 401);
        }

        return true;
    }


    /**
     * Get data from an encoded JWT token. Throws an exception if any error.
     * @param string $token The encoded token
     * @return array The decoded payload as an array
     */
    public static function getTokenData(string $token): array
    {
        $separator = '.';
        if (2 !== substr_count($token, $separator)) {
            throw new Exception("Invalid token format", 401);
        }

        list($header, $payload, $signature) = explode(".", $token);

        $payload = base64_decode($payload);

        $payload = json_decode($payload, true);

        return $payload;
    }

}