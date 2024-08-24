<?php

namespace App\Http;

use App\Utils\WooCommerce\User;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use stdClass;
use WP_Error;

class RemoteRequestHandler
{
    private const JWT_KEY = "aPY37c2SF5P3lqB1rKJs7oTioa5OzIosl2TUEB3Bjg8Ay2PJWeV8gvwGPFGS51bM";
    private const TOKEN_META_KEY = 'lnd_master_access_token';
    private const JWT_ALGORITHM = 'HS256';

    /**
     * Make a remote request with optional SSL verification and bearer token.
     *
     * @param string $url The URL to make the request to.
     * @param array|null $body The request body (for POST requests).
     * @param string $method The HTTP method (GET or POST).
     * @param bool $bearerToken Whether to use a bearer token for authentication.
     * @return mixed The response body or a stdClass with error information.
     */
    public static function makeRequest(string $url, string $method = 'GET', ?array $body = null, bool $bearerToken = false)
    {
        $headers = [];
        $args = [
            'method' => $method,
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'cookies' => [],
            'access_url' => get_site_url()
        ];


        if ($bearerToken) {
            $token = self::getValidToken();
            if (!$token) {
                return self::createErrorResponse('Failed to obtain a valid token.');
            }
            $headers['Authorization'] = "Bearer $token";
        }

        if ($body) {
            $args['body'] = wp_json_encode($body);
            $headers['Content-Type'] = 'application/json';
        }

        $args['headers'] = $headers;

        // Try with SSL verification first
        $serverResponse = self::performRequest($url, $args, true);

        // If SSL verification fails, try without it
        if (is_wp_error($serverResponse)) {
            $serverResponse = self::performRequest($url, $args, false);
        }

        if (is_wp_error($serverResponse)) {
            return self::createErrorResponse($serverResponse->get_error_message());
        }

        $body = wp_remote_retrieve_body($serverResponse);
        $statusCode = wp_remote_retrieve_response_code($serverResponse);

        if ($statusCode >= 400) {
            return self::createErrorResponse("HTTP Error: $statusCode", $statusCode);
        }

        if (empty($body) || $body === "GET404") {
            return self::createErrorResponse("Empty response or 404 error", 404);
        }

        return json_decode($body);
    }

    /**
     * Perform the actual request with or without SSL verification.
     *
     * @param string $url The URL to make the request to.
     * @param array $args The request arguments.
     * @param bool $sslVerify Whether to verify SSL.
     * @return array|WP_Error The response or WP_Error on failure.
     */
    private static function performRequest(string $url, array $args, bool $sslVerify): array|WP_Error
    {
        $args['sslverify'] = $sslVerify;
        return $args['method'] === 'POST' && function_exists('wp_remote_post')
            ? wp_remote_post($url, $args)
            : wp_remote_request($url, $args);
    }

    /**
     * Create a standardized error response.
     *
     * @param string $message The error message.
     * @param int $statusCode The HTTP status code (optional).
     * @return stdClass The error response object.
     */
    private static function createErrorResponse(string $message, int $statusCode = 0): stdClass
    {
        $response = new stdClass();
        $response->msg = $message;
        $response->is_request_error = true;
        $response->status_code = $statusCode;
        return $response;
    }

    /**
     * Request a new JWT token.
     *
     * @return bool True if the token was successfully obtained and stored.
     */
    public static function requestToken(): bool
    {
        $url = LND_MASTER_DOWNLOADS_URL_API . 'wp-json/generate-token/v1/generate_jwt';
        $body = [
            'access_url' => get_site_url(),
            'data' => User::get_user_simple_data(),
        ];
        
        $request = self::makeRequest($url, 'POST', $body);

        if (!isset($request->token)) {
            return false;
        }
        
        $userId = get_current_user_id();
        $success = update_user_meta($userId, self::TOKEN_META_KEY, $request);
        
        return $success && get_user_meta($userId, self::TOKEN_META_KEY, true) == $request;
    }

    /**
     * Get a valid token, requesting a new one if necessary.
     *
     * @return string|null The valid token or null if unable to obtain one.
     */
    public static function getValidToken(): ?string
    {
        $userId = get_current_user_id();
        $tokenObject = get_user_meta($userId, self::TOKEN_META_KEY, true);

        if (!$tokenObject || !self::isTokenValid($tokenObject)) {
            $request = self::requestToken();
            if (!$request) {
                return null;
            }
            $tokenObject = get_user_meta($userId, self::TOKEN_META_KEY, true);
        }

        return $tokenObject->token ?? null;
    }

    /**
     * Check if the given token is valid.
     *
     * @param object $tokenObject The token object to validate.
     * @return bool True if the token is valid, false otherwise.
     */
    private static function isTokenValid(object $tokenObject): bool
    {
        if (!isset($tokenObject->token)) {
            return false;
        }

        try {
            JWT::decode($tokenObject->token, new Key(self::JWT_KEY, self::JWT_ALGORITHM));
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}