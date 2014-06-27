<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Grupo
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\Grupo;

use OsRest\Classes\Controller;
use OsApp\Models\Grupo as Model_Grupo;
use OsApp\Models\Maestro as Model_Maestro;
use OsApp\Models\User as Model_User;
use MongoId;
use MongoDate;
use OsRest\Auth\Authentication as Auth;
use Exception;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_Mailer;

/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Grupo
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
        parent::validateRoles(['admin', 'maestro']);
        $config        = osrestConfig('auth');
        $loggedUser    = Auth::getSession($config);
        $app           = $loggedUser['app'];
        $model_grupo   = new Model_Grupo();
        $model_maestro = new Model_Maestro();
        $conditions    = ['app' => $app];

        if (in_array('maestro', $loggedUser['roles'])) {
            $maestro = $model_maestro->findOne(['email' => $loggedUser['_id']]);
            $conditions['maestros'] = ['$in' => [$maestro['_id']->__toString()]];
        }

        $result        = $model_grupo->find($conditions);
        $result        = iterator_to_array($result);

        foreach($result as $index => $grupo) {
            foreach($grupo['maestros'] as $indexMaestro => $maestro) {
                $result[$index]['maestros'][$indexMaestro] = $model_maestro->findOne(['_id' => new MongoId($maestro)]);
            }
        }

        $this->response->sendMessage($result)
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
        $model_grupo = new Model_Grupo();
        $data       = json_decode(file_get_contents("php://input"));
        $config     = osrestConfig('auth');
        $roles      = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data->app) {
                throw new Exception("notAllowed", 1);
            }
        }

        $model_usuario = new Model_User();

        if (!isset($data->active)) {
        	$data->active = true;
        }

        if (isset($data->_id) && $data->_id !== "0") {
            $conditions = ['_id' => new MongoId($data->_id)];
            unset($data->_id);
            $result = $model_grupo->update($conditions, ['$set' => $data]);
            $data->_id = $conditions['_id'];
        } else {
            unset($data->_id);
            $result = $model_grupo->insert($data);
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
        $model_grupo = new Model_Grupo();
        $result       = $model_grupo->find($conditions);
        $data         = iterator_to_array($result);

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data[$conditions->_id->__toString()]['app']) {
               throw new Exception("notAllowed", 1);
            }
        }

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }
}