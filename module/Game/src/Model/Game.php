<?php

namespace Game\Model;

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

class Game implements InputFilterAwareInterface
{
    public $id;
    public $title;
    public $developer;
    public $date;
    public $rating;
    public $img;
    public $online;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->title  = !empty($data['title']) ? $data['title'] : null;
        $this->developer = !empty($data['developer']) ? $data['developer'] : null;
        $this->date = !empty($data['date']) ? $data['date'] : null;
        $this->rating = !empty($data['rating']) ? $data['rating'] : null;
        $this->img = !empty($data['img']) ? $data['img'] : null;
        $this->online = !empty($data['online']) ? $data['online'] : 0;
    }

    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'title' => $this->title,
            'developer'  => $this->developer,
            'date' => $this->date,
            'rating' => $this->rating,
            'img' => $this->img,
            'online' => $this->online,

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
        'name' => 'title',
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
                    'min' => 1,
                    'max' => 100,
                ],
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'developer',
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
                    'min' => 1,
                    'max' => 100,
                ],
            ],
        ],
    ]);

    //Check this
    $inputFilter->add([
        'name' => 'date',
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

    //CHeck this
    $inputFilter->add([
        'name' => 'rating',
        'required' => true,
        'filters' => [
            ['name' => StripTags::class],
            ['name' => StringTrim::class],
        ],
        'validators' => [
            [
                'name' => 'InArray',
                'options' => [
                  'haystack' => [1, 3, 7, 12, 16, 18],
                ]
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'img',
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
                    'min' => 1,
                    'max' => 300,
                ],
            ],
        ],
    ]);

    $inputFilter->add([
        'name' => 'online',
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