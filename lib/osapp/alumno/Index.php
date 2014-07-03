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
use OsApp\Models\User as Model_User;
use OsApp\Models\Maestro as Model_Maestro;
use OsApp\Models\Grupo as Model_Grupo;
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
        parent::validateRoles(['admin', 'superadmin', 'padre', 'maestro']);
        $config  = osrestConfig('auth');
        $roles   = Auth::getSession($config, 'roles');
        $data    = json_decode(file_get_contents("php://input"));
        $currentApp = Auth::getSession($config, 'app');

        if (!isset($data->app)) {
            $data->app = new MongoId($currentApp);
        } else {
            unset($data->app);
        }

        $model_usuario = new Model_User();
        $model_alumno  = new Model_Alumno();

        if (isset($data->_id) && $data->_id != 0) {
            $conditions = ['_id' => new MongoId($data->_id)];
            $alumnoId = $data->_id;
            unset($data->_id);
            unset($data->created);
            $aresult  = $model_alumno->update($conditions, ['$set' => $data]);
            $data->_id = new MongoId($alumnoId);
        } else {
            if (isset($data->_id)) {
                unset($data->_id);
            }

            $aresult  = $model_alumno->insert($data);
            $alumnoId = $data->_id;
        }

        foreach ($data->contactos as $contacto) {
            $userConditions = ['_id' => $contacto->_id];
            $usuario = $model_usuario->findOne($userConditions);

            if (!empty($usuario)) {
                if (!in_array('padre', $usuario['roles']))
                {
                    $usuario['roles'][] = 'padre';
                }

                unset($usuario['created']);
                $usuario['telefonos'] = $contacto->telefonos;
                if (!isset($usuario['alumnos']))
                {
                    $usuario['alumnos'] = [$alumnoId];
                } else {
                    if (!in_array($alumnoId, $usuario['alumnos'])) {
                        $usuario['alumnos'][] = $alumnoId;
                    }
                }
                $model_usuario->update($userConditions, ['$set' => $usuario]);
            } else {
                $contacto->roles    = ['padre'];
                $plainPassword      = uniqid();
                $contacto->password = sha1($plainPassword);
                $contacto->created  = new MongoDate();
                $contacto->active   = true;
                $contacto->deleted  = false;
                $contacto->alumnos  = [$alumnoId];
                $contacto->token    = uniqid();

                $htmlMessage = <<<EOD
                <html>
                    <body>
                        <h1>Bienvenido a tu sistema de guarderia</h1>
                        <p>
                            Has sido registrado en http://guarderia.os-cloud.net ahora 
                            podrás ingresar con los siguientes datos:
                        </p>
                        <table>
                            <tr>
                                <th style="text-align: left">Usuario:</th>
                                <td>{$contacto->_id}</td>
                            </tr>
                            <tr>
                                <th style="text-align: left">Password:</th>
                                <td>{$plainPassword}</td>
                            </tr>
                        </table>
                    </body>
                </html>
EOD;

                $message = Swift_Message::newInstance()
                  ->setSubject('Bienvenido a tu sistema de guarderia')
                  ->setFrom(array(getenv('OSCLOUD_MAIL')))
                  ->setTo(array($contacto->_id => $contacto->name))
                  ->setBody($htmlMessage, 'text/html');

                $transport = Swift_SmtpTransport::newInstance(getenv('OSCLOUD_SMTP'), 25)
                  ->setUsername(getenv('OSCLOUD_MAIL'))
                  ->setPassword(getenv('OSCLOUD_MAILPASSWORD'));

                $mailer = Swift_Mailer::newInstance($transport);
                $result = $mailer->send($message);
                $model_usuario->insert($contacto);
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
        parent::validateRoles(['admin', 'maestro', 'padre']);
        $config       = osrestConfig('auth');
        $roles        = Auth::getSession($config, 'roles');
        $loggedUser   = Auth::getSession($config);
        $conditions   = json_decode(file_get_contents("php://input"));
        $model_alumno = new Model_Alumno();
        $result       = $model_alumno->find($conditions);
        $data         = iterator_to_array($result);
        $isValid      = false;

        if (in_array('maestro', $roles)) {
            $model_maestro = new Model_Maestro();
            $maestro       = $model_maestro->findOne(['email' => $loggedUser['_id']]);
            $maestroId     = $maestro['_id']->__toString();

            $model_grupo = new Model_Grupo();
            $grupos      = $model_grupo->find(['maestros' => ['$in' => [$maestroId]]]);
            $alumnos     = [];
            
            foreach($grupos as $grupo) {
                $alumnos = array_merge($alumnos, $grupo['alumnos']);
            }

            foreach ($alumnos as $index => $alumno) {
                if ($alumno instanceof MongoId) {
                    $alumnos[$index] = $alumno->__toString();
                }
            }

            if (in_array($conditions->_id, $alumnos)) {
                $isValid = true;
            }
        }

        if (in_array('padre', $roles)) {
            $alumnos = $loggedUser['alumnos'];

            foreach ($alumnos as $index => $alumno) {
                if ($alumno instanceof MongoId) {
                    $alumnos[$index] = $alumno->__toString();
                }
            }

            if (in_array($conditions->_id, $alumnos)) {
                $isValid = true;
            }
        }

        if ($isValid || in_array('admin', $roles)) {
            $result = $model_alumno->find($conditions);
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
        parent::validateRoles(['admin', 'maestro', 'padre']);
        $config       = osrestConfig('auth');
        $roles        = Auth::getSession($config, 'roles');
        $loggedUser   = Auth::getSession($config);
        
        $model_alumno = new Model_Alumno();

        $alumnos = [];

        if (in_array('maestro', $roles)) {
            $model_maestro = new Model_Maestro();
            $maestro = $model_maestro->findOne(['email' => $loggedUser['_id']]);

            if (isset($maestro['_id']))
            {
                $maestroId = $maestro['_id']->__toString();

                $model_grupo = new Model_Grupo();
                $grupos = $model_grupo->find(['maestros' => ['$in' => [$maestroId]]]);
                
                foreach($grupos as $grupo) {
                    $alumnos = array_merge($alumnos, $grupo['alumnos']);
                }
            }
        }

        if (in_array('padre', $roles)) {
            //var_dump($loggedUser);
            $alumnos = array_merge($alumnos, $loggedUser['alumnos']);
        }

        foreach ($alumnos as $index => $alumno) {
            if (!$alumno instanceof MongoId) {
                $alumnos[$index] = new MongoId($alumno);
            }
        }

        $alumnos = $model_alumno->find(['_id' => ['$in' => $alumnos]]);
        $alumnos = iterator_to_array($alumnos);

        if (in_array('admin', $roles)) {
            $app          = $loggedUser['app'];
            $result = $model_alumno->find(['app' => new MongoId($app)]);
            $alumnos = iterator_to_array($result);
        }

        $this->response->sendMessage($alumnos)
            ->setCode(200);
    }
}