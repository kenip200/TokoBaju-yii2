<?php

namespace app\controllers;

use app\models\Category;
use app\models\Product;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

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

    public function actionView($id)
    {
        $model = $this->findModel($id);

        return $this->render('view', [
            'model' => $model,
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Produk tidak ditemukan.');
    }
}