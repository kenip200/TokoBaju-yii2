<?php

namespace app\controllers\admin;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\Transaction;
use app\models\TransactionItem;
use kartik\mpdf\Pdf;

class TransactionController extends AdminController
{
    public function actionIndex()
    {
        $filters = $this->getFilters();
        $query = $this->buildFilteredQuery($filters);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 15],
        ]);

        return $this->render('index', array_merge(
            ['dataProvider' => $dataProvider],
            $filters
        ));
    }

    public function actionExportPdf()
    {
        $filters = $this->getFilters();
        $query = $this->buildFilteredQuery($filters);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        // Body HTML murni, TANPA tag <style> di dalamnya
        $body = $this->renderPartial('_pdf_history', [
            'dataProvider' => $dataProvider,
            'filters' => $filters,
        ]);

        $css = <<<CSS
            body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; }
            h2 { text-align: center; margin-bottom: 4px; }
            .subtitle { text-align: center; margin-bottom: 15px; color: #555; }
            .filter-info { margin-bottom: 12px; padding: 8px; background-color: #f5f5f5; border: 1px solid #ddd; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th, td { border: 1px solid #999; padding: 6px 8px; font-size: 11px; }
            th { background-color: #343a40; color: #fff; text-align: center; }
            td.text-right { text-align: right; }
            td.text-center { text-align: center; }
            tfoot td { font-weight: bold; background-color: #f0f0f0; }
        CSS;

        $pdf = new Pdf([
            'mode' => Pdf::MODE_CORE,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_BROWSER,
            'content' => $body,       // hanya HTML body, tanpa <style>
            'cssInline' => $css,      // CSS dipisah lewat properti khusus ini
            'options' => ['title' => 'Riwayat Transaksi Global'],
            'methods' => [
                'SetHeader' => ['Riwayat Transaksi Global||' . date('d-m-Y')],
                'SetFooter' => ['||Halaman {PAGENO}'],
            ],
        ]);

        return $pdf->render();
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

    /**
     * Ambil parameter filter dari request (dipakai bareng index & export pdf)
     */
    private function getFilters(): array
    {
        return [
            'code' => Yii::$app->request->get('code'),
            'user_name' => Yii::$app->request->get('user_name'),
            'period' => Yii::$app->request->get('period', 'all'),
            'month' => Yii::$app->request->get('month'),
        ];
    }

    /**
     * Bangun query Transaction berdasarkan filter yang diberikan
     */
    private function buildFilteredQuery(array $filters)
    {
        $query = Transaction::find()->orderBy(['created_at' => SORT_DESC]);
        $query->andFilterWhere(['like', 'code', $filters['code']]);
        $query->andFilterWhere(['like', 'user_name', $filters['user_name']]);

        switch ($filters['period']) {
            case 'today':
                $start = date('Y-m-d 00:00:00');
                $end   = date('Y-m-d 23:59:59');
                $query->andWhere(['between', 'created_at', $start, $end]);
                break;

            case 'week':
                $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
                $end   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
                $query->andWhere(['between', 'created_at', $start, $end]);
                break;

            case 'month':
                if ($filters['month']) {
                    $start = $filters['month'] . '-01 00:00:00';
                    $end   = date('Y-m-t 23:59:59', strtotime($start));
                    $query->andWhere(['between', 'created_at', $start, $end]);
                }
                break;

            case 'all':
            default:
                break;
        }

        return $query;
    }
}