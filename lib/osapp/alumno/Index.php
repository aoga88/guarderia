<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Alumno
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\Alumno;

use OsRest\Classes\Controller;
use OsApp\Models\Alumno as Model_Alumno;
use MongoId;
use OsRest\Auth\Authentication as Auth;
use Exception;

/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Alumno
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
class Index extends Controller
{
    /**
     * Saves the Alumno
     *
     * @return void
     */
    public function save()
    {
        parent::validateRoles(['admin', 'superadmin']);
        $config  = osrestConfig('auth');
        $roles   = Auth::getSession($config, 'roles');
        $data    = json_decode(file_get_contents("php://input"));
        $currentApp = Auth::getSession($config, 'app');

        if (!isset($data->app)) {
            $data->app = new MongoId($currentApp);
        } else {
            unset($data->app);
        }

        $model_alumno = new Model_Alumno();

        if (isset($data->_id) && $data->_id != 0) {
            $conditions = ['_id' => new MongoId($data->_id)];
            unset($data->_id);
            $result = $model_alumno->update($conditions, ['$set' => $data]);
        } else {
            if (isset($data->_id)) {
                unset($data->_id);
            }

            $result = $model_alumno->insert($data);
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
        parent::validateRoles(['superadmin', 'admin']);
        $config       = osrestConfig('auth');
        $roles        = Auth::getSession($config, 'roles');
        $conditions   = json_decode(file_get_contents("php://input"));
        $model_alumno = new Model_Alumno();
        $result       = $model_alumno->find($conditions);
        $data         = iterator_to_array($result);

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data[$conditions->_id->__toString()]['app']->__toString()) {
                throw new Exception("notAllowed", 1);
            }
        }

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }

    /**
     * Searchs based on conditions
     *
     * @return void
     */
    public function current()
    {
        parent::validateRoles(['admin']);
        $config       = osrestConfig('auth');
        $loggedUser   = Auth::getSession($config);
        $app          = $loggedUser['app'];
        $model_alumno = new Model_Alumno();
        $result       = $model_alumno->find(['app' => new MongoId($app)]);

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }
}