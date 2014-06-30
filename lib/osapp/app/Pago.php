<?php
/**
 * Pago.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  App
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
namespace OsApp\App;

use OsRest\Classes\Controller;
use OsApp\Models\App as Model_App;
use MongoId;
use MongoDate;
use OsRest\Auth\Authentication as Auth;

/**
 * Pago.php
 *
 * PHP version 5
 *
 * @category OsApp
 * @package  App
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 */
class Pago extends Controller
{
    /**
     * Saves the App
     *
     * @return void
     */
    public function pagar()
    {
        parent::validateRoles(['superadmin']);
        $data = json_decode(file_get_contents("php://input"));
        $pago = $data->guarderia->pagos[$data->indexPago];
        
        $model_app = new Model_App();
        $pago->pago = new MongoDate();

        $fecha = strtotime(gmdate("Y-m-d", $pago->fecha->sec) . ' +1 month');

        $model_app->update(['_id' => new MongoId($data->guarderia->_id)], [
        		'$set' => [
        			'pagos.' . $data->indexPago . '.pago' => new MongoDate(),
                    'pagos.' . ($data->indexPago + 1) . '.fecha' => new MongoDate($fecha),
                    'pagos.' . ($data->indexPago + 1) . '.monto' => 500
        		]
        	]);

        $this->response->sendMessage($pago)
            ->setCode(200);
    }
}