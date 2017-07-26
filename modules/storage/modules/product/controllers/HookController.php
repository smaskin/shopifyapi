<?php

namespace app\modules\storage\modules\product\controllers;

use app\modules\storage\behaviors\WebHookBehavior;
use app\modules\storage\modules\product\models\Product;
use Yii;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * @mixin WebHookBehavior
 */
class HookController extends Controller
{
    public function behaviors()
    {
        return ['webHook' => WebHookBehavior::classname()];
    }

    public function actionUpdate()
    {
        $shop = $this->getShopModel();
        $data = $this->getWebhookData();
        Yii::error($data);
        $model = ($existedProduct = $shop->getProducts()->andWhere(['shop_id' => $shop->id, 'shop_product_id' => $data->id])->one())
            ? $existedProduct
            : new Product(['shop_id' => $shop->id, 'shop_product_id' => $data->id]);
        $model->title = $data->title;
        $model->description = $data->body_html;
        $prices = $meta = $collections = [];
        foreach ($data->variants as $variant) {
            $prices[] = (int)($variant->price);
        }
        foreach ($this->getMeta($shop->shop, $data->id) as $tag) {
            $meta[] = $tag['key'] . ': ' . $tag['value'];
        }
        foreach ($this->getCollections($shop->shop, $data->id) as $collection) {
            $collections[] = $collection['title'];
        }
        $model->price = implode(',', $prices);
        $model->meta = implode('<br />', $meta);
        $model->collections = implode('<br />', $collections);
        $model->save();
        Yii::error($model->getErrors());
    }

    private function getMeta($shop, $id)
    {
        $httpClient = new Client();
        $response = $httpClient->createRequest()
            ->setMethod('get')
            ->addHeaders(['X-Shopify-Access-Token' => $this->getShopModel()->token])
            ->setUrl("https://{$shop}/admin/products/{$id}/metafields.json")
            ->send();
        if (!$response->isOk) {
            throw new BadRequestHttpException('ShopifyAPI status code:' . $response->statusCode);
        }
        Yii::error($response->data);
        return $response->data['metafields'];
    }

    private function getCollections($shop, $id)
    {
        $httpClient = new Client();
        $response = $httpClient->createRequest()
            ->setMethod('get')
            ->addHeaders(['X-Shopify-Access-Token' => $this->getShopModel()->token])
            ->setUrl("https://{$shop}/admin/custom_collections.json?product_id={$id}")
            ->send();
        if (!$response->isOk) {
            throw new BadRequestHttpException('ShopifyAPI status code:' . $response->statusCode);
        }
        Yii::error($response->data);
        return $response->data['custom_collections'];
    }

}
