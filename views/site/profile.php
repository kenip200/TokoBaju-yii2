<?php

use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var app\models\ProfileForm $model */

$this->title = 'Profile Saya';
?>
<div class="site-profile">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>

    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger">
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(['id' => 'profile-form']); ?>

            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

            <hr>
            <p class="text-muted">
                Kosongkan kedua field di bawah ini jika tidak ingin mengganti password.
            </p>

            <?= $form->field($model, 'password')->passwordInput(['autocomplete' => 'new-password']) ?>

            <?= $form->field($model, 'confirm_password')->passwordInput(['autocomplete' => 'new-password']) ?>

            <div class="form-group">
                <?= Html::submitButton('Simpan Perubahan', ['class' => 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>