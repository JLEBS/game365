<?php
namespace Game\Form;

use Zend\Form\Form;

class GameForm extends Form
{
    public function __construct($name = null)
    {
        // We will ignore the name provided to the constructor
        parent::__construct('game');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'title',
            'type' => 'text',
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'developer',
            'type' => 'text',
            'options' => [
                'label' => 'Developer',
            ],
        ]);
        $this->add([
            'name' => 'date',
            'type' => 'date',
            'options' => [
                'label' => 'Date',
            ],
        ]);
        $this->add([
            'name' => 'rating',
            'type' => 'Zend\Form\Element\Select',
            'options' => [
                'empty_option' => 'Please select',
                'label' => 'PEGI Rating',
              
                'value_options'=> [
                    3 => '3', 7 => '7', 12 => '12', 16 => '16', 18 => '18', 
                    
                ],
            ],
        ]);
        $this->add([
            'name' => 'img',
            'type' => 'text',
            'options' => [
                'label' => 'Image',
            ],
        ]);
        $this->add([
            'name' => 'online',
            'type' => 'Zend\Form\Element\Select',
            'options' => [
                'empty_option' => 'Please select',
                'label' => 'Multiplayer',
                    'value_options'=> [
                        1 => "Yes",
                        0 => "No",
                    ],
            ],
        ]);
        if (isset($options['include_userid'])){
            $this->add([
                'name' => 'userid',
                'type' => 'hidden',
            ]);
        }
        $this->add([
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
