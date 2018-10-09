<?php

namespace Users\Controller\Plugin;
use Zend\Session\Container;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class GetAdminUser extends AbstractPlugin
{
    private $sessionContainer; 

    public function get()
    {
        $session = $this->getSessionContainer();
        return $session->user;
    }

    public function set($user)
    {
        $session = $this->getSessionContainer();
        $session->user = $user;
    }

    private function getSessionContainer()
    {
        if (!$this->sessionContainer) {
            $this->sessionContainer = new Container('admin');
        }

        return $this->sessionContainer;
    }
}

?>