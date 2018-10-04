<?php

namespace Users\View\Helper;
use Zend\Session\Container;
use Zend\View\Helper\AbstractHelper;

class GetAuthenticatedUser extends AbstractHelper
{
   private $sessionContainer; 

    public function __invoke()
    {
        $session = $this->getSessionContainer();
        return $session->user;
    }

    private function getSessionContainer()
    {
        if (!$this->sessionContainer) {
            $this->sessionContainer = new Container('auth');
        }

        return $this->sessionContainer;
    }
}