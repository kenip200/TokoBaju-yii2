<?php
/**
 * Struktur tabel yang dipakai (sesuai tokobaju.sql):
 *
 * transaction
 *   - id, user_name, code, total, paid_amount, change_amount, created_at
 *
 * transaction_item
 *   - id, transaction_id, product_id, qty, price, subtotal, created_at
 *
 * product
 *   - id, category_id, name, price, stock, created_at, updated_at, description, image
 *
 * Letakkan file ini di: controllers/admin/DashboardController.php
 * (namespace disesuaikan supaya route-nya jadi admin/dashboard/index,
 *  konsisten dengan route admin/product/index yang sudah kamu pakai)
 */

namespace app\controllers\admin;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\db\Query;

class DashboardController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'], // sesuaikan dengan RBAC/role admin yang kamu pakai
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        // ambil filter tanggal dari GET, default: 7 hari terakhir
        $tanggalAwal  = Yii::$app->request->get('tanggal_awal', date('Y-m-d', strtotime('-6 days')));
        $tanggalAkhir = Yii::$app->request->get('tanggal_akhir', date('Y-m-d'));

        // jaga-jaga kalau user menukar urutan tanggal
        if ($tanggalAwal > $tanggalAkhir) {
            [$tanggalAwal, $tanggalAkhir] = [$tanggalAkhir, $tanggalAwal];
        }

        $awalDT  = $tanggalAwal . ' 00:00:00';
        $akhirDT = $tanggalAkhir . ' 23:59:59';

        // ---------- 1. Total penjualan harian ----------
        $penjualanHarianRaw = (new Query())
            ->select([
                'tanggal' => 'DATE(t.created_at)',
                'total_transaksi' => 'COUNT(DISTINCT t.id)',
                'total_penjualan' => 'SUM(ti.subtotal)',
            ])
            ->from(['t' => 'transaction'])
            ->innerJoin(['ti' => 'transaction_item'], 'ti.transaction_id = t.id')
            ->where(['between', 't.created_at', $awalDT, $akhirDT])
            ->groupBy('DATE(t.created_at)')
            ->orderBy('tanggal ASC')
            ->all();

        // index-kan hasil query per tanggal supaya gampang dicocokkan
        $penjualanPerTanggal = [];
        foreach ($penjualanHarianRaw as $row) {
            $penjualanPerTanggal[$row['tanggal']] = $row;
        }

        // isi semua tanggal dalam rentang filter, hari tanpa transaksi diisi 0
        $penjualanHarian = [];
        $periode = new \DatePeriod(
            new \DateTime($tanggalAwal),
            new \DateInterval('P1D'),
            (new \DateTime($tanggalAkhir))->modify('+1 day')
        );
        foreach ($periode as $tanggalObj) {
            $tanggal = $tanggalObj->format('Y-m-d');
            $penjualanHarian[] = $penjualanPerTanggal[$tanggal] ?? [
                'tanggal' => $tanggal,
                'total_transaksi' => 0,
                'total_penjualan' => 0,
            ];
        }

        // ---------- 2. Barang terlaris ----------
        $barangTerlaris = (new Query())
            ->select([
                'product_id' => 'p.id',
                'nama_produk' => 'p.name',
                'total_qty' => 'SUM(ti.qty)',
                'total_pendapatan' => 'SUM(ti.subtotal)',
            ])
            ->from(['ti' => 'transaction_item'])
            ->innerJoin(['t' => 'transaction'], 't.id = ti.transaction_id')
            ->innerJoin(['p' => 'product'], 'p.id = ti.product_id')
            ->where(['between', 't.created_at', $awalDT, $akhirDT])
            ->groupBy(['p.id', 'p.name'])
            ->orderBy(['total_qty' => SORT_DESC])
            ->limit(10)
            ->all();

        // ---------- 3. Ringkasan total (dipakai untuk kartu di atas dashboard) ----------
        $grandTotal     = array_sum(array_column($penjualanHarian, 'total_penjualan'));
        $grandTransaksi = array_sum(array_column($penjualanHarian, 'total_transaksi'));

        return $this->render('index', [
            'penjualanHarian'  => $penjualanHarian,
            'barangTerlaris'   => $barangTerlaris,
            'grandTotal'       => $grandTotal,
            'grandTransaksi'   => $grandTransaksi,
            'tanggalAwal'      => $tanggalAwal,
            'tanggalAkhir'     => $tanggalAkhir,
        ]);
    }
}
