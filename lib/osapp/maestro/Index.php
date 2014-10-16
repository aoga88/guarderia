<?php
/**
 * Index.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  Maestro
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\Maestro;

use OsRest\Classes\Controller;
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
 * @package  Maestro
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


        $model_user = new Model_User();
        $result = $model_user->find(['app' => $app, 'roles' => ['$in' => ['maestro']]]);

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
        $model_maestro = new Model_Maestro();
        $data          = json_decode(file_get_contents("php://input"));
        $config        = osrestConfig('auth');
        $loggedUser    = Auth::getSession($config);
        $app           = $loggedUser['app'];
        $roles         = Auth::getSession($config, 'roles');
        $data->app     = $app;

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

        if ($data->_id === "0" && isset($data->email))
        {
            $data->_id = $data->email;
            unset($data->email);
        }

        $userConditions = ['_id' => $data->_id];
        $usuario        = $model_usuario->findOne($userConditions);
        $dataResponse   = clone $data;

        if (!empty($usuario)) {

            $usuario = array_merge($usuario, (array) $data);

            if (!in_array('maestro', $usuario['roles']))
            {
                $usuario['roles'][] = 'maestro';
            }

            unset($usuario['created']);
            $usuario['telefonos'] = $data->telefonos;
            $user_id = $usuario['_id'];
            unset($usuario['_id']);
            $model_usuario->update($userConditions, ['$set' => $usuario]);
            $usuario['_id'] = $user_id;
            if ($loggedUser['_id'] === $data->_id)
            {
                $sessionName = $config['sessionName'];
                $_SESSION[$sessionName]['osrest_user'] = serialize($usuario);
            }
        } else {
            $data->roles    = ['maestro'];
            $plainPassword      = uniqid();
            $data->password = sha1($plainPassword);
            $data->created  = new MongoDate();
            $data->active   = true;
            $data->deleted  = false;
            $data->app      = $currentApp;
            $data->token    = uniqid();
            unset($data->email);

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
            $model_usuario->insert($data);
        }

        $this->response->sendMessage($dataResponse)
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
        $loggedUser      = Auth::getSession($config);
        $app             = $loggedUser['app'];
        $roles        = Auth::getSession($config, 'roles');
        $conditions   = json_decode(file_get_contents("php://input"));
        $conditions->app = $app;
        $model_user = new Model_User();
        $result       = $model_user->find($conditions);
        $data         = iterator_to_array($result);

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $data[$conditions->_id]['app']) {
               throw new Exception("notAllowed", 1);
            }
        }

        $this->response->sendMessage(iterator_to_array($result))
            ->setCode(200);
    }
}