<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Token
{
    /**
     * @var  string  key name used for token storage
     */
    public static $token_name = 'security_token';

    /**
     * Generate and store a unique token which can be used to help prevent
     * [CSRF](http://wikipedia.org/wiki/Cross_Site_Request_Forgery) attacks.
     *
     *     $token = Security::token();
     *
     * You can insert this token into your forms as a hidden field:
     *
     *     echo Form::hidden('csrf', Security::token());
     *
     * And then check it when using [Validation]:
     *
     *     $array->rules('csrf', array(
     *         'not_empty'       => NULL,
     *         'Security::check' => NULL,
     *     ));
     *
     * This provides a basic, but effective, method of preventing CSRF attacks.
     *
     * @param   boolean $new    force a new token to be generated?
     * @return  string
     * @uses    Session::instance
     */
    public static function get($new = FALSE)
    {
        $session = Session::instance();

        // Get the current token
        $token = $session->get(self::$token_name);

        if ($new === TRUE OR ! $token)
        {
            // Generate a new unique token
            if (function_exists('openssl_random_pseudo_bytes'))
            {
                // Generate a random pseudo bytes token if openssl_random_pseudo_bytes is available
                // This is more secure than uniqid, because uniqid relies on microtime, which is predictable
                $token = base64_encode(openssl_random_pseudo_bytes(32));
            }
            else
            {
                // Otherwise, fall back to a hashed uniqid
                $token = sha1(uniqid(NULL, TRUE));
            }

            // Store the new token
            $session->set(self::$token_name, $token);
        }

        return $token;
    }

    /**
     * Check that the given token matches the currently stored security token.
     *
     *     if (Security::check($token))
     *     {
     *         // Pass
     *     }
     *
     * @param   string  $token  token to check
     * @return  boolean
     */
    public static function check($token)
    {
        return self::get() === $token;
    }
}