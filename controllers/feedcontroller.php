<?php

/**
 * Controller class to handle all feed actions
 *
 * @author ashleyl
 *
 */
class NdbcFeedController extends Controller {

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
    	
 		$itemArray = $this->mapper->getObjects($args);

        $this->view->setVariables('itemArray', $itemArray);

        $this->view->render($this->templateDir . Config::get('ds') . 'read');
    }



}
