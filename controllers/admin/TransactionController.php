<?php

namespace app\controllers\admin;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\Transaction;
use app\models\TransactionItem;

class TransactionController extends AdminController
{
    public function actionIndex()
    {
        $code = Yii::$app->request->get('code');
        $userName = Yii::$app->request->get('user_name');
        $period = Yii::$app->request->get('period', 'all'); // today | week | month | all
        $month = Yii::$app->request->get('month'); // format Y-m, dipakai kalau period=month

        $query = Transaction::find()->orderBy(['created_at' => SORT_DESC]);
        $query->andFilterWhere(['like', 'code', $code]);
        $query->andFilterWhere(['like', 'user_name', $userName]);

        switch ($period) {
            case 'today':
                $start = date('Y-m-d 00:00:00');
                $end   = date('Y-m-d 23:59:59');
                $query->andWhere(['between', 'created_at', $start, $end]);
                break;

            case 'week':
                // Senin s/d Minggu minggu ini
                $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $end   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                $query->andWhere(['between', 'created_at', $start, $end]);
                break;

            case 'month':
                if ($month) {
                    // $month formatnya "2026-09"
                    $start = $month . '-01 00:00:00';
                    $end   = date('Y-m-t 23:59:59', strtotime($start));
                    $query->andWhere(['between', 'created_at', $start, $end]);
                }
                break;

            case 'all':
            default:
                // tidak ada filter tanggal
                break;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'code' => $code,
            'user_name' => $userName,
            'period' => $period,
            'month' => $month,
        ]);
    }

    public function actionView($id)
    {
        $transaction = Transaction::findOne($id);
        if (!$transaction) {
            throw new NotFoundHttpException('Transaksi tidak ditemukan.');
        }

        $items = TransactionItem::find()->where(['transaction_id' => $transaction->id])->all();

        return $this->render('view', [
            'transaction' => $transaction,
            'items' => $items,
        ]);
    }
}