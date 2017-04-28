<?php
 
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;


class CarsController extends ControllerBase
{
    /**
     * Index action
     */
    public function indexAction()
    {
        $this->persistent->parameters = null;
    }

    /**
     * Searches for cars
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Cars', $_POST);
            $this->persistent->parameters = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = $this->persistent->parameters;
        if (!is_array($parameters)) {
            $parameters = [];
        }
        $parameters["order"] = "id";

        $cars = Cars::find($parameters);
        if (count($cars) == 0) {
            $this->flash->notice("The search did not find any cars");

            $this->dispatcher->forward([
                "controller" => "cars",
                "action" => "index"
            ]);

            return;
        }

        $paginator = new Paginator([
            'data' => $cars,
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
     * Edits a car
     *
     * @param string $id
     */
    public function editAction($id)
    {
        if (!$this->request->isPost()) {

            $car = Cars::findFirstByid($id);
            if (!$car) {
                $this->flash->error("car was not found");

                $this->dispatcher->forward([
                    'controller' => "cars",
                    'action' => 'index'
                ]);

                return;
            }

            $this->view->id = $car->id;

            $this->tag->setDefault("id", $car->id);
            $this->tag->setDefault("name", $car->name);
            $this->tag->setDefault("type", $car->type);
            
        }
    }

    /**
     * Creates a new car
     */
    public function createAction()
    {
        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'index'
            ]);

            return;
        }

        $car = new Cars();
        $car->name = $this->request->getPost("name");
        $car->type = $this->request->getPost("type");
        

        if (!$car->save()) {
            foreach ($car->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'new'
            ]);

            return;
        }

        $this->flash->success("car was created successfully");

        $this->dispatcher->forward([
            'controller' => "cars",
            'action' => 'index'
        ]);
    }

    /**
     * Saves a car edited
     *
     */
    public function saveAction()
    {

        if (!$this->request->isPost()) {
            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'index'
            ]);

            return;
        }

        $id = $this->request->getPost("id");
        $car = Cars::findFirstByid($id);

        if (!$car) {
            $this->flash->error("car does not exist " . $id);

            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'index'
            ]);

            return;
        }

        $car->name = $this->request->getPost("name");
        $car->type = $this->request->getPost("type");
        

        if (!$car->save()) {

            foreach ($car->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'edit',
                'params' => [$car->id]
            ]);

            return;
        }

        $this->flash->success("car was updated successfully");

        $this->dispatcher->forward([
            'controller' => "cars",
            'action' => 'index'
        ]);
    }

    /**
     * Deletes a car
     *
     * @param string $id
     */
    public function deleteAction($id)
    {
        $car = Cars::findFirstByid($id);
        if (!$car) {
            $this->flash->error("car was not found");

            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'index'
            ]);

            return;
        }

        if (!$car->delete()) {

            foreach ($car->getMessages() as $message) {
                $this->flash->error($message);
            }

            $this->dispatcher->forward([
                'controller' => "cars",
                'action' => 'search'
            ]);

            return;
        }

        $this->flash->success("car was deleted successfully");

        $this->dispatcher->forward([
            'controller' => "cars",
            'action' => "index"
        ]);
    }

}
