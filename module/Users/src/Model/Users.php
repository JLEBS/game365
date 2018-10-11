<?php

namespace Users\Model;

use DomainException;

class Users
{
    public $id;
    public $firstname;
    public $surname;
    public $dob;
    public $email;
    public $username;
    public $password;
    public $admin;
    public $avatar;
    public $join_date;
    public $active;
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
        $this->avatar = !empty($data['avatar']) ? $data['avatar'] : null;
        $this->join_date = !empty($data['join_date']) ? $data['join_date'] : null;
        $this->active = !empty($data['active']) ? $data['active'] : null;
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
            'avatar' => $this->avatar,
            'join_date' => $this->join_date,
            'active' => $this->active,
        ];
    }
}
?>

