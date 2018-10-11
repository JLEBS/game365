<?php

namespace Users\Controller\Plugin;
use Zend\Session\Container;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\Session\Config\StandardConfig;
use Zend\Session\SessionManager;

class GetAuthenticatedUser extends AbstractPlugin
{
    private $sessionContainer; 

    public function get()
    {
        $session = $this->getSessionContainer();
        $this->touchSessionCOntainer();
        return $session->user;
    }

    public function set($user)
    {
        $session = $this->getSessionContainer();
        $session->user = $user;
    }
    
    public function touchSessionContainer()
    {
        $this->getSessionContainer()->setExpirationSeconds(600);
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