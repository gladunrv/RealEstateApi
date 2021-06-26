<?php

namespace app\controllers\api;

use yii\rest\ActiveController;
use \app\models\ActionsLog;
use \Yii;

abstract class ApiController extends ActiveController
{

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $headers = Yii::$app->request->getHeaders();
        $result = parent::afterAction($action, $result);
        $params = [
            'client' => $headers->get('client', 'Api'),
            'client_version' => (int) $headers->get('client-version', 1),
            'app_version' => (int) $headers->get('app-version', 1),
        ];
        ActionsLog::addToLog($action, $result, $params);
        return $result;
    }

}
