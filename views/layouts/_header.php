<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;
use yii\helpers\Html;

$isGuest = Yii::$app->user->isGuest;
$isAdmin = !$isGuest && Yii::$app->user->identity->role === 'admin';
$isCustomer = !$isGuest && !$isAdmin;

$items = [
    [
        'label' => 'Dashboard',
        'url' => ['/admin/dashboard/index'],
        'visible' => $isAdmin,
    ],
    [
        'label' => 'Kategori',
        'url' => ['/admin/category/index'],
        'visible' => $isAdmin,
    ],
    [
        'label' => 'Produk',
        'url' => ['/admin/product/index'],
        'visible' => $isAdmin,
    ],
    [
        'label' => 'Riwayat Transaksi',
        'url' => ['/admin/transaction/index'],
        'visible' => $isAdmin,
    ],
    [
        'label' => 'Home',
        'url' => ['/site/index'],
        'visible' => $isCustomer,
    ],
    [
        'label' => 'Produk',
        'url' => ['/product/index'],
        'visible' => $isCustomer,
    ],
    [
        'label' => 'Keranjang',
        'url' => ['/cart/index'],
        'visible' => $isCustomer,
    ],
    [
        'label' => 'Riwayat Transaksi',
        'url' => ['/transaction/history'],
        'visible' => $isCustomer,
    ],
    [
        'label' => 'Login',
        'url' => ['/site/login'],
        'visible' => $isGuest,
    ],
    [
        'label' => 'Profile',
        'url' => ['/site/profile'],
        'visible' => !$isGuest,
    ],
    [
        'label' => 'Logout (' . Html::encode(Yii::$app->user->identity?->username ?? '') . ')',
        'url' => ['/site/logout'],
        'linkOptions' => [
            'data-method' => 'post',
            'class' => 'nav-link logout',
        ],
        'visible' => !$isGuest,
    ],
];

?>
<header id="header">
    <?php NavBar::begin(
        [
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => ['class' => 'navbar-expand-md navbar-dark bg-dark fixed-top']
        ],
    ) ?>
    <?= Nav::widget(
        [
            'options' => ['class' => 'navbar-nav me-auto'],
            'encodeLabels' => false,
            'items' => $items,
        ],
    ) ?>
    <?= Html::button(
        '&#127769;',
        [
            'id' => 'theme-toggle',
            'class' => 'btn btn-link nav-link fs-5',
            'aria-label' => 'Switch to dark mode',
        ],
    ) ?>
    <?php NavBar::end() ?>
</header>
