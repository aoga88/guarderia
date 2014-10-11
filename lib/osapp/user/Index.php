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

    public function upload()
    {
        $config        = osrestConfig('auth');
        $loggedUser    = Auth::getSession($config);
        $model_user    = new Model_User();

        $foto = $_FILES['foto'];

        if ( $foto['error'] !== 0 )
        {
            echo "<html>
                <head>
                </head>
                <body>
                    <script>
                        alert('Ha ocurrido un error al subir la imagen, por favor verifique el archivo.');
                        location.href = '/#/profile/';
                    </script>
                </body>
            </html>";
            $this->setResponse(null);
            return;
        }

        //creamos la imagen en resolucion 250
        $this->generate_image_thumbnail(250, 250, $foto['tmp_name'], '../upload/' . $loggedUser['_id'] . '_250');
        //creamos la imagen en resolucion 80
        $this->generate_image_thumbnail(80, 80, $foto['tmp_name'], '../upload/' . $loggedUser['_id'] . '_80');

        $conditions = ['_id' => $loggedUser['_id']];
        $update = ['foto' => [
            'header' => $foto['type'],
            'url' => '../upload/' . $loggedUser['_id']
        ]];
        $model_user->update($conditions, ['$set' => $update]);
        
        header("location: /#/profile/");
    }

    private function generate_image_thumbnail($new_width, $new_height, $source_image_path, $thumbnail_image_path)
    {
        list($source_image_width, $source_image_height, $source_image_type) = getimagesize($source_image_path);
        switch ($source_image_type) {
            case IMAGETYPE_GIF:
                $source_gd_image = imagecreatefromgif($source_image_path);
                break;
            case IMAGETYPE_JPEG:
                $source_gd_image = imagecreatefromjpeg($source_image_path);
                break;
            case IMAGETYPE_PNG:
                $source_gd_image = imagecreatefrompng($source_image_path);
                break;
        }
        if ($source_gd_image === false) {
            return false;
        }
        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = $new_width / $new_height;
        if ($source_image_width <= $new_width && $source_image_height <= $new_height) {
            $thumbnail_image_width = $source_image_width;
            $thumbnail_image_height = $source_image_height;
        } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
            $thumbnail_image_width = (int) ($new_height * $source_aspect_ratio);
            $thumbnail_image_height = $new_height;
        } else {
            $thumbnail_image_width = $new_width;
            $thumbnail_image_height = (int) ($new_width / $source_aspect_ratio);
        }
        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);
        imagejpeg($thumbnail_gd_image, $thumbnail_image_path, 90);
        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        return true;
    }
}