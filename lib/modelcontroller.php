<?php
/**
 * Controller class for domain model classes
 *
 * require model and mapper classes
 *
 * @author ashleyl
 *
 */
abstract class modelController extends Controller{

	// domain model object
	protected $model;

	/**
	 * Extend parent contructor to set model
	 *
	 * @param View $view
	 * @param Model $model
	 */
    public function __construct(View $view, Model $model){
        parent::__construct($view);

        $this->model = $model;
    }

    public abstract function create(array $args);

	public abstract function read(array $args);

	public abstract function update(array $args);

	public abstract function delete(array $args);


}