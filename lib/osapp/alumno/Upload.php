<?php
/**
 * Upload.php
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
use MongoId;
use MongoDate;
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
class Upload extends Controller
{
    /**
     * Searchs based on conditions
     *
     * @return void
     */
    public function contacto()
    {
        parent::validateRoles(['admin', 'padre']);
        $config        = osrestConfig('auth');
        $loggedUser    = Auth::getSession($config);
        $app           = $loggedUser['app'];
        $model_alumno   = new Model_Alumno();;

        $foto = $_FILES['foto'];
        $alumno_id = $_POST['alumno_id'];
        $index = $_POST['contacto_index'];

        copy($foto['tmp_name'], '../upload/' . $alumno_id . '_' . $index);
        $conditions = ['_id' => new MongoId($alumno_id)];
        $update = ['contactos.' . $index . '.foto' => [
            'header' => $foto['type'],
            'url' => '../upload/' . $alumno_id . '_' . $index
        ]];
        $model_alumno->update($conditions, ['$set' => $update]);
        
        header("location: /#/alumnos/" . $alumno_id);
    }

    public function showContacto($id, $index)
    {
        $model_alumno = new Model_Alumno();
        $alumno = $model_alumno->findOne(['_id' => new MongoId($id)]);
        if (isset($alumno['contactos'][$index]['foto']))
        {
            $foto = $alumno['contactos'][$index]['foto'];    
        } else {
            $foto = [
                'header' => 'image/png',
                'url' => '../public/img/noimage.png'
            ];
        }
        

        header('Content-Type: ' . $foto['header']);
        $this->setResponse(null);
        echo file_get_contents(getcwd() . '/' . $foto['url']);
    }
}