<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  App
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\App;

use OsRest\Classes\Controller;
use OsApp\Models\App as Model_App;
use MongoId;
use OsRest\Auth\Authentication as Auth;

/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  App
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
class Index extends Controller
{
    /**
     * Saves the App
     *
     * @return void
     */
    public function save()
    {
        parent::validateRoles(['admin', 'superadmin']);
        $config    = osrestConfig('auth');
        $roles     = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data->_id) {
                throw new Exception("notAllowed", 1);
            }
        }

        $model_app = new Model_App();
        $data      = json_decode(file_get_contents("php://input"));

        if (isset($data->_id) && $data->_id != 0) {
            $conditions = ['_id' => new MongoId($data->_id)];
            unset($data->_id);
            $result = $model_app->update($conditions, ['$set' => $data]);
        } else {
            if (isset($data->_id)) {
                unset($data->_id);
            }

            $result = $model_app->insert($data);
        }

        $this->response->sendMessage($data)
            ->setCode(200);
    }

    /**
     * Searchs based on conditions
     *
     * @return void
     */
    public function find()
    {
        parent::validateRoles(['superadmin']);
        $model_app  = new Model_App();
        $conditions = json_decode(file_get_contents("php://input"));

        $result = $model_app->find($conditions);

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }
}