<?php

namespace app\modules\storage\models;

use app\modules\storage\modules\product\models\Product;

/**
 * @property integer $id
 * @property string $shop
 * @property string $token
 * @property integer $timestamp
 */
class ShopBase extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'shop';
    }

    public function rules()
    {
        return [
            [['timestamp'], 'integer'],
            [['shop', 'token'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop' => 'Shop',
            'token' => 'Token',
            'timestamp' => 'Timestamp',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['shop_id' => 'id']);
    }
}
