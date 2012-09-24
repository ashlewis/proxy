<?php
/**
 * ModelController Factory class
 *
 * @author ashleyl
 *
 */
class ModelControllerFactory
{
	public static function create($modelController, View $view, Model $model){

		return new $modelController($view, $model);
	}
}