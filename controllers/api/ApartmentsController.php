<?php

namespace app\controllers\api;

class ApartmentsController extends ApiController
{

    public $modelClass = 'app\models\Apartment';

    public function actions()
    {
        $actions = parent::actions();
        //unset($actions['view']);
        //unset($actions['index']);
        //unset($actions['update']);
        //unset($actions['create']);
        unset($actions['delete']);
        return $actions;
    }

}
