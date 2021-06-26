<?php

use yii\db\Schema;
use yii\db\Migration;

class m210626_175936_apartment extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable(
            '{{%apartment}}',
            [
                'id'=> $this->primaryKey(11)->comment('ID'),
                'city'=> $this->string(128)->notNull()->comment('Город'),
                'district'=> $this->string(128)->notNull()->comment('Район'),
                'address'=> $this->string(128)->notNull()->comment('Адрес'),
                'residential_complex'=> $this->string(128)->notNull()->comment('Жилой комплекс'),
                'block'=> $this->string(128)->null()->defaultValue(null)->comment('Корпус'),
                'total_floors'=> $this->integer(11)->notNull()->comment('Всего этажей'),
                'floor'=> $this->integer(11)->notNull()->comment('Этаж'),
                'rooms'=> $this->integer(11)->notNull()->comment('Количество комнат'),
                'area'=> $this->integer(11)->notNull()->comment('Площадь квартиры'),
                'rent_price'=> $this->integer(11)->notNull()->comment('Стоимость аренды'),
            ],$tableOptions
        );

    }

    public function safeDown()
    {
        $this->dropTable('{{%apartment}}');
    }
}
