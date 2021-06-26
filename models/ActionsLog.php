<?php

namespace app\models;

use Yii;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%actions_log}}".
 *
 * @property int $id
 * @property int $uid
 * @property string $action
 * @property int $time
 * @property string $client
 * @property int $app_version
 * @property int $client_version
 * @property string $user_ip
 * @property double $execution_time
 * @property double $memory_usage
 * @property string $params
 * @property string $answer
 */
class ActionsLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%actions_log}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['uid', 'action', 'execution_time', 'memory_usage'], 'required'],
            [['uid', 'time', 'app_version', 'client_version', 'db_count'], 'integer'],
            [['execution_time', 'memory_usage', 'db_time'], 'number'],
            [['action', 'client', 'params', 'answer', 'user_ip'], 'string'],
            ['uid', 'default', 'value' => 0],
            ['client', 'default', 'value' => ''],
            ['time', 'default', 'value' => time()],
            ['app_version', 'default', 'value' => 0],
            ['client_version', 'default', 'value' => 0],
            ['params', 'default', 'value' => ''],
            ['answer', 'default', 'value' => ''],
            ['user_ip', 'default', 'value' => ''],
            ['db_count', 'default', 'value' => 0],
            ['db_time', 'default', 'value' => 0],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'uid' => Yii::t('app', 'Uid'),
            'action' => Yii::t('app', 'Action'),
            'time' => Yii::t('app', 'Time'),
            'client' => Yii::t('app', 'Client'),
            'app_version' => Yii::t('app', 'App Version'),
            'client_version' => Yii::t('app', 'Client Version'),
            'user_ip' => Yii::t('app', 'User Ip'),
            'execution_time' => Yii::t('app', 'Execution Time'),
            'memory_usage' => Yii::t('app', 'Memory Usage'),
            'params' => Yii::t('app', 'Params'),
            'answer' => Yii::t('app', 'Answer'),
            'db_count' => Yii::t('app', 'Requests to DB'),
            'db_time' => Yii::t('app', 'DB Query Time'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function addToLog($action, $result, $params = [])
    {
        $action = !empty($action) ? $action->controller->module->controller->module->requestedRoute : 'index';
        $userIp = Yii::$app->request->userIP;
        $uid = !empty(Yii::$app->user->identity->id) ? Yii::$app->user->identity->id : 0;

        // Если он по какой-то причине отсутствует, то помечаем тест пропущенным
        $logger = Yii::getLogger(); // Получаем логгер

        if (
            self::isLogUsers($uid) ||
            self::isLogAction($action) ||
            self::isLogIp($userIp)
        ) {
            $actionLog = new self;
            $actionLog->uid = $uid;
            $actionLog->action = $action;
            $actionLog->client = !empty($params['client']) ? $params['client'] : $actionLog->client;
            $actionLog->app_version = !empty($params['app_version']) ? $params['app_version'] : $actionLog->app_version;
            $actionLog->client_version = !empty($params['client_version']) ? $params['client_version'] : $actionLog->client_version;
            $actionLog->user_ip = $userIp;
            $actionLog->execution_time = Yii::getLogger()->getElapsedTime() * 1000;
            $actionLog->memory_usage = round(memory_get_peak_usage() / (1024 * 1024), 2);
            $actionLog->db_count = $logger ? $logger->getDbProfiling()[0] : 0;
            $actionLog->db_time = $logger ? $logger->getDbProfiling()[1] * 1000 : 0;

            if (
                self::isLogAnswersUsers($uid) ||
                self::isLogAnswersAction($action) ||
                self::isLogAnswersIp($userIp)
            ) {
                $request = ArrayHelper::merge(Yii::$app->request->get(), Yii::$app->request->post());
                $actionLog->params = substr(Json::encode($request), 0, 2048);
                if (is_array($result)) {
                    $actionLog->answer = substr(Json::encode($result), 0, 2048);
                }
            }
            if (!$actionLog->save()) {
                throw new Exception("Error in addToLog process", $actionLog->errors);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function isLogUsers($uid)
    {
        $logUsers = ArrayHelper::merge(Yii::$app->params['actionsLogUsers'], Yii::$app->params['actionsLogAnswersUsers']);
        return $uid ? in_array($uid, $logUsers) || $uid % 100 == intval(time() / 86400) % 100 : !rand(0, 100);
    }

    /**
     * {@inheritdoc}
     */
    public static function isLogAnswersUsers($uid)
    {
        return in_array($uid, Yii::$app->params['actionsLogUsers']);
    }

    /**
     * {@inheritdoc}
     */
    public static function isLogAction($action)
    {
        return in_array($action, Yii::$app->params['actionsLogActions']);
    }

    /**
     * {@inheritdoc}
     */
    public static function isLogAnswersAction($action)
    {
        $logActions = ArrayHelper::merge(Yii::$app->params['actionsLogActions'], Yii::$app->params['actionsLogAnswersActions']);
        return in_array($action, Yii::$app->params['actionsLogActions']);
    }

    /**
     * {@inheritdoc}
     */
    public static function isLogIp($userIp)
    {
        $logIps = Yii::$app->params['actionsLogIp'];
        return in_array($userIp, $logIps) || in_array('*', $logIps);
    }

    /**
     * {@inheritdoc}
     */
    public static function isLogAnswersIp($userIp)
    {
        $logIp = ArrayHelper::merge(Yii::$app->params['actionsLogIp'], Yii::$app->params['actionsLogAnswersIp']);
        return in_array($userIp, Yii::$app->params['actionsLogIp']);
    }

    /**
     * {@inheritdoc}
     */
    public static function getStatistics()
    {
        return (new Query())
            ->select([
                'client',
                'action',
                'actions_count' => new Expression('COUNT(*)'),
                'execution_time' => new Expression('AVG(execution_time)'),
                'memory_usage' => new Expression('AVG(memory_usage)'),
                'query_time' => new Expression('AVG(db_time)'),
                'requests_count' => new Expression('AVG(db_count)'),
                'users' => new Expression('COUNT(DISTINCT uid)'),
            ])
            ->from('actions_log')
            ->andWhere('time > :time', [':time' => time() - 24 * 60 * 60])
            ->groupBy(['client', 'action'])
            ->orderBy('client DESC, actions_count DESC')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function getExecutionTimeForHours()
    {
        return (new Query())
            ->select([
                'client',
                'action',
                'execution_time' => new Expression('AVG(execution_time)'),
                'hour' => new Expression('(`actions_log`.`time` DIV 3600) * 3600'),
            ])
            ->from('actions_log')
            ->andWhere('time > :time', [':time' => time() - 48 * 60 * 60])
            ->groupBy(['client', 'action', 'hour'])
            ->orderBy('hour DESC')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function getExecutionTimeClientForHours()
    {
        return (new Query())
            ->select([
                'client',
                'execution_time' => new Expression('AVG(execution_time)'),
                'hour' => new Expression('(`actions_log`.`time` DIV 3600) * 3600'),
            ])
            ->from('actions_log')
            ->andWhere('time > :time', [':time' => time() - 48 * 60 * 60])
            ->groupBy(['client', 'hour'])
            ->orderBy('hour DESC')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function getActionCountForHours()
    {
        return (new Query())
            ->select([
                'client',
                'action',
                'actions_count' => new Expression('COUNT(*)'),
                'hour' => new Expression('(`actions_log`.`time` DIV 3600) * 3600'),
            ])
            ->from('actions_log')
            ->andWhere('time > :time', [':time' => time() - 48 * 60 * 60])
            ->groupBy(['client', 'action', 'hour'])
            ->orderBy('hour DESC')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function getActionCountClientForHours()
    {
        return (new Query())
            ->select([
                'client',
                'action',
                'actions_count' => new Expression('COUNT(*)'),
                'hour' => new Expression('(`actions_log`.`time` DIV 3600) * 3600'),
            ])
            ->from('actions_log')
            ->andWhere('time > :time', [':time' => time() - 48 * 60 * 60])
            ->groupBy(['client', 'hour'])
            ->orderBy('hour DESC')
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public static function getDataForHours()
    {
        $_data = self::getExecutionTimeForHours();
        $data = [];
        foreach ($_data as $k => $v) {
            $key = $v['client'] . '-' . $v['action'];
            $data[$key][$v['hour']] = round($v['execution_time'], 2);
        }
        foreach ($data as $k => &$v) {
            for ($i = 0; $i < 48; $i++) {
                $hour = 3600 * intval(time() / 3600) - $i * 3600;
                if (empty($v[$hour])) {
                    $v[$hour] = 0;
                }
            }
            ksort($v);
        }
        $labels = [];
        for ($i = 0; $i < 48; $i++) {
            $hour = 3600 * intval(time() / 3600) - $i * 3600;
            $labels[] = date("'H:i d.m'", $hour);
        }
        $labels = array_reverse($labels, true);
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * {@inheritdoc}
     */
    public static function getClientDataForHours()
    {
        $_data = self::getExecutionTimeClientForHours();
        $data = [];
        foreach ($_data as $k => $v) {
            $key = $v['client'];
            $data[$key][$v['hour']] = round($v['execution_time'], 2);
        }
        foreach ($data as $k => &$v) {
            for ($i = 0; $i < 48; $i++) {
                $hour = 3600 * intval(time() / 3600) - $i * 3600;
                if (empty($v[$hour])) {
                    $v[$hour] = 0;
                }
            }
            ksort($v);
        }
        $labels = [];
        for ($i = 0; $i < 48; $i++) {
            $hour = 3600 * intval(time() / 3600) - $i * 3600;
            $labels[] = date("'H:i d.m'", $hour);
        }
        $labels = array_reverse($labels, true);
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * {@inheritdoc}
     */
    public static function getClientActionCountForHours()
    {
        $_data = self::getActionCountClientForHours();
        $data = [];
        foreach ($_data as $k => $v) {
            $key = $v['client'];
            $data[$key][$v['hour']] = $v['actions_count'];
        }
        foreach ($data as $k => &$v) {
            for ($i = 0; $i < 48; $i++) {
                $hour = 3600 * intval(time() / 3600) - $i * 3600;
                if (empty($v[$hour])) {
                    $v[$hour] = 0;
                }
            }
            ksort($v);
        }
        $labels = [];
        for ($i = 0; $i < 48; $i++) {
            $hour = 3600 * intval(time() / 3600) - $i * 3600;
            $labels[] = date("'H:i d.m'", $hour);
        }
        $labels = array_reverse($labels, true);
        return ['labels' => $labels, 'data' => $data];
    }

    /**
     * {@inheritdoc}
     */
    public static function getActionCountDataForHours()
    {
        $_data = self::getActionCountForHours();
        $data = [];
        foreach ($_data as $k => $v) {
            $key = $v['client'] . '-' . $v['action'];
            $data[$key][$v['hour']] = $v['actions_count'];
        }
        foreach ($data as $k => &$v) {
            for ($i = 0; $i < 48; $i++) {
                $hour = 3600 * intval(time() / 3600) - $i * 3600;
                if (empty($v[$hour])) {
                    $v[$hour] = 0;
                }
            }
            ksort($v);
        }
        $labels = [];
        for ($i = 0; $i < 48; $i++) {
            $hour = 3600 * intval(time() / 3600) - $i * 3600;
            $labels[] = date("'H:i d.m'", $hour);
        }
        $labels = array_reverse($labels, true);
        return ['labels' => $labels, 'data' => $data];
    }

}
