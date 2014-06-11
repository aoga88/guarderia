<?php
/**
 * Restful.php
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
use \Exception;

/**
 * The Restful class, this is the main class
 *
 * @category OsRest
 * @package  Classes
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
class Restful
{
    /**
     * The constructor
     * Initialices the app using the url given
     */
    public function __construct()
    {
        $this->readUrl();
    }

    /**
     * Reads the url
     *
     * @return void
     */
    public function readUrl()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $url    = str_replace('/api/', '', $_SERVER['REQUEST_URI']);
        $parts  = explode('/', $url);
        $routes = osrestConfig('routes');
        $routes = $routes[$method];

        if (isset($routes['/' . $url])) {
            $matchConfig = [];
            $routeConfig = $routes['/' . $url];
        } else {
            foreach ($routes as $route => $itemConfig) {
                $itemParts = explode('/', $route);
                $urlEx = "/^";

                foreach ($itemParts as $itemPart) {
                    if (!isset($itemPart[0])) {
                        continue;
                    }
                    
                    if ($itemPart[0] != ':') {
                        $urlEx .= "\/" . $itemPart;
                    } else {
                        $urlEx .= "\/([a-zA-Z0-9_-]+)";
                        $varName = substr($itemPart, 1, strlen($itemPart));
                    }
                }

                $urlEx = str_replace('//', '/', $urlEx);
                $urlEx .= '$/';
                $match = [];

                if (preg_match($urlEx, '/' . $url, $match) != 0) {
                    $routeConfig = $itemConfig;
                    $matchConfig = $match;

                    if (count($matchConfig) !== 0) {
                        unset($matchConfig[0]);
                    }
                }
            }
        }

        if (!isset($routeConfig)) {
            throw new Exception("noRoute", 1);
        }

        if ($routeConfig['requiredLogin'] == true) {
            $config = osrestConfig('auth');
            Auth::authenticate($config);
        }

        $controllerArray = $routeConfig['controller'];
        $moduleArray     = $routeConfig['module'];
        $this->_module   = $this->_getController($controllerArray, $moduleArray);
        $action          = $routeConfig['action'];

        call_user_func_array(array($this->_module, $action), $matchConfig);
    }

    /**
     * Gets the response
     *
     * @return OsRest\Classes\Response
     */
    public function getResponse()
    {
        return $this->_module->getResponse();
    }

    /**
     * Gets the controller
     *
     * @param string $controller The controller name
     * @param string $module     The module name
     *
     * @return OsRest\Classes\Controller
     */
    private function _getController($controller, $module)
    {
        $module          = ucfirst($module);
        $controller      = ucfirst($controller);
        $controllerClass = "OsApp\\{$module}\\{$controller}";
        
        $object = new $controllerClass();
        return $object;
    }

    private $_module;

    private $_controller;

    private $_action;
}