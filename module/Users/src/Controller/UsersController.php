<?php

    namespace Users\Controller;

    use Zend\Crypt\Password\Bcrypt;
    use Users\Form\UsersForm;
    use Users\Form\LoginForm;
    use Users\Model\Users;
    use Users\Model\UsersTable;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    

    class UsersController extends AbstractActionController
    {
        private $table;

        public function __construct(UsersTable $table)
        {
            $this->table = $table;
        }

        public function indexAction()
        {

            if (!$user = $this->getAuthenticatedUser()->get()) 
            {
                $this->flashMessenger()->addMessage('You must create an account to have access to this feature!');
                return $this->redirect()
                ->toRoute('game');
                exit;
            }

            $sortableAttributes = [
                'username',
                'dob',
                'admin',
                'firstname',
                'surname',
                'email'
            ];

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

            $form->get('submit')->setValue('Add');
            $form->setAttribute('action', $this->url()->fromRoute('users', [ 'action' => 'register' ]));
    
            $request = $this->getRequest();
    
            if (! $request->isPost()) {
                return ['form' => $form];
            }
    
            $user= new Users();
            $form->setInputFilter($user->getInputFilter());
            $form->setData($request->getPost());
    
            if (! $form->isValid()) {
                return ['form' => $form];
            }
    
            $user->exchangeArray($form->getData());

            $bcrypt = new Bcrypt();
            $user->password = $bcrypt->create($user->password);

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

            try {
                $users = $this->table->getUsers($id);
                $users->validatePassword = false;
        
            } catch (\Exception $e) {
                return $this->redirect()->toRoute('users', ['action' => 'index']);
            }

            $form = new UsersForm();
            $form->bind($users);
            $form->get('submit')->setAttribute('value', 'Edit');

            $request = $this->getRequest();
            $viewData = ['id' => $id, 'form' => $form];

            if (! $request->isPost()) {
                return $viewData;
            }

            $form->setData($request->getPost());

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

            $request = $this->getRequest();
            if ($request->isPost()) {
                $del = $request->getPost('del', 'No');

                if ($del == 'Yes') {
                    $id = (int) $request->getPost('id');
                    $this->table->deleteUsers($id);
                }

                return $this->redirect()->toRoute('users');
            }

            return [
                'id'    => $id,
                'users' => $this->table->getUsers($id),
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

        /*
        public function loginAction()
        {
            $loginform = new LoginForm([
                "includepassword" => true
            ]);

            return [
                'loginform' => $loginform
            ];
        }
        
        public function getAuthAdapter(array $params)
        {
                // Leaving this to the developer...
                // Makes the assumption that the constructor takes an array of 
                // parameters which it then uses as credentials to verify identity.
                // Our form, of course, will just pass the parameters 'username'
                // and 'password'.
        }*/
        
   
    }

?>