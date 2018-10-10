<?php

namespace Users\InputFilter;
use Zend\InputFilter\InputFilter;
use Zend\Db\Adapter\Adapter;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\Filter\ToInt;
use Zend\Filter\File\RenameUpload;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Validator\StringLength;
use Zend\Validator\Digits;
use Zend\Validator\Date;
use Zend\InputFilter\FileInput;
use Zend\ValidatorBetween;
use Zend\Validator\Db\RecordExists;
use Zend\Validator\Db\NoRecordExists;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;
//use Zend\Mvc\Plugin\FilePrg;

class UserInputFilter extends InputFilter
{
    public $validatePassword = false;

    public function __construct(Adapter $dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
   
    $this->add([
        'name' => 'id',
        'required' => true,
        'filters' => [
            ['name' => ToInt::class],
        ],
    ]);

    $this->add([
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

    $this->add([
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

    $this->add([
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

    $this->add([
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
                    'max' => 255,
                ],
            ],
            [
                'name' => NoRecordExists::class,
                'options' => [
                    'adapter' => $this->dbAdapter,
                    'table' =>  'users',
                    'field' =>  'email',
                ]
            ],
            [
                'name' => EmailAddress::class,
                'options' => [
                    
                ]
            ],
        ],
    ]);

    $this->add([
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
            [
                'name' => NoRecordExists::class,
                'options' => [
                    'adapter' => $this->dbAdapter,
                    'table' =>  'users',
                    'field' =>  'username',
                ]
            ],
        ],
    ]);

    if ($this->validatePassword) {
        $this->add([
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

    $this->add([
        'name' => 'admin',
        'required' => false,
        'filters' => [
            ['name' => StripTags::class], 
        ],
        'validators' => [
            [
                'name' => 'InArray',
                'options' => [
                  'haystack' => [1,0],
                ]
            ],
        ],
    ]);

    $this->add([
        'name' => 'avatar',
        'required' => false,
        'filters' => [
            [
                'name' => RenameUpload::class,
                'options' => [
                    'target' => './public/uploads',
                    'randomize' => true,
                    'use_upload_extension' => true
                ],
            ],
        ],
        'validators' => [
            /* [
                'name' => StringLength::class,
                'options' => [
                    'encoding' => 'UTF-8',
                    'min' => 6,
                    'max' => 255,
                ],
            ], */
            // [
            //     'name' => FilePrg::class,
            //     'options' => [
            //         'adapter' => $this->dbAdapter,
            //         'minSize' =>  64,
            //         'maxSize' =>  1028,
            //         'newFileName' => 'fileName',
            //         'uploadPath' => ''
            //     ],
            // ],
        ],
    ]);

    $this->add([
        'name' => 'join_date',
        'required' => false,
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


}}