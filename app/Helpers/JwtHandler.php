<?php

namespace App\Helpers;

use Exception;
use Firebase\JWT\JWT;

class JwtHandler
{
    /**
     * Generate a JWT token.
     *
     * @param  array  $user
     * @return string
     * @throws Exception
     */
    public static function generateJwt($user)
    {
        try {
            // Get the secret key from the environment variables
            $secretKey = env('JWT_SECRET');

            // Define the payload
            $payload = [
                'iat' => time(), // Issued at: current timestamp
                'exp' => time() + 3600 * 30, // Expiration: 1 hour
                'id' => $user['id'], // Subject: the user's ID or any unique identifier
                'name' => $user['name'], // User's name or any other data to include
                'email' => $user['email'], // User's email or any other data to include
            ];
            // Create the JWT token
            $jwt = JWT::encode($payload, $secretKey, 'HS256');
            return $jwt;
        } catch (Exception $e) {
            // Handle any errors that occur while generating the JWT
            throw new Exception('Error generating token: ' . $e->getMessage());
        }
    }
    public function decodeJwt($token)
    {
        try {
            $headers = new \stdClass();
            $headers->alg = 'HS256';
            $kid = env('JWT_SECRET');
            $decoded = JWT::decode($token, $kid, $headers);
            return $decoded;
        } catch (Exception $e) {
            throw new Exception('Invalid token: ' . $e->getMessage());
        }
    }
}
