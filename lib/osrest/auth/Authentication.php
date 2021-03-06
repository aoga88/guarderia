<?php
/**
 * Authentication.php
 *
 * PHP version 5
 *
 * @category OsRest
 * @package  Auth
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsRest\Auth;

use Exception;
use OsApp\Models\User as Model_User;

/**
 * The Authentication class, this is the Authentication class
 *
 * @category OsRest
 * @package  Auth
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
class Authentication
{

    /**
     * Authenticates based on the configuration
     *
     * @param array $config The configuration
     *
     * @return void
     */
    public static function authenticate(array $config)
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace("Basic ", "", $headers['Authorization']);
            $model_user = new Model_User();
            $user = $model_user->findOne(['token' => $token]);

            if ($user === null) {
                throw new Exception("notLoggedIn", 1);
            }
        } else {
        
            $sessionName = $config['sessionName'];
            session_start();

            if (!isset($_SESSION[$sessionName])) {
                throw new Exception("notLoggedIn", 1);
            }

            if (!isset($_SESSION[$sessionName]['osrest_user'])) {
                throw new Exception("notLoggedIn", 1);
            }

            $user = unserialize($_SESSION[$sessionName]['osrest_user']);
        }
    }

    /**
     * Creates a session
     *
     * @param array $config The configuration
     * @param mixed $user   The user info
     *
     * @return void
     */
    public static function createSession($config, $user)
    {
        $sessionName = $config['sessionName'];
        @session_start();
        $_SESSION[$sessionName]['osrest_user'] = serialize($user);
    }

    /**
     * Gets the session
     *
     * @param array  $config The configuration
     * @param string $key    (Optional) The key
     *
     * @return void
     */
    public static function getSession($config, $key = null)
    {
        $headers = apache_request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace("Basic ", "", $headers['Authorization']);
            $model_user = new Model_User();
            $current = $model_user->findOne(['token' => $token]);

            if ($current === null) {
                throw new Exception("notLoggedIn", 1);
            }
        } else {
            $sessionName = $config['sessionName'];
            @session_start();
            $current = unserialize($_SESSION[$sessionName]['osrest_user']);
        }

        if ($key === null) {
            return $current;
        }

        if (isset($current[$key])) {
            return $current[$key];
        } else {
            return null;
        }
    }
}