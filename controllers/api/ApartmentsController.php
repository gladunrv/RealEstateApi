<?php

namespace app\controllers\api;

use app\models\ApartmentSearch;
use Yii;

class ApartmentsController extends ApiController
{

    public $modelClass = 'app\models\Apartment';

    public function actions()
    {
        $actions = parent::actions();
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];
        //unset($actions['view']);
        //unset($actions['index']);
        //unset($actions['update']);
        //unset($actions['create']);
        unset($actions['delete']);
        return $actions;
    }

    public function prepareDataProvider()
    {
        $searchModel = new ApartmentSearch;
        return $searchModel->search(Yii::$app->request->queryParams);
    }

}
