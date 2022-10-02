<?php

namespace app\controllers;

use app\models\Messages;
use app\models\User;
use yii\rest\ActiveController;
use Yii;
use yii\web\Response;

class MessagesController extends ActiveController
{
    public $modelClass = 'app\models\Messages';

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * @param $start
     * @param $end
     * @param string $period
     * @return array
     * @throws \yii\db\Exception
     */
    public function deliveredByTime($start, $end, $period = 'year')
    {
        $result = Messages::getMessagesByTime($start, $end, $period);
        return $result;
    }

    /**
     * @throws \yii\db\Exception
     */
    public function actionTotal()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();
        $result = $this->deliveredByTime($params["period_start"], $params["period_end"], $params["period_group_unit"]);
        if (empty($result)) {
            return json_encode(array( 'status' => false, 'data' => ""));
        } else {
            return json_encode(array( 'status' => false, 'data' => $result));
        }
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionUserActivity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->get();
        $result = User::getUserActivity($params["period_start"], $params["period_end"], $params["limit"], $params["dir"]);
        if (empty($result)) {
            return json_encode(array( 'status' => false, 'data' => ""));
        } else {
            return json_encode(array( 'status' => false, 'data' => $result));
        }
    }
}
