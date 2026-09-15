<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\LoginForm;
use app\models\ProfileForm;
use Yii;
use yii\base\Security;
use yii\captcha\CaptchaAction;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\mail\MailerInterface;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\Response;

use app\models\Product;
use app\models\TransactionItem;

class SiteController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly MailerInterface $mailer,
        private readonly Security $security,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout', 'profile'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions(): array
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
            'captcha' => [
                'class' => CaptchaAction::class,
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'transparent' => true,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        // Guest tidak boleh akses beranda, lempar ke login
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        // Admin juga tidak pakai beranda customer ini
        if (Yii::$app->user->identity->role === 'admin') {
            return $this->redirect(['admin/product/index']);
        }

        $username = Yii::$app->user->identity->username;

        $topSale = Product::find()
            ->select(['product.*', 'SUM(transaction_item.qty) AS total_sold'])
            ->joinWith('transactionItems transaction_item', false)
            ->groupBy('product.id')
            ->orderBy(['total_sold' => SORT_DESC])
            ->limit(5)
            ->all();

        $recentProductIds = TransactionItem::find()
            ->select(['product_id', 'MAX(transaction.created_at) AS last_purchased'])
            ->joinWith('transaction', false)
            ->where(['transaction.user_name' => $username])
            ->groupBy('product_id')
            ->orderBy(['last_purchased' => SORT_DESC])
            ->limit(5)
            ->column();

        $recentProducts = Product::find()
            ->where(['id' => $recentProductIds])
            ->all();

        return $this->render('index', [
            'username'       => $username,
            'topSale'        => $topSale,
            'recentProducts' => $recentProducts,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin(): Response|string
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm($this->security);

        if ($model->load($this->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout(): Response
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionSignup()
    {
        $model = new \app\models\SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            $user = $model->signup();
            if ($user !== null) {
                if (Yii::$app->user->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', ['model' => $model]);
    }

    public function actionProfile()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        /** @var \app\models\User $identity */
        $identity = Yii::$app->user->identity;

        $model = new ProfileForm();
        $model->id = $identity->id;
        $model->username = $identity->username;

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $identity->username = $model->username;

            if (!empty($model->password)) {
                $identity->setPassword($model->password);
                // generateAuthKey() DIHAPUS supaya session/remember-me tidak invalid
            }

            if ($identity->save(false)) {
                // pastikan Yii::$app->user tetap sinkron dengan identity yang baru disimpan
                Yii::$app->user->setIdentity($identity);

                Yii::$app->session->setFlash('success', 'Profile berhasil diperbarui.');
                return $this->refresh();
            } else {
                Yii::$app->session->setFlash('error', 'Gagal menyimpan perubahan.');
            }
        }

        $model->password = null;
        $model->confirm_password = null;

        return $this->render('profile', [
            'model' => $model,
        ]);
    }

}
