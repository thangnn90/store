<?php

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class PhongbanController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Phongban', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $phongban = Phongban::find($parameters);
        if (count($phongban) == 0) {
            $this->flash->notice("The search did not find any phongban");

            $this->dispatcher->forward([
                "controller" => "phongban",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $phongban,
            'limit' => 10,
            'page' => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();

    }

    /**
     * Searches for phongban
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Phongban', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $phongban = Phongban::find($parameters);
        if (count($phongban) == 0) {
            $this->flash->notice("The search did not find any phongban");

            $this->dispatcher->forward([
                "controller" => "phongban",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $phongban,
            'limit' => 10,
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
     * Edits a phongban
     *
     * @param string $id
     */
    public function editAction($id)
    {
//        var_dump($id);
        if (!$this->request->isPost()) {

            $phongban = Phongban::findFirstByid($id);
            if (!$phongban) {
                $this->flash->error("phongban was not found");
                $this->dispatcher->forward([
                    'controller' => "phongban",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $phongban->id;

            $this->tag->setDefault("id", $phongban->id);
            $this->tag->setDefault("name", $phongban->name);

        }
    }

    /**
     * Creates a new phongban
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'index'
            ]);

            return;
        }

        $phongban = new Phongban();
        $phongban->name = $this->request->getPost("name");
        if (!$phongban->save()) {
            foreach ($phongban->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("phongban was created successfully");

        $this->dispatcher->forward([
            'controller' => "phongban",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a phongban edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $phongban = Phongban::findFirstByid($id);

        if (!$phongban) {
            $this->flash->error("phongban does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'index'
            ]);

            return;
        }

        $phongban->name = $this->request->getPost("name");


        if (!$phongban->save()) {

            foreach ($phongban->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'edit',
                'params' => [$phongban->id]
            ]);

            return;
        }

        $this->flash->success("phongban was updated successfully");

        $this->dispatcher->forward([
            'controller' => "phongban",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a phongban
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $phongban = Phongban::findFirstByid($id);
        if (!$phongban) {
            $this->flash->error("phongban was not found");

            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'index'
            ]);

            return;
        }

        if (!$phongban->delete()) {

            foreach ($phongban->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "phongban",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("phongban was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "phongban",
            'action' => "index"
        ]);
    }

}
