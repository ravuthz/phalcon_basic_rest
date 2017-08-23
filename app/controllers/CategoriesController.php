<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class CategoriesController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for categories
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Categories', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $categories = Categories::find($parameters);
        if (count($categories) == 0) {
            $this->flash->notice("The search did not find any categories");

            $this->dispatcher->forward([
                "controller" => "categories",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $categories,
            'limit'=> 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Displays the creation form
     */
    public function newAction()
    {

    }

    /**
     * Edits a categorie
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $categorie = Categories::findFirstByid($id);
            if (!$categorie) {
                $this->flash->error("categorie was not found");

                $this->dispatcher->forward([
                    'controller' => "categories",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $categorie->id;

            $this->tag->setDefault("id", $categorie->id);
            $this->tag->setDefault("slug", $categorie->slug);
            $this->tag->setDefault("name", $categorie->name);
            $this->tag->setDefault("note", $categorie->note);
            $this->tag->setDefault("created_at", $categorie->created_at);
            $this->tag->setDefault("updated_at", $categorie->updated_at);
            
        }
    }

    /**
     * Creates a new categorie
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'index'
            ]);

            return;
        }

        $categorie = new Categories();
        $categorie->slug = $this->request->getPost("slug");
        $categorie->name = $this->request->getPost("name");
        $categorie->note = $this->request->getPost("note");
        $categorie->created_at = $this->request->getPost("created_at");
        $categorie->updated_at = $this->request->getPost("updated_at");
        

        if (!$categorie->save()) {
            foreach ($categorie->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("categorie was created successfully");

        $this->dispatcher->forward([
            'controller' => "categories",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a categorie edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $categorie = Categories::findFirstByid($id);

        if (!$categorie) {
            $this->flash->error("categorie does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'index'
            ]);

            return;
        }

        $categorie->slug = $this->request->getPost("slug");
        $categorie->name = $this->request->getPost("name");
        $categorie->note = $this->request->getPost("note");
        $categorie->created_at = $this->request->getPost("created_at");
        $categorie->updated_at = $this->request->getPost("updated_at");
        

        if (!$categorie->save()) {

            foreach ($categorie->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'edit',
                'params' => [$categorie->id]
            ]);

            return;
        }

        $this->flash->success("categorie was updated successfully");

        $this->dispatcher->forward([
            'controller' => "categories",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a categorie
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $categorie = Categories::findFirstByid($id);
        if (!$categorie) {
            $this->flash->error("categorie was not found");

            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'index'
            ]);

            return;
        }

        if (!$categorie->delete()) {

            foreach ($categorie->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "categories",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("categorie was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "categories",
            'action' => "index"
        ]);
    }

}
