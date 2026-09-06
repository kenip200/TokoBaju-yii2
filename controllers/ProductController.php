<?php

namespace app\controllers;

use Yii;
use app\models\Product;
use app\models\Category;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;

class ProductController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // semua user yang sudah login
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $categoryId = Yii::$app->request->get('category_id');
        $search = Yii::$app->request->get('search');

        $query = Product::find();

        // filter kategori
        $query->andFilterWhere(['category_id' => $categoryId]);

        // search nama produk (LIKE)
        $query->andFilterWhere(['like', 'name', $search]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ],
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC],
            ],
        ]);

        $categories = Category::find()->all();

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'categories' => $categories,
            'categoryId' => $categoryId,
            'search' => $search,
        ]);
    }
}