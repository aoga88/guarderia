<?php
/**
 * App.php
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
use OsRest\Classes\Response;
use OsRest\Classes\Controller;
use Exception;
use OsRest\Auth\Authentication as Auth;

/**
 * The User/App Controller
 *
 * @category OsApp
 * @package  User
 * @author   Osvaldo Garcia <aoga88@gmail.com>
 * @license  This cannot be reproduced without explicit permission
 * @link     http://santander.os-cloud.net 
 **/
class App extends Controller
{
    /**
     * Gets the users from app
     *
     * @param string $id The id
     *
     * @return void
     */
    public function getFromApp($id)
    {
        parent::validateRoles(['superadmin', 'admin']);

        $config    = osrestConfig('auth');
        $roles     = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $id) {
                throw new Exception("notAllowed", 1);
            }
        }

        $model_user = new Model_User();
        $users      = iterator_to_array(
            $model_user->find(['app' => $id])
        );
        $this->response->sendMessage($users)
            ->setCode(200);
    }

    /**
     * Gets the admins from app
     *
     * @param string $id The id
     *
     * @return void
     */
    public function getAdminsFromApp($id)
    {
        parent::validateRoles(['superadmin', 'admin']);

        $config    = osrestConfig('auth');
        $roles     = Auth::getSession($config, 'roles');

        if (!in_array('superadmin', $roles)) {
            $currentApp = Auth::getSession($config, 'app');
            if ($currentApp !== $id) {
                throw new Exception("notAllowed", 1);
            }
        }

        $model_user = new Model_User();
        $users = iterator_to_array(
            $model_user->find(['app' => $id, 'roles' => ['$in' => ['admin']]])
        );
        $this->response->sendMessage($users)
            ->setCode(200);
    }

}