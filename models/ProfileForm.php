<?php

namespace app\models;

use Yii;
use yii\base\Model;

class ProfileForm extends Model
{
    public $id;
    public $username;
    public $password;
    public $confirm_password;

    public function rules()
    {
        return [
            [['username'], 'required'],
            [['username'], 'string', 'min' => 3, 'max' => 255],
            [['username'], 'validateUsername'],

            [['password', 'confirm_password'], 'string', 'min' => 6],
            [
                'password', 'required',
                'when' => function ($model) {
                    return !empty($model->confirm_password);
                },
                'message' => 'Password wajib diisi jika ingin mengganti password.',
            ],
            [
                'confirm_password', 'required',
                'when' => function ($model) {
                    return !empty($model->password);
                },
                'message' => 'Konfirmasi password wajib diisi.',
            ],
            [
                'confirm_password', 'compare', 'compareAttribute' => 'password',
                'message' => 'Konfirmasi password tidak sama dengan password.',
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'username' => 'Username',
            'password' => 'Password Baru',
            'confirm_password' => 'Konfirmasi Password Baru',
        ];
    }

    public function validateUsername($attribute, $params)
    {
        $exists = User::find()
            ->where(['username' => $this->username])
            ->andWhere(['<>', 'id', $this->id])
            ->exists();

        if ($exists) {
            $this->addError($attribute, 'Username sudah digunakan.');
        }
    }
}