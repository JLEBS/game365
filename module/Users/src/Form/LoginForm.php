<?php
namespace Users\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LoginForm extends Form
{
    static private $inputFilter;

    public function __construct($name = null)
    {
        parent::__construct('login');

        $this->setInputFilter($this->getInputFilter());
        
        $this->add([
            'name' => 'username',
            'type' => 'text',
            'options' => [
                'label' => 'Username',
                'required' => true,
            ],
        ]);
      
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
                'required' => true,
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

    public function getInputFilter()
    {
    
      if(isset (self::$inputFilter)) {
        return self::$inputFilter;
      }
    
      $filter = new InputFilter();
    
      $filter->add([
        'name' => 'username',
        'required' => true,
      ]);
    
      $filter->add([
        'name' => 'password',
        'required' => true,
      ]);
    
      return self::$inputFilter = $filter;
    }
}
