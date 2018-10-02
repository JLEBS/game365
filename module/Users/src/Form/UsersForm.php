<?php
namespace Users\Form;

use Zend\Form\Form;

class UsersForm extends Form
{
    public function __construct($options = [])
    {
        // We will ignore the name provided to the constructor
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
