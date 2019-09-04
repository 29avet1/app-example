<?php

namespace App\Repositories;

use Exception;
use Illuminate\Container\Container as App;
use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    /**
     * @var
     */
    public $model;
    /**
     * @var
     */
    public $app;

    /**
     * @param App $app
     * @throws Exception
     */
    public function __construct(App $app)
    {
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * @return Model
     * @throws Exception
     */
    public function makeModel(): Model
    {
        return $this->setModel($this->model());
    }

    /**
     * Set Eloquent Model to instantiate
     *
     * @param $eloquentModel
     * @return Model
     * @throws Exception
     */
    public function setModel($eloquentModel): Model
    {
        $this->newModel = $this->app->make($eloquentModel);

        if (!$this->newModel instanceof Model) {
            throw new Exception("Class {$this->newModel} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $this->newModel;
    }

    /**
     * @param null   $items
     * @param array  $itemsToPluck
     * @param string $glue
     * @return array
     */
    public function pluckWith($items = null, array $itemsToPluck, string $glue): array
    {
        $items = $items ?: $this->model->all();
        $itemsPluck = [];

        foreach ($items as $item) {
            $itemsArray = [];
            foreach ($itemsToPluck as $pluck) {
                $itemsArray[] = $item->{$pluck};
            }

            $itemsPluck[$item->id] = implode($glue, $itemsArray);
        }

        return $itemsPluck;
    }

    /**
     * @param Exception $exception
     * @throws Exception
     * @throws Exception
     */
    protected function handleError(Exception $exception)
    {
        throw $exception;
//        if ($exception->getCode() != 500) {
//            throw $exception;
//        }
//
//        abort(500, 'There was an error while processing request. Please recheck request data and try again.');
    }
}