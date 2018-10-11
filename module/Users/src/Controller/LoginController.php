<?php

    namespace Users\Controller;
    use Users\Form\LoginForm;
    use Users\Form\ResetForm;
    use Users\Form\PasswordResetForm;
    use Carbon\Carbon;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\Crypt\Password\Bcrypt;
    use Zend\View\Model\ViewModel;
    use Users\Model\Users;
    use Users\Model\UsersTable;
    use Zend\Session\Config\SessionConfig;
    use Zend\Session\Container;
    use Zend\Mail;
    use Zend\Mail\Message;
    use PUGX\Shortid\Shortid;
    use Zend\Mail\Transport\Sendmail as SendmailTransport;
    use Users\Model\Verify;
    use Users\Model\VerifyTable;

class LoginController extends AbstractActionController
{
    private $table;
    private $verifyTable;

    public function __construct(
        UsersTable $table,
        VerifyTable $verifyTable
    ) {
        $this->table = $table;
        $this->verifyTable = $verifyTable;
    }

    public function indexAction()
    {

        if ($user = $this->getAuthenticatedUser()->get()) {

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

    public function passwordresetAction()
    {
        if ($user = $this->getAuthenticatedUser()->get()) {

            $this->flashMessenger()->addMessage('Already logged in!');
            return $this->redirect()
            ->toRoute('game');
        }

        $resetform = new ResetForm(array(
            'action' => 'passwordreset',
            'method' => 'post',
        ));

        $emailrequest = $this->getRequest();

        if (! $emailrequest->isPost()){
            return [
                'resetform' => $resetform
            ];
        }

        $pwdata = $emailrequest->getPost();

        $resetform->setData($pwdata);

        if (!$resetform->isValid()){
            $this->flashMessenger()->addMessage('Please enter your email');
            return $this->redirect()->toRoute('passwordreset');
        }

        $user = $this->table->fetchAll([
            "email" => $pwdata['email']
        ])->current();

        if (!$user)
        {
            echo "hello";
            exit;
        }

        $resetmodel = new Verify();
        $resetmodel->user_id = $user->id;
        $resetmodel->date_created = date('Y-m-d H:i:s');
        $resetmodel->token = ShortID::generate();
        $this->verifyTable->saveTokens($resetmodel);
        $message = new Message();

        $resetLink = $this->url()
            ->fromRoute('passwordreset', [
                'action' => 'setpassword',
                'token' => $resetmodel->token
            ], [
                'force_canonical' => true
            ]);
        
        $message->addFrom('noreply@sidigital.co', 'Game 365');
        $message->addTo($user->email);
        $message->setSubject('Forgotten password verification email');
        $message->setBody("It appeares you have forgotten your password, try to not be so forgetful next time? Click <a href='{$resetLink}'>here.</a>");
        $headers = $message->getHeaders();
       
    ?> 

        <table>
            <tr>
                <th>Date</th>
                <th>From</th>
                <th>To</th>
                <th>Subject</th>
                <th>Content</th>
            </tr>
            <tr>
                <?php
                foreach($headers as $header)
                {
                    echo "<td>".$header->toString()."</td>";
                }
                ?>
                <td><?php echo $message->getBodyText();?></td>
            </tr>
        </table>

    <?php

        $transport = new SendmailTransport();
        $transport->send($message);

        exit;
    }

    public function setpasswordAction()
    {
        if ($user = $this->getAuthenticatedUser()->get()) {

            $this->flashMessenger()->addMessage('Already logged in!');
            return $this->redirect()
            ->toRoute('game');
        }

        $form = new PasswordResetForm();
    
        $request = $this->getRequest();
        $token = $this->params('token');

        if ($token == null)
        {
            $this->flashMessenger()->addMessage('Not a valid token');
            return $this->redirect()
            ->toRoute('passwordreset');
        }
        
        $request = $this->getRequest();

        if (! $request->isPost()){
            return [
                'passwordresetform' => $form
            ];
        }

        $newtime = date('Y-m-d H:i:s', strtotime('-10 Minutes'));

        $verify = $this->verifyTable
            ->fetchAll([
               'token' => $token,
               'unused' => true,
               'created_after' => $newtime,
            ])
            ->current();

        if (!$verify) {
            $this->flashMessenger()->addMessage('Invalid token');
            return $this->redirect()
            ->toRoute('resetpassword');
            exit;
        }
        if ($verify->$newtime )

        try{
            $passwordreset = $this->table->fetchAll($id);
        }
        catch (\Exception $e){
            return $this->redirect()->toRoute('passwordreset', ['action' => 'index']);
        }

        $user = $this->table
            ->fetchAll([
                'id' => $verify->user_id
            ])
            ->current();

        if (!$user) {
            echo 'User does not exist';
            exit;
        }

        $bcrypt = new Bcrypt();

        $form->setData($request->getPost());

        if (!$form->isValid()) {
            $this->flashMessenger()->addMessage('Please Enter a valid password');
            return $this->redirect()
            ->toRoute('setpassword');
            exit;
        }

        $formData = $form->getData();

        $user->password = $bcrypt->create($formData['password']);

        $this->table
            ->saveUsers($user);

        $verify->date_used = date('Y-m-d H:i:s');
        $this->verifyTable->saveTokens($verify);

        return $this->redirect()->toRoute('login', ['action' => 'index']);
        
    }
}

?>