<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Actividad
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\Actividad;

use OsRest\Classes\Controller;
use OsApp\Models\Actividad as Model_Actividad;
use OsApp\Models\User as Model_User;
use MongoId;
use OsRest\Auth\Authentication as Auth;
use Exception;

/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Actividad
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
class Index extends Controller
{
    /**
     * Searchs based on conditions
     *
     * @return void
     */
    public function current()
    {
        parent::validateRoles(['admin']);
        $config          = osrestConfig('auth');
        $loggedUser      = Auth::getSession($config);
        $app             = $loggedUser['app'];
        $model_actividad = new Model_Actividad();
        $result          = $model_actividad->find(['app' => $app]);

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }

    /**
     * Save
     *
     * @return void
     */
    public function save()
    {    
        parent::validateRoles(['admin']);
        $model_actividad = new Model_Actividad();
        $data       = json_decode(file_get_contents("php://input"));
        $config     = osrestConfig('auth');
        $roles      = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data->app) {
                throw new Exception("notAllowed", 1);
            }
        }

        if (isset($data->_id)) {
            $conditions = ['_id' => $data->_id];
            unset($data->_id);
            $result = $model_actividad->update($conditions, ['$set' => $data]);
        } else {
            $result = $model_actividad->insert($data);
        }

        $this->response->sendMessage($data)
            ->setCode(200);
    }
}