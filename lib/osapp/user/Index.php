<?php
/**
 * index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  User
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\User;

use OsApp\Models\User as Model_User;
use OsApp\Models\Notification as Model_Notification;
use OsRest\Classes\Response;
use OsRest\Classes\Controller;
use Exception;
use OsRest\Auth\Authentication as Auth;
use MongoId;
use MongoDate;
use Swift_Message;
use Swift_Mailer;
use Swift_SmtpTransport;

/**
 * The User/Index Controller
 *
 * @category OsApp
 * @package  User
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
class Index extends Controller
{
    /**
     * Login
     *
     * @return void
     */
    public function login()
    {
        $data       = json_decode(file_get_contents("php://input"));
        $model_user = new Model_User();

        if (!isset($data->email)) {
            $data = (object) ['email' => '', 'password' => ''];
        }

        $conditions = [
            '_id'      => $data->email,
            'password' => $data->password,
            'active'   => true,
            'deleted'  => false
        ];

        $result = $model_user->findOne($conditions);

        if (empty($result)) {
            $this->response->sendMessage('Usuario o contraseña incorrecto')
                ->setCode(500);
        } else {
            unset($result['password']);
            $this->response->sendMessage($result)
                ->setCode(200);

            $config = osrestConfig('auth');
            Auth::createSession($config, $result);
        }    
    }

    /**
     * Current
     *
     * @return void
     */
    public function current()
    {
        $config  = osrestConfig('auth');
        $session = Auth::getSession($config);

        $this->response->sendMessage($session)
            ->setCode(200);
    }

    public function getPicture($id, $resolution)
    {
        $model_user = new Model_User();
        $user = $model_user->findOne(['_id' => $id]);

        $availableResolutions = [250, 80];

        if (!in_array($resolution, $availableResolutions))
        {
            $resolution = 250;
        }

        if (!isset($user['foto']))
        {
            $foto = [
                'header' => 'image/png',
                'url' => '../public/img/noimage_' . $resolution . '.png'
            ];
            $useResolution = false;
        } else {
            $foto = $user['foto'];
            $useResolution = true;
        }

        header('Content-Type: ' . $foto['header']);
        $this->setResponse(null);
        if ($useResolution)
        {
            echo file_get_contents(getcwd() . '/' . $foto['url'] . '_' . $resolution);
        } else {
            echo file_get_contents(getcwd() . '/' . $foto['url']);
        }
        
    }

    /**
     * Logout
     *
     * @return void
     */
    public function logout()
    {
        @session_start();
        @session_destroy();
        $this->response->sendMessage(true)
            ->setCode(200);
    }

    /**
     * Find
     *
     * @return void
     */
    public function find()
    {
        parent::validateRoles(['superadmin', 'admin', 'maestro', 'padre']);
        $conditions = json_decode(file_get_contents("php://input"));

        $config    = osrestConfig('auth');
        $roles     = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles) && !in_array('padre', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $conditions->app) {
                throw new Exception("notAllowed", 1);
            }
        }

        if (isset($conditions->app)) {
            unset($conditions->app);
        }

        $model_user  = new Model_User();
        
        $result = $model_user->realFind($conditions);

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }

    /**
     * NewUser
     *
     * @return void
    */
    public function newUser()
    {    
        parent::validateRoles(['admin', 'superadmin']);
        $model_user     = new Model_User();
        $data           = json_decode(file_get_contents("php://input"));

        $conditions = json_decode(file_get_contents("php://input"));

        $config    = osrestConfig('auth');
        $roles     = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data->app) {
                throw new Exception("notAllowed", 1);
            }
        }

        $plainPassword  = uniqid();
        $data->password = sha1($plainPassword);
        $data->created  = new MongoDate();
        $data->active   = true;
        $data->deleted  = false;
        $data->token    = uniqid();
        $result         = $model_user->insert($data);

        $htmlMessage = <<<EOD
            <html>
                <body>
                    <h1>Bienvenido a tu sistema de guarderia</h1>
                    <p>
                        Has sido registrado en guarderia.os-cloud.net ahora 
                        podrás ingresar con los siguientes datos:
                    </p>
                    <table>
                        <tr>
                            <th style="text-align: left">Usuario:</th>
                            <td>{$data->_id}</td>
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
          ->setTo(array($data->_id => $data->name))
          ->setBody($htmlMessage, 'text/html');

        $transport = Swift_SmtpTransport::newInstance(getenv('OSCLOUD_SMTP'), 25)
          ->setUsername(getenv('OSCLOUD_MAIL'))
          ->setPassword(getenv('OSCLOUD_MAILPASSWORD'));

        $mailer = Swift_Mailer::newInstance($transport);
        $result = $mailer->send($message);

        $this->response->sendMessage($data)
            ->setCode(200);
    }

    /**
     * Obtiene las acciones sin leer
     *
     * @return void
     */
    public function notifications()
    {
        $config     = osrestConfig('auth');
        $loggedUser = Auth::getSession($config);

        $model_notificacion = new Model_Notification();
        $notifications = $model_notificacion->find(['user' => $loggedUser['_id'], 'leido' => false]);

        $this->response->sendMessage(iterator_to_array($notifications))
            ->setCode(200);
    }

    public function readNotification($id)
    {
        $model_notificacion = new Model_Notification();
        $model_notificacion->update(['_id' => new MongoId($id)], ['$set' => ['leido' => true]]);
        $this->response->sendMessage(['res' => true])
            ->setCode(200);
    }

    /**
     * Save
     *
     * @return void
     */
    public function save()
    {    
        parent::validateRoles(['superadmin', 'admin', 'maestro', 'padre']);
        $model_user = new Model_User();
        $data       = json_decode(file_get_contents("php://input"));

        $config    = osrestConfig('auth');
        $roles     = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles) && !in_array('padre', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data->app) {
                throw new Exception("notAllowed", 1);
            }
        }

        if (isset($data->_id)) {
            $conditions = ['_id' => $data->_id];
            unset($data->_id);
            if (isset($data->alumnos)) {
                foreach ($data->alumnos as $index => $alumno) {
                    $alumno = (array) $alumno;
                    $data->alumnos[$index] = new MongoId($alumno['$id']);
                }
            }
            $result = $model_user->update($conditions, ['$set' => $data]);
        } else {
            $result = $model_user->insert($data);
        }

        $this->response->sendMessage($data)
            ->setCode(200);
    }
}