<?php

declare(strict_types=1);

namespace app\models;

use Yii;
use yii\base\Model;

class SignupForm extends Model
{
    public string $username = '';
    public string $password = '';
    public string $role = 'customer';

    /**
     * @return array the validation rules.
     */
    public function rules(): array
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => User::class, 'message' => 'Username ini sudah dipakai.'],
            ['username', 'string', 'min' => 3, 'max' => 255],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup(): User|null
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->username = $this->username;
        $user->role = $this->role;
        $user->status = 10;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->created_at = time();
        $user->updated_at = time();

        return $user->save() ? $user : null;
    }
}