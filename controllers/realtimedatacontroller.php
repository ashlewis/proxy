<?php

/**
 * Controller class to handle all item actions
 *
 * @author ashleyl
 *
 */
class RealtimedataController extends ModelController
{

    /**
     * Default item action
     *
     * @param array $args
     */
    public function index(array $args) {
        $this->read($args);
    }


    /**
     * View item
     *
     * @param array $args
     */
    public function read(array $args) {

        try {
            $this->model->init($args[0]);
            $jsonData = $this->model->getJsonData();
            $this->view->setVariables('json', $jsonData);

        } catch (Exception $e) {
            $this->view->setVariables('json', false);
        }        

        $this->view->render($this->templateDir . Config::get('ds') . 'read', 0, 0);
    }

    public function create(array $args){}

    public function update(array $args){}

    public function delete(array $args){}


}