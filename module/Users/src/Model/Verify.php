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

class Verify
{
    public $id;
    public $date_created;
    public $token;
    public $date_used;
    public $user_id;
    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->date_created  = !empty($data['date_created']) ? $data['date_created'] : null;
        $this->token = !empty($data['token']) ? $data['token'] : null;
        $this->date_used = !empty($data['date_used']) ? $data['date_used'] : null;
        $this->user_id = !empty($data['user_id']) ? $data['user_id'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'date_created' => $this->date_created,
            'token'  => $this->token,
            'date_used' => $this->date_used,
            'user_id' => $this->user_id,
        ];
    }


}
?>

