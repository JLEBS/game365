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

        $user = $this->table->fetchAll([
            "username" => $data['username']
        ])->current();

        $bcrypt = new Bcrypt();
        $password = $data['password'];

        if (!$user) {
            echo 'Failed (user does not exist)';
            exit;
        }
        if (!$bcrypt->verify($password, $user->password))
        {
            return $this->redirect()
            ->toRoute('users');

            echo 'Failed (password does not match)';
            exit;
        }

        $this->getAuthenticatedUser()->set($user);

        return $this->redirect()
            ->toRoute('game');
    }



}

?>