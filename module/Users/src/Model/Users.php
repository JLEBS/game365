<?php

namespace Users\Model;

use DomainException;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use Zend\Validator\Digits;
use Zend\Validator\Date;
use Zend\ValidatorBetween;

class Users implements InputFilterAwareInterface
{
    public $id;
    public $firstname;
    public $surname;
    public $dob;
    public $email;
    public $username;
    public $password;
    public $admin;
    public $validatePassword = true;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->firstname  = !empty($data['firstname']) ? $data['firstname'] : null;
        $this->surname = !empty($data['surname']) ? $data['surname'] : null;
        $this->dob = !empty($data['dob']) ? $data['dob'] : null;
        $this->email = !empty($data['email']) ? $data['email'] : null;
        $this->username = !empty($data['username']) ? $data['username'] : null;
        $this->password = !empty($data['password']) ? $data['password'] : null;
        $this->admin = !empty($data['admin']) ? $data['admin'] : 0;
    }

    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'firstname' => $this->firstname,
            'surname'  => $this->surname,
            'dob' => $this->dob,
            'email' => $this->email,
            'username' => $this->username,
            'password' => $this->password,
            'admin' => $this->admin,
        ];
    }


    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
    if ($this->inputFilter) {
        return $this->inputFilter;
    }

    $inputFilter = new InputFilter();

    $inputFilter->add([
        'name' => 'id',
        'required' => true,
        'filters' => [
            ['name' => ToInt::class],
        ],
    ]);

    $inputFilter->add([
        'name' => 'firstname',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 2,
                    'max' => 50,
                ],
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'surname',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 2,
                    'max' => 50,
                ],
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'dob',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
          [
                'name' => 'Date',
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'email',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 6,
                    'max' => 50,
                ],
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'username',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
            [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 6,
                    'max' => 50,
                ],
            ],
        ],
    ]);

    if ($this->validatePassword) {
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 6,
                        'max' => 50,
                    ],
                ],
            ],
        ]);
    }


    $inputFilter->add([
        'name' => 'admin',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
           
        ],
        'validators' => [
            [
                'name' => 'InArray',
                'options' => [
                  'haystack' => [1, 0],
                ]
            ],
        ],
    ]);


    $this->inputFilter = $inputFilter;
    return $this->inputFilter;
    }
}
?>

