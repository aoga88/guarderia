<?php
/**
 * Response.php
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

/**
 * The Response class, this is the Response class
 *
 * @category OsRest
 * @package  Classes
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
class Response
{

    /**
     * The constructor
     */
    public function __construct()
    {

    }

    /**
     * Sets the response code
     *
     * @param integer $code The response code
     *
     * @return OsRest\Classes\Response
     */
    public function setCode($code)
    {
        $this->_code = $code;
        return $this;
    }

    /**
     * The response set
     *
     * @param string $message The message
     *
     * @return OsRest\Classes\Response
     */
    public function sendMessage($message)
    {
        $this->_response = ['response' => $message];
        return $this;
    }

    /**
     * Sets the response to string
     *
     * @return string
     */
    public function __toString()
    {
        //header('Content-Type: application/json');
        http_response_code($this->_code);
        return json_encode($this->_response);
    }

    private $_response;

    private $_code;
}