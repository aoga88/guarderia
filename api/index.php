<?php
/**
 * api/index.php
 * This file bootstraps the API backend
 *
 * PHP Version 5
 *
 * @category OsRest
 * @package  Api
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
namespace OsRest\Api;

require '../vendor/autoload.php';
require '../require.php';

use OsRest\Classes\Restful as RestfulApp;
use OsRest\Classes\Response;
use Exception;

try
{
    $restfulApp = new RestfulApp();
    $response   = $restfulApp->getResponse();
}catch(Exception $e)
{
    $response = new Response();
    $response->sendMessage($e->getMessage())
        ->setCode(500);
}

echo $response;