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
        $model_alumno  = new Model_Alumno();;
        $model_user    = new Model_User();

        $foto = $_FILES['foto'];
        $alumno_id = $_POST['alumno_id'];
        $index = $_POST['contacto_index'];

        if ( $foto['error'] !== 0 )
        {
            echo "<html>
                <head>
                </head>
                <body>
                    <script>
                        alert('Ha ocurrido un error al subir la imagen, por favor verifique el archivo.');
                        location.href = '/#/alumnos/" . $alumno_id . "';
                    </script>
                </body>
            </html>";
            $this->setResponse(null);
            return;
        }

        //creamos la imagen en resolucion 250
        $this->generate_image_thumbnail(250, 250, $foto['tmp_name'], '../upload/' . $alumno_id . '_' . $index . '_250');
        //creamos la imagen en resolucion 80
        $this->generate_image_thumbnail(80, 80, $foto['tmp_name'], '../upload/' . $alumno_id . '_' . $index . '_80');

        $conditions = ['_id' => new MongoId($alumno_id)];
        $update = ['contactos.' . $index . '.foto' => [
            'header' => $foto['type'],
            'url' => '../upload/' . $alumno_id . '_' . $index
        ]];
        $model_alumno->update($conditions, ['$set' => $update]);
        $alumno = $model_alumno->findOne(['_id' => new MongoId($alumno_id)]);

        $conditions = ['_id' => $alumno['contactos'][$index]['_id']];
        $update = ['foto' => [
            'header' => $foto['type'],
            'url' => '../upload/' . $alumno_id . '_' . $index
        ]];
        $model_user->update($conditions, ['$set' => $update]);
        
        header("location: /#/alumnos/" . $alumno_id);
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

    public function showContacto($id, $index, $resolution = 250)
    {
        $availableResolutions = [250, 80];

        if (!in_array($resolution, $availableResolutions))
        {
            $resolution = 250;
        }

        $model_alumno = new Model_Alumno();
        $model_user = new Model_User();
        $alumno = $model_alumno->findOne(['_id' => new MongoId($id)]);
        if (isset($alumno['contactos'][$index]))
        {
            $contacto = $alumno['contactos'][$index];
            $user = $model_user->findOne(['_id' => $contacto['_id']]);
            $foto = $alumno['contactos'][$index]['foto'];

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
        } else {
            $foto = [
                'header' => 'image/png',
                'url' => '../public/img/noimage_' . $resolution . '.png'
            ];
            $useResolution = false;
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
}