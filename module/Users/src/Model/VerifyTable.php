<?php

namespace Users\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;
use Users\Model\Verify;

class VerifyTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll($options = [])
    {
        
        return $this->tableGateway->select(function($select) use ($options) {
            if (isset($options['token'])) {
                $select->where([ 'token' => $options['token'] ]);
            }

            if (isset($options['unused'])) {
                $select->where->isNull('date_used');
            }

            if (isset($options['created_after'])){

                $select->where->greaterThanOrEqualTo('date_created', $options['created_after']);
            }
            return $select;
        });
    }

    public function saveTokens(Verify $token)
    {
        $data = $token->getArrayCopy();

        $id = (int) $token->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteToken($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}

?>