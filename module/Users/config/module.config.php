<?php

namespace Users;

use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'router' => [
        'routes' => [
            'users' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/users[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\UsersController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'login' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/login',
                    'defaults' => [
                        'controller' => Controller\LoginController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'users' => __DIR__ . '/../view',
        ],
    ],

    'controller_plugins' => [
        'aliases' => [
            'getAuthenticatedUser' => Controller\Plugin\GetAuthenticatedUser::class
        ],
        'factories' => [
            Controller\Plugin\GetAuthenticatedUser::class => InvokableFactory::class
        ],
    ],
];

?>