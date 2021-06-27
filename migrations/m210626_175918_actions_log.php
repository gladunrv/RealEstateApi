<?php

use yii\db\Migration;

class m210626_175918_actions_log extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable(
            '{{%actions_log}}',
            [
                'id' => $this->primaryKey(11),
                'uid' => $this->integer(11)->null()->defaultValue(null),
                'action' => $this->string(128)->null()->defaultValue(null),
                'time' => $this->integer(11)->null()->defaultValue(null),
                'client' => $this->string(128)->null()->defaultValue(null),
                'app_version' => $this->integer(11)->null()->defaultValue(null),
                'client_version' => $this->integer(11)->null()->defaultValue(null),
                'user_ip' => $this->string(64)->null()->defaultValue(null),
                'execution_time' => $this->float()->null()->defaultValue(null),
                'memory_usage' => $this->float()->null()->defaultValue(null),
                'db_count' => $this->integer(11)->notNull(),
                'db_time' => $this->float()->notNull(),
                'params' => $this->string(2048)->null()->defaultValue(null),
                'answer' => $this->string(2048)->null()->defaultValue(null),
            ], $tableOptions
        );
        $this->createIndex('idx-actions_log-action', '{{%actions_log}}', ['action'], false);
        $this->createIndex('idx-actions_log-client', '{{%actions_log}}', ['client'], false);
        $this->createIndex('idx-actions_log-time', '{{%actions_log}}', ['time'], false);
        $this->createIndex('idx-actions_log-uid', '{{%actions_log}}', ['uid'], false);
        $this->createIndex('idx-actions_log-action-client-time', '{{%actions_log}}', ['action', 'client', 'time'], false);

    }

    public function safeDown()
    {
        $this->dropIndex('idx-actions_log-action', '{{%actions_log}}');
        $this->dropIndex('idx-actions_log-client', '{{%actions_log}}');
        $this->dropIndex('idx-actions_log-time', '{{%actions_log}}');
        $this->dropIndex('idx-actions_log-uid', '{{%actions_log}}');
        $this->dropIndex('idx-actions_log-action-client-time', '{{%actions_log}}');
        $this->dropTable('{{%actions_log}}');
    }
}
