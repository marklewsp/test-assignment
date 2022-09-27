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
        $tempStart = $start;
        switch ($period) {
            case 'year':
                $tempEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($tempStart)) . " +1 year -1 day"));
                do{
                    if ($tempEnd >= $end) {
                        $tempEnd = $end;
                    }
                    elseif ($tempStart < $end) {
                        $tempEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($tempStart)) . " +1 year -1 day"));
                    } else {
                        $tempEnd = $end;
                    }
                    $result[] = Messages::getMessagesByTime($tempStart, $tempEnd, $period);
                    $tempStart = date("Y-m-d", strtotime(date("Y-m-d", strtotime($tempEnd)) . " +1 day"));

                }while($tempStart <= $end);
                break;
            case 'month':
                $tempEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($tempStart)) . " +1 month -1 day"));
                do{
                    if ($tempStart <= $end) {
                        $tempEnd = date("Y-m-d", strtotime(date("Y-m-d", strtotime($tempStart)) . " +1 month -1 day"));
                    }
                    if ($tempEnd >= $end) {
                        $tempEnd = $end;
                    }

                    $result[] = Messages::getMessagesByTime($tempStart, $tempEnd, $period);
                    $tempStart = date("Y-m-d", strtotime(date("Y-m-d", strtotime($tempEnd)) . " +1 day"));

                }while($tempStart <= $end);
                break;
            case 'day':
                $result = Messages::getMessagesByTime($start, $end, $period);
                break;
            default:
                $result = [];
        }
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
