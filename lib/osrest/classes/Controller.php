<?php
/**
 * Controller.php
 *
 * PHP version 5
 *
 * @category OsRest
 * @package  Classes
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsRest\Classes;

use OsRest\Auth\Authentication as Auth;
use Exception;

/**
 * The Controller class, this is the Controller class
 *
 * @category OsRest
 * @package  Classes
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
abstract class Controller
{

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->response = new Response();
    }

    /**
     * Sets the response
     *
     * @param OsRest\Classes\Response $response The response
     *
     * @return void
     */
    protected function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Gets the response
     *
     * @return OsRest\Classes\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Validates a role
     *
     * @param array $roles The roles to validate
     *
     * @return boolean
     */
    public function validateRoles(array $roles = [])
    {
        $config  = osrestConfig('auth');
        $session = Auth::getSession($config);
        $uRoles  = $session['roles'];

        $found = false;

        foreach ($roles as $role) {
            if (in_array($role, $uRoles)) {
                $found = true;
            }
        }

        if ($found === false) {
            throw new Exception("notAllowed", 1);
            
        }

        return $found;
    }

    protected $response;
    
}