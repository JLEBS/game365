<?php

    namespace Users\Controller;
    use Users\Form\LoginForm;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\Crypt\Password\Bcrypt;
    use Zend\View\Model\ViewModel;
    use Users\Model\Users;
    use Users\Model\UsersTable;
    use Zend\Session\Config\SessionConfig;
    use Zend\Session\Container;

class LoginController extends AbstractActionController
{

    private $table;

    public function __construct(UsersTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {

        if ($user = $this->getAuthenticatedUser()->get()) {

            //only username must be right
            //$this->flashMessenger()->addMessage('Already logged in!');
            return $this->redirect()
            ->toRoute('game');
        }


        $loginform = new LoginForm(array(
            'action' => '/login/process',
            'method' => 'post',
        ));

        $request = $this->getRequest();
    
         if (! $request->isPost()) {
            return  [
                'loginform' => $loginform
            ];
        }
        
        $data = $request->getPost();

        $loginform->setData($data);

        if (!$loginform->isValid()) {
            $this->flashMessenger()->addMessage('Please enter your username and password');
            return $this->redirect()->toRoute('login');
        }

        $user = $this->table->fetchAll([
            "username" => $data['username']
        ])->current();

        $bcrypt = new Bcrypt();
        $password = $data['password'];

        if (!$user) {

            $this->flashMessenger()->addMessage('Username & Password do not match!');

            return $this->redirect()
            ->toRoute('login');
            exit;
        }
        if (!$bcrypt->verify($password, $user->password))
        {

            $this->flashMessenger()->addMessage('Password does not match!');
            return $this->redirect()
            ->toRoute('login');

    
            exit;
        }

        $this->getAuthenticatedUser()->set($user);

        return $this->redirect()
            ->toRoute('login');
    }

    public function logoutAction()
    {
        $this->getAuthenticatedUser()->set(null);
        return $this->redirect()->toRoute('login');

    }


}

?>