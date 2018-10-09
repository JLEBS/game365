<?php
namespace Users\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\Validator\StringLength;

use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\Digits;
use Zend\Validator\Date;
use Zend\ValidatorBetween;

class ResetForm extends Form
{
    static private $inputFilter;

    public function __construct($email = null)
    {
        parent::__construct('login');

        $this->setInputFilter($this->getInputFilter());
        
        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email',
                'required' => true,
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
        'name' => 'email',
        'required' => true,
        'validators'=> [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 6,
                    'max' => 255,
                ],
            ],
        ],
      ]);
    
      return self::$inputFilter = $filter;
    }
}
