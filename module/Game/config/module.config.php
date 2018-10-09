<?php

namespace Game;

use Zend\Router\Http\Segment;

return [
    

    'router' => [
        'routes' => [
            'game' => [
                'type'    => Segment::class,
                'options' => [
                    'route' => '/game[/:action[/:id]]',
                    'constraints' => [
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\GameController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            
        ],
    ],

    'view_manager' => [
        'template_path_stack' => [
            'game' => __DIR__ . '/../view',
        ],
    ],
];

?>
