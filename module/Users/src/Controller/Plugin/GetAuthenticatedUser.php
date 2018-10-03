<?php

namespace Users\Controller\Plugin;
use Zend\Session\Container;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;


class GetAuthenticatedUser extends AbstractPlugin
{
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
            $this->sessionContainer = new Container('auth');
        }

        return $this->sessionContainer;
    }
}

?>