<?php
/**
 * Bootstrap class to set up application
 *
 * @author ashlewis (@shleylewis)
 * @version 0.1.0
 * @license WTFPL
 */

class Bootstrap
{
	//------------------------------------------
	// Private properties
	//------------------------------------------
    private $controllerName,
    		$controller,
    		$modelName,
    		$model,
    		$action,
     		$args,
     		$pathArray;

    //------------------------------------------
	// Public functions
	//------------------------------------------

	/**
	 * Initialize app
	 */
	public function __construct(){
	    // try {
	    //     $this->dbh = new PDO(Config::get('dbDSN'), Config::get('dbUsername'), Config::get('dbPassword'));
	    // } catch (PDOException $e) {
	    //     echo 'Connection failed: '. $e->getMessage();
	    // }
	}

	/**
	 * Route application based on url
	 *
	 * @param string $defaultController
	 * @param string $defaultAction
	 */
	public function route($defaultController, $defaultAction){

		$this->setPathArray();

		$this->setControllerName();

		$this->setModelName();

		$this->setAction();

		$this->setArgs();

		$this->setController();
		
		$this->dispatch();
	}

	//------------------------------------------
	// Private functions
	//------------------------------------------

	/**
	 * Create array from url components
	 */
	private function setPathArray(){
		$path = isset($_GET['q']) ? $_GET['q'] : null;
		$this->pathArray = isset($path) ? explode('/', rtrim($path, '/')) : array();
	}

	/**
	 * Determine controller name from url components
	 */
	private function setControllerName(){

		$controllerName = ($controllerName = array_shift($this->pathArray)) ? $controllerName : $defaultController;

		$this->controllerName = ucfirst($controllerName) .'Controller';
	}

	/**
	 * Determine model name from url components
	 */
	private function setModelName(){
		$this->modelName = str_replace('Controller', '', $this->controllerName);

	}

	/**
	 * Determine action from url components
	 */
	private function setAction(){
		$this->action = ($this->action = array_shift($this->pathArray)) ? $this->action : $defaultAction;
	}	

	/**
	 * Determine args from url components
	 */
	private function setArgs(){
		$this->args = $this->pathArray;
	}

	/**
	 * Set controller and action for page not found situations
	 */
	private function setRoutePageNotFound(){
		$this->controller = new ErrorController(new View());
		$this->action = 'pageNotFound';	
	}

	/**
	 * Dynamically create required controller(and model)
	 */
	private function setController(){

		try {
			$this->controller = ModelControllerFactory::create(
									$this->controllerName,
									new View(),
									ModelFactory::create($this->modelName)
								);

		} catch (Exception $e) {
			// required controller class does not exist (NOTE: php 5.3.0+ only)
			$this->setRoutePageNotFound();
		}

		if (!method_exists($this->controller, $this->action)) {
			// required action method does not exist within controller class
			$this->setRoutePageNotFound();		
		}
	}

	/**
	 * Call required action method (with required arguments) in required controller class
	 */
	private function dispatch(){
		$this->controller->{$this->action}($this->args);
	}
}