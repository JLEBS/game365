<?php

namespace Game\Model;

use RuntimeException;
use Zend\Db\TableGateway\TableGatewayInterface;

class GameTable
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

    public function getGame($id)
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

    public function saveGame(Game $game)
    {
        $data = [
            'title' => $game->title,
            'developer'  => $game->developer,
            'date' => $game->date,
            'rating' => $game->rating,
            'img' => $game->img,
            'online' => $game->online,
        ];

        $id = (int) $game->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        if (! $this->getGame($id)) {
            throw new RuntimeException(sprintf(
                'Cannot update game with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteGame($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}

?>