<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\Product;

class CartController extends Controller
{
    const SESSION_KEY = 'cart';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // hanya user yang sudah login
                    ],
                ],
            ],
        ];
    }

    /**
     * Ambil isi cart dari session
     * Format: [ product_id => qty, ... ]
     */
    protected function getCart()
    {
        return Yii::$app->session->get(self::SESSION_KEY, []);
    }

    protected function saveCart($cart)
    {
        Yii::$app->session->set(self::SESSION_KEY, $cart);
    }

    protected function findProduct($id)
    {
        $model = Product::findOne($id);
        if ($model === null) {
            throw new NotFoundHttpException('Produk tidak ditemukan.');
        }
        return $model;
    }

    // Tambah produk ke cart (dipanggil dari form di product/view)
    public function actionAdd()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $qty = (int) $request->post('qty', 1);

        $product = $this->findProduct($id);

        if ($qty < 1) {
            $qty = 1;
        }
        if ($qty > $product->stock) {
            $qty = $product->stock;
        }

        if ($product->stock <= 0) {
            Yii::$app->session->setFlash('error', 'Stok produk habis.');
            return $this->redirect(['product/view', 'id' => $id]);
        }

        $cart = $this->getCart();

        if (isset($cart[$id])) {
            $newQty = $cart[$id] + $qty;
            $cart[$id] = min($newQty, $product->stock);
        } else {
            $cart[$id] = $qty;
        }

        $this->saveCart($cart);

        Yii::$app->session->setFlash('success', 'Produk berhasil ditambahkan ke keranjang.');
        return $this->redirect(['cart/index']);
    }

    // Tampilkan isi cart
    public function actionIndex()
    {
        $cart = $this->getCart();
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::findOne($productId);

            // Jika produk sudah dihapus dari DB, skip & bersihkan dari cart
            if ($product === null) {
                unset($cart[$productId]);
                continue;
            }

            // Jaga-jaga kalau qty di session melebihi stock terbaru
            if ($qty > $product->stock) {
                $qty = $product->stock;
                $cart[$productId] = $qty;
            }

            $subtotal = $product->price * $qty;
            $total += $subtotal;

            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        $this->saveCart($cart);

        return $this->render('index', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    // Update qty item di cart
    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = $request->post('id');
        $qty = (int) $request->post('qty', 1);

        $product = $this->findProduct($id);
        $cart = $this->getCart();

        if (isset($cart[$id])) {
            if ($qty < 1) {
                unset($cart[$id]);
            } else {
                $cart[$id] = min($qty, $product->stock);
            }
            $this->saveCart($cart);
        }

        return $this->redirect(['cart/index']);
    }

    // Hapus 1 item dari cart
    public function actionRemove($id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->saveCart($cart);
        }
        return $this->redirect(['cart/index']);
    }

    // Kosongkan semua cart
    public function actionClear()
    {
        Yii::$app->session->remove(self::SESSION_KEY);
        return $this->redirect(['cart/index']);
    }
}