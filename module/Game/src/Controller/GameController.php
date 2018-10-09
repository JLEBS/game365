<?php

    namespace Game\Controller;

    use Game\Form\GameForm;
    use Game\Model\Game;
    use Game\Model\GameTable;
    use Zend\Mvc\Controller\AbstractActionController;
    use Zend\View\Model\ViewModel;
    

class GameController extends AbstractActionController
{
    private $table;

    public function __construct(GameTable $table)
    {
        $this->table = $table;
    }

    public function indexAction()
    {
        $sortableAttributes = [
            'title',
            'developer',
            'date',
            'rating',
            'online'
            ];
            
        $sortValues = array_reduce($sortableAttributes, 
        
        function ($reduced, $attribute) 
        {
            $sortValue = $this->getSortAttribute($attribute, $this->getSort());
            $reduced[$attribute] = $sortValue['order'];
            return $reduced;

        },[]);

        return new ViewModel([
            'games' => $this->table->fetchAll([
                "sort" => self::getZfOrder($this->getSort())
            ]),
                "sort" => $this->params()->fromQuery('sort'),
                "sortValues" => $sortValues,
                "user" => $this->getAuthenticatedUser()->get()
                // return $this=>$sortValues
                //return current($sortValue);
        ]);
    }

    public function addAction()
    {
        if (!$user = $this->getAuthenticatedUser()->get()) {
            $this->flashMessenger()->addMessage('You must create an account to have access to this feature!');
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        if ($user->admin == 0)
        {
            $this->flashMessenger()->addMessage('Check your privilege!');
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        $form = new GameForm();
        $form->get('submit')->setValue('Add');

        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        $game= new Game();
        $form->setInputFilter($game->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        $game->exchangeArray($form->getData());
        $this->table->saveGame($game);
        return $this->redirect()->toRoute('game');
    
    }

    public function editAction()
    {

        if (!$user = $this->getAuthenticatedUser()->get()) {
            $this->flashMessenger()->addMessage('You must create an account to have access to this feature!');
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        if ($user->admin == 0)
        {
            $this->flashMessenger()->addMessage('Check your privilege!');
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('game', ['action' => 'add']);
        }

        try {
            $game = $this->table->getGame($id);
    
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('game', ['action' => 'index']);
        }

        $form = new GameForm();
        
        $form->bind($game);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($game->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveGame($game);
        return $this->redirect()->toRoute('game', ['action' => 'index']);
    }

    public function deleteAction()
    {

        if (!$user = $this->getAuthenticatedUser()->get()) 
        {
            $this->flashMessenger()->addMessage('You must create an account to have access to this feature!');
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        if ($user->admin == 0)
        {
            $this->flashMessenger()->addMessage('Check your privilege!');
            return $this->redirect()
            ->toRoute('game');
            exit;
        }

        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('game');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteGame($id);
            }

            // Redirect to list of games
            return $this->redirect()->toRoute('game');
        }

        return [
            'id'    => $id,
            'game' => $this->table->getGame($id),
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