<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%apartment}}".
 *
 * @property int $id ID
 * @property string $city Город
 * @property string $district Район
 * @property string $address Адрес
 * @property string $residential_complex Жилой комплекс
 * @property string $block Корпус
 * @property int $total floors Всего этажей
 * @property int $floor Этаж
 * @property int $rooms Количество комнат
 * @property int $area Площадь квартиры
 * @property int $rent_price Стоимость аренды
 */
class Apartment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%apartment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city', 'district', 'address', 'residential_complex', 'total_floors', 'floor', 'rooms', 'area', 'rent_price'], 'required'],
            [['total_floors', 'floor', 'rooms', 'area', 'rent_price'], 'integer'],
            [['city', 'district', 'address', 'residential_complex', 'block'], 'string', 'max' => 128],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city' => 'Город',
            'district' => 'Район',
            'address' => 'Адрес',
            'residential_complex' => 'Жилой комплекс',
            'block' => 'Корпус',
            'total_floors' => 'Всего этажей',
            'floor' => 'Этаж',
            'rooms' => 'Количество комнат',
            'area' => 'Площадь квартиры',
            'rent_price' => 'Стоимость аренды',
        ];
    }
}
