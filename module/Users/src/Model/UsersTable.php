<?php

namespace Users\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class UsersTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($options = [])
    {
        
        return $this->tableGateway->select(function($select) use ($options) {
            if (isset($options['sort'])) {
                $select->order($options['sort']);
            }

            return $select;
        });
    }

    public function getUsers($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveUsers(Users $user)
    {
        $data = [
            'firstname' => $user->firstname,
            'surname'  => $user->surname,
            'dob' => $user->dob,
            'email' => $user->email,
            'username' => $user->username,
            'password' => $user->password,
            'admin' => $user->admin,
        ];

        $id = (int) $user->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (! $this->getUsers($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update User with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteUsers($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}

?>