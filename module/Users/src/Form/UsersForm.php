<?php
namespace Users\Form;

use Zend\Form\Form;

class UsersForm extends Form
{
    public function __construct($options = [])
    {
        parent::__construct('user');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);
        $this->add([
            'name' => 'firstname',
            'type' => 'text',
            'options' => [
                'label' => 'Firstname',
            ],
        ]);
        $this->add([
            'name' => 'surname',
            'type' => 'text',
            'options' => [
                'label' => 'Surname',
            ],
        ]);
        $this->add([
            'name' => 'dob',
            'type' => 'date',
            'options' => [
                'label' => 'DOB',
            ],
        ]);
        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
            ],
        ]);
        if (isset($options["includepassword"])) {
            $this->add([
                'name' => 'password',
                'type' => 'password',
                'options' => [
                    'label' => 'Password',
                ],
            ]);
        }
        if (isset($options["includeAdmin"])) {
            $this->add([
                'name' => 'admin',
                'type' => 'Zend\Form\Element\Select',
                'options' => [
                    'empty_option' => 'Please select',
                    'label' => 'Admin',
                        'value_options'=> [
                            1 => "Yes",
                            0 => "No",
                        ],
                ],
            ]);
        }
        
        
            $this->add([
                'name' => 'avatar',
                'type' => 'file',
                'options' => [
                    'label' => 'Avatar',
                ],
            ]);
        
        

        if (isset($options["includejoin_date"])){
            $this->add([
                'name' => 'join_date',
                'type' => 'date',
                'options' => [
                    'label' => 'Date Joined',
                ],
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
