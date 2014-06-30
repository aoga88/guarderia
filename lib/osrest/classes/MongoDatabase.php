<?php
/**
 * MongoDatabase.php
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

use MongoCollection;
use MongoClient;
use MongoId;

/**
 * The MongoDatabase class, this is the Database class
 *
 * @category OsRest
 * @package  Classes
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
abstract class MongoDatabase extends MongoCollection
{

    /**
     * The constructor
     */
    public function __construct()
    {
        try
        {
            $options = array(
                    'db'       => getenv("OSCLOUD_MONGODB_NAME"),
                    'username' => getenv("OSCLOUD_MONGODB_USER"),
                    'password' => getenv("OSCLOUD_MONGODB_PASSWORD")
                );
            $client  = new MongoClient(getenv("OSCLOUD_MONGODB_URL"), $options);

            $this->link_id = $client->selectDb(getenv("OSCLOUD_MONGODB_NAME"));
        }catch(MongoConnectionException $e){
            error_log($e->getMessage());
        }
        
        parent::__construct($this->link_id, $this->collection);
    }

    /**
     * Activa el elemento
     *
     * @param string $id El id
     *
     * @return void
     */
    public function activar($id)
    {
        $condition = array("_id" => $id);
        $this->update(
            $condition, ['$set' => 
                   [
                    "active" => true
                   ]
            ]
        );
    }

    /**
     * Updates just one
     *
     * @param array $condition The condition
     * @param array $object    The object
     *
     * @return void
     */
    public function updateOne(array $condition, array $object)
    {
        $actualObject = $this->findOne($condition);
        array_merge($actualObject, $object);
        parent::update($condition, $object);
    }

    /**
     * Finds the condition
     *
     * @param array $condition The condition
     *
     * @return void
     */ 
    public function find($condition = [])
    {
        if (isset($condition->_id)) {
            if (strlen($condition->_id) == 24) {
                $newId = new MongoId($condition->_id);
                
                if ($newId->isValid()) {
                    $condition->_id = $newId;    
                }
            }
        }
        return parent::find($condition);
    }

    /**
     * Finds the condition
     *
     * @param array $condition The condition
     *
     * @return void
     */ 
    public function realFind($condition = [])
    {
        return parent::find($condition);
    }

    protected $link_id;
    protected $collection;
}