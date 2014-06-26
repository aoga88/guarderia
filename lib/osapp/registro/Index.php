<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Registro
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\Registro;

use OsRest\Classes\Controller;
use OsApp\Models\Alumno as Model_Alumno;
use OsApp\Models\Grupo as Model_Grupo;
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
 * @package  Registro
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
        $model_maestro = new Model_Maestro();
        $result          = $model_maestro->find(['app' => $app]);

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
        $data       = json_decode(file_get_contents("php://input"));
        $config     = osrestConfig('auth');
        $roles      = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data->app) {
                throw new Exception("notAllowed", 1);
            }
        }

        if (isset($data->grupos)) {
            $model = new Model_Grupo();
            foreach ($data->grupos as $grupoId) {
                $grupo = $model->findOne(['_id' => new MongoId($grupoId)]);
                if (!isset($grupo['actividades'])) {
                    $grupo['actividades'] = [];
                }

                foreach ($data->actividades as $actividad) {
                    $grupo['actividades'][] = [
                        'fecha' => new MongoDate(),
                        'actividad' => $actividad
                    ];
                }

                foreach ($grupo['alumnos'] as $alumno) {
                    if (!isset($data->alumnos)) {
                        $data->alumnos = [];
                    }

                    if (!in_array($alumno, $data->alumnos)) {
                        $data->alumnos[] = $alumno;
                    }
                }

                unset($grupo['_id']);
                unset($grupo['created']);

                $model->update(['_id' => new MongoId($grupoId)],['$set' => $grupo]);
            }
        }

        if (isset($data->alumnos)) {
            $model = new Model_Alumno();
            foreach ($data->alumnos as $alumnoId) {
                $alumno = $model->findOne(['_id' => new MongoId($alumnoId)]);
                if (!isset($alumno['actividades'])) {
                    $alumno['actividades'] = [];
                }

                foreach ($data->actividades as $actividad) {
                    $alumno['actividades'][] = [
                        'fecha' => new MongoDate(),
                        'actividad' => $actividad
                    ];
                }

                unset($alumno['_id']);
                unset($alumno['created']);

                $model->update(['_id' => new MongoId($alumnoId)],['$set' => $alumno]);
            }
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
        $model_maestro = new Model_Maestro();
        $result       = $model_maestro->find($conditions);
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