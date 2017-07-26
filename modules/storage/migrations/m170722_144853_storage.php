<?php

use yii\db\Migration;

class m170722_144853_storage extends Migration
{
    public $shopTable = 'shop';
    public $productTable = 'product';
    public $fk = 'fk_shop_product';

    public function up()
    {
        $this->createTable($this->shopTable, [
            'id' => $this->primaryKey(),
            'shop' => $this->string(),
            'token' => $this->string(),
            'timestamp' => $this->integer()
        ]);

        $this->createTable($this->productTable, [
            'id' => $this->primaryKey(),
            'shop_id' => $this->integer(),
            'shop_product_id' => $this->bigInteger(),
            'title' => $this->string(),
            'description' => $this->text(),
            'price' => $this->text(),
            'meta' => $this->text(),
            'collections' => $this->text()
        ]);

        $this->addForeignKey($this->fk, $this->productTable, 'shop_id', $this->shopTable, 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey($this->fk, $this->productTable);
        $this->dropTable($this->shopTable);
        $this->dropTable($this->productTable);
    }
}
