<?php

    namespace Users\Controller;

    use Zend\Crypt\Password\Bcrypt;
    use Users\Form\UsersForm;
    use Users\Form\LoginForm;
    use Users\Model\Users;
    use Users\Model\UsersTable;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    use Users\InputFilter\UserInputFilter;
    
class UsersController extends AbstractActionController
{
    private $table;
    private $inputFilter;

    public function __construct(UsersTable $table, UserInputFilter $inputFilter)
    {
        $this->table = $table;
        $this->inputFilter = $inputFilter;
    }

    public function indexAction()
    {

        if (!$user = $this->getAuthenticatedUser()->get()) 
        {
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        $sortableAttributes = [
            'avatar',
            'id',
            'username',
            'dob',
            'join_date',
            'admin',
            'firstname',
            'surname',
            'email',
        ];

    $adminlevel = $this->getAuthenticatedUser()->get()->admin;

    $sortValues = array_reduce($sortableAttributes,

        function ($reduced, $attribute)
        {
            $sortValue = $this->getSortAttribute($attribute, $this->getSort());
            $reduced[$attribute] = $sortValue['order'];
            return $reduced;

        },[]);

        return new ViewModel([
            'users' => $this->table->fetchAll([
                "sort" => self::getZfOrder($this->getSort())
            ]),
                "sort" => $this->params()->fromQuery('sort'),
                "sortValues" => $sortValues,
                "user" => $this->getAuthenticatedUser()->get()
                
        ]);
    }

    public function registerAction()
    {
        $form = new UsersForm([
            "includepassword" => true
        ]);

        $form->setInputFilter($this->inputFilter);

        $form->get('submit')->setValue('Add');
        $form->setAttribute('action', $this->url()->fromRoute('users', [ 'action' => 'register' ]));

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $user= new Users();
        $form->setData(array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        ));

        $request->getFiles();

        /* if ($request->getFiles()){
            $file = current($request->getFiles());
            $uploadSuccess = move_uploaded_file($file['tmp_name'], 'public/uploads/' . $file['name']);
            if (!$uploadSuccess) {
                echo 'Could not upload';
            }
        } */

        if (! $form->isValid()) {
            //echo '<pre>';
           // print_r($form->getMessages());

            //$this->flashMessenger()->addMessage($form->getMessages());
            //echo '</pre>';
            return ['form' => $form];
        }

        /* echo '<pre>';
        print_r($form->getData());
        echo '</pre>';
        exit; */

        $user->exchangeArray($form->getData());

        $bcrypt = new Bcrypt();
        $date = date('Y-m-d H:i:s');

        $user->join_date = $date;
        $user->password = $bcrypt->create($user->password);
        $user->avatar = basename($form->getData()['avatar']['tmp_name']);
       // $user->admin = 0;

        $this->table->saveUsers($user);
        return $this->redirect()->toRoute('users');
    }

    public function editAction()
    {
        if (!$user = $this->getAuthenticatedUser()->get()) 
        {
            $this->flashMessenger()->addMessage('You must create an account to have access to this feature!');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        if ($user->admin == 0)
        {
            $this->flashMessenger()->addMessage('Check your privilege!');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('users', ['action' => 'add']);
        }

        $currentuser = $this->getAuthenticatedUser()->get()->id;
        $adminlevel = $this->getAuthenticatedUser()->get()->admin;

        if($id == $currentuser){

            $this->flashMessenger()->addMessage('You cannot modify yourself, idiot!');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        try {
            $users = $this->table->getUsers($id);
            $password = $users->password;
            $join_date = $users->join_date;
            //$avatar = $users->avatar;
            $users->validatePassword = false;
    
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('users', ['action' => 'index']);
        }

        if($adminlevel == 1 && ($users->admin == 1 || $users->admin == 2)){

            $this->flashMessenger()->addMessage("This isn't a dictatorship, you cannot just modify your peers, christ man, what's your problem?");
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        $form = new UsersForm([
            'includeAdmin' => true
        ]);

        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            $form->setData($users->getArrayCopy());
            return $viewData;
        }
        
        $form->setInputFilter($this->inputFilter);
        $form->setData($request->getPost());
        

        //NEW CODE
        $form->setData(array_merge_recursive(
            $request->getPost()->toArray(),
            $request->getFiles()->toArray()
        ));





        if (! $request->isPost()) {
            return $viewData;
        }

        $form->isValid();

        $users->exchangeArray($form->getData());
        $users->password = $password;
        $users->join_date = $join_date;
        $users->avatar = basename($form->getData()['avatar']['tmp_name']);
        //$users->avatar = $avatar;

        $this->table->saveUsers($users);
        return $this->redirect()->toRoute('users', ['action' => 'index']);
    }

    public function deleteAction()
    {
        if (!$user = $this->getAuthenticatedUser()->get()) 
        {
            $this->flashMessenger()->addMessage('You must create an account to have access to this feature!');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        if ($user->admin == 0)
        {
            $this->flashMessenger()->addMessage('Check your privilege!');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (!$id) {
            return $this->redirect()->toRoute('users');
        }

        $currentuser = $this->getAuthenticatedUser()->get()->id;

        $adminlevel = $this->getAuthenticatedUser()->get()->admin;

        if($id == $currentuser){

            $this->flashMessenger()->addMessage('You cannot delete yourself, idiot!');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        $users = $this->table->getUsers($id);
       
        if($adminlevel == 1 && ($users->admin == 1 || $users->admin == 2)){

            $this->flashMessenger()->addMessage("This isn't a dictatorship, you cannot just delete your peers, christ man, what's your problem?");
            return $this->redirect()
            ->toRoute('users');
            exit;
            
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteUsers($id);
            }

            return $this->redirect()->toRoute('users');
        }

        $user = $this->table->fetchAll([ 'id' => $id ])
            ->current();

        if (!$user) {
            $this->flashMessenger()->addMessage('ID does not exist');
            return $this->redirect()
            ->toRoute('users');
            exit;
        }

        return [
            'id'    => $id,
            'users' => $user,
        ];
    }

    protected function getSort () {

        $query = $this->params()
        ->fromQuery('sort');
    
        if (!$query) {
        return [];
        }
    
        $query = explode(',', $query);
    
        return array_reduce($query, function ($reduced, $entry) {
        $parts = explode('-', $entry);
        $attribute = end($parts);
    
        $order = substr($entry, 0, 1) === '-'
            ? 'DESC'
            : 'ASC';
    
        $reduced[] = [
            'attribute' => $attribute,
            'order' => $order
        ];
        return $reduced;
        }, []);
        }
        
        protected static function getZfOrder ($sort) {
            return array_map(function ($entry) {
            return implode(' ', $entry);
            }, $sort);
        }

        protected function getSortAttribute ($name, $sort) {
            $sortValue = array_filter($sort, function ($sortAttribute) use ($name) {
            return $sortAttribute['attribute'] === $name;
            });
        
            return current($sortValue);
    }
}

?>