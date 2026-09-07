<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use app\models\Transaction;
use app\models\TransactionItem;
use app\models\Product;
use yii\data\ActiveDataProvider;

class TransactionController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionCheckout()
    {
        $cart = Yii::$app->session->get(CartController::SESSION_KEY, []);

        if (empty($cart)) {
            Yii::$app->session->setFlash('error', 'Keranjang Anda kosong.');
            return $this->redirect(['cart/index']);
        }

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $qty) {
            $product = Product::findOne($productId);
            if (!$product) {
                continue;
            }
            $qty = min($qty, $product->stock);
            if ($qty <= 0) {
                continue;
            }
            $subtotal = $product->price * $qty;
            $items[] = [
                'product'  => $product,
                'qty'      => $qty,
                'subtotal' => $subtotal,
            ];
            $total += $subtotal;
        }

        if (empty($items)) {
            Yii::$app->session->setFlash('error', 'Semua produk di keranjang sudah tidak tersedia / stok habis.');
            return $this->redirect(['cart/index']);
        }

        // Kalau ini submit tombol "Konfirmasi & Bayar" (POST), baru proses simpan + kurangi stok
        if (Yii::$app->request->isPost) {
            $bayar = (float) Yii::$app->request->post('bayar', 0);

            if ($bayar < $total) {
                Yii::$app->session->setFlash('error', 'Jumlah bayar kurang dari total belanja.');
                return $this->render('checkout', [
                    'items' => $items,
                    'total' => $total,
                ]);
            }

            return $this->processConfirm($items, $total, $bayar);
        }

        // GET -> cuma tampilkan halaman ringkasan/konfirmasi, BELUM ada perubahan data
        return $this->render('checkout', [
            'items' => $items,
            'total' => $total,
        ]);
    }

    private function processConfirm($items, $total, $bayar)
    {
        $db = Yii::$app->db;
        $dbTransaction = $db->beginTransaction();

        try {
            $kembalian = $bayar - $total;

            $transaction = new Transaction();
            $transaction->user_name     = Yii::$app->user->identity->username;
            $transaction->code          = $this->generateUniqueCode();
            $transaction->total         = $total;
            $transaction->paid_amount   = $bayar;
            $transaction->change_amount = $kembalian;
            $transaction->created_at    = date('Y-m-d H:i:s');

            if (!$transaction->save()) {
                throw new \Exception('Gagal menyimpan transaksi.');
            }

            foreach ($items as $item) {
                $product = $item['product'];
                $qty     = $item['qty'];

                $product->refresh();
                if ($product->stock < $qty) {
                    throw new \Exception('Stok produk "' . $product->name . '" tidak mencukupi.');
                }

                $transactionItem = new TransactionItem();
                $transactionItem->transaction_id = $transaction->id;
                $transactionItem->product_id     = $product->id;
                $transactionItem->qty            = $qty;
                $transactionItem->price          = $product->price;
                $transactionItem->subtotal       = $item['subtotal'];

                if (!$transactionItem->save()) {
                    throw new \Exception('Gagal menyimpan item transaksi.');
                }

                $product->stock -= $qty;
                if (!$product->save(false)) {
                    throw new \Exception('Gagal mengurangi stok produk "' . $product->name . '".');
                }
            }

            $dbTransaction->commit();

            Yii::$app->session->remove(CartController::SESSION_KEY);
            Yii::$app->session->setFlash('success', 'Transaksi berhasil! Kode: ' . $transaction->code);

            return $this->redirect(['transaction/success', 'id' => $transaction->id]);
        } catch (\Exception $e) {
            $dbTransaction->rollBack();
            Yii::$app->session->setFlash('error', 'Transaksi gagal: ' . $e->getMessage());
            return $this->redirect(['cart/index']);
        }
    }

    private function generateUniqueCode()
    {
        do {
            $code = 'TRX-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
        } while (Transaction::find()->where(['code' => $code])->exists());

        return $code;
    }

    public function actionSuccess($id)
    {
        $transaction = Transaction::find()
            ->where(['id' => $id])
            ->andWhere(['user_name' => Yii::$app->user->identity->username])
            ->one();

        if (!$transaction) {
            throw new NotFoundHttpException('Transaksi tidak ditemukan.');
        }

        $items = TransactionItem::find()->where(['transaction_id' => $transaction->id])->all();

        return $this->render('success', [
            'transaction' => $transaction,
            'items'       => $items,
        ]);
    }

    public function actionHistory()
    {
        $query = Transaction::find()
            ->where(['user_name' => Yii::$app->user->identity->username])
            ->orderBy(['created_at' => SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 10],
        ]);

        return $this->render('history', [
            'dataProvider' => $dataProvider,
        ]);
    }
}