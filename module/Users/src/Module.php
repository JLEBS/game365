<?php

namespace Users;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

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
            ],
        ];
    }
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\UsersController::class => function($container) {
                    return new Controller\UsersController(
                        $container->get(Model\UsersTable::class)
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

}

?>