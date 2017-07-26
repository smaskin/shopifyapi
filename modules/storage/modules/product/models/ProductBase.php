<?php

namespace app\modules\storage\modules\product\models;

use app\modules\storage\models\Shop;

/**
 * @property integer $id
 * @property integer $shop_id
 * @property integer $shop_product_id
 * @property string $title
 * @property string $description
 * @property string $price
 * @property string $meta
 * @property string $collections
 */
class ProductBase extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'product';
    }

    public function rules()
    {
        return [
            [['shop_id', 'shop_product_id'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['description', 'price', 'meta', 'collections'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'shop_id' => 'Shop ID',
            'shop_product_id' => 'Shop product ID',
            'title' => 'Title',
            'description' => 'Description',
            'price' => 'Price',
            'meta' => 'Metafields',
            'collections' => 'Collections'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShop()
    {
        return $this->hasOne(Shop::className(), ['id' => 'shop_id']);
    }
}
