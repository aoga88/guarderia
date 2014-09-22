<?php
/**
 * Asistencia.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Models
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\Models;

use OsRest\Classes\MongoDatabase;

/**
 * Asistencia.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Models
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
class Asistencia extends MongoDatabase
{

    /**
     * The constructor
     */
    public function __construct()
    {
        $this->collection = "asistencias";
        parent::__construct();
    }
}