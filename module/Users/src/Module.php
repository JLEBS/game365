<?php

namespace Users;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\UsersTable::class => function($container) {
                    $tableGateway = $container->get(Model\UsersTableGateway::class);
                    return new Model\UsersTable($tableGateway);
                },
                Model\UsersTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Users());
                    return new TableGateway('users', $dbAdapter, null, $resultSetPrototype);
                },
                Model\VerifyTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Verify());
                    return new TableGateway('verify', $dbAdapter, null, $resultSetPrototype);
                },
                Model\VerifyTable::class => function($container) {
                    $tableGateway = $container->get(Model\VerifyTableGateway::class);
                    return new Model\VerifyTable($tableGateway);
                },
                InputFilter\UserInputFilter::class => InputFilter\Factory\UserInputFilterFactory::class
            ],
        ];
    }
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\UsersController::class => function($container) {
                    return new Controller\UsersController(
                        $container->get(Model\UsersTable::class),
                        $container->get(InputFilter\UserInputFilter::class)
                    );
                },
                Controller\LoginController::class => function($container) {
                    return new Controller\LoginController(
                        $container->get(Model\UsersTable::class),
                        $container->get(Model\VerifyTable::class)
                    );
                },
            ],
        ];
    }
    public function onBootstrap($e)
    {
         // Get event manager.
         $eventManager = $e->getApplication()->getEventManager();
         $sharedEventManager = $eventManager->getSharedManager();

         // Register the event listener method. 
         $sharedEventManager->attach(AbstractActionController::class, 
                 MvcEvent::EVENT_DISPATCH, [ $this, 'updateUserSession' ], 100);
    }

    public function updateUserSession($e)
    {
        $application = $e->getApplication();
        $services = $application->getServiceManager();
        
        $getAuthenticatedUser = $services
            ->get('ControllerPluginManager')
            ->get('getAuthenticatedUser');
        
        $userTable = $services->get(Model\UsersTable::class);

        // var_dump($getAuthenticatedUser);
        // exit;

        $user = $getAuthenticatedUser->get();

        if (!$user) {
            return;
        }

        $nextUser = $userTable
            ->fetchAll([
                'id' => $user->id
            ])
            ->current();
        
        $getAuthenticatedUser->set($nextUser);
    }

}

?>