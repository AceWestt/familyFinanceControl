<?php

namespace app\controllers;

use app\models\IncomeCategory;
use app\models\IncomeOperation;
use app\models\OutcomeCategory;
use app\models\OutcomeOperation;
use app\models\Role;
use app\models\Total;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Admin;
use app\models\User;
use yii\helpers\Json;

class SiteController extends AppController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */


    /**
     * Login action.
     *
     * @return Response|string
     */

    public function actionIndex(){
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        else{
            $toCheck = '0';
            $incomeCategories = IncomeCategory::find()->asArray()->all();
            $outcomeCategories = OutcomeCategory::find()->asArray()->all();
            $totalIncomeCash = 0;
            $totalIncomeCard = 0;
            $totalOutcomeCash = 0;
            $totalOutcomeCard = 0;
            $totalIncome = 0;
            $totalOutcome = 0;
            $incomeOperations = IncomeOperation::find()->asArray()->all();
            $outcomeOperations  = OutcomeOperation::find()->asArray()->all();
            if($incomeOperations != null){
                foreach ($incomeOperations as $operation){
                    $totalIncomeCash += $operation['incomeCash'];
                    $totalIncomeCard += $operation['incomeCard'];
                }
            }
            if($outcomeOperations != null){
                foreach ($outcomeOperations as $operation){
                    $totalOutcomeCash += $operation['outcomeCash'];
                    $totalOutcomeCard += $operation['outcomeCard'];
                }
            }
            $totalIncome = $totalIncomeCash + $totalIncomeCard;
            $totalOutcome = $totalOutcomeCash + $totalOutcomeCard;
            $total = Total::find()->one();
            if($total == null){
                $total = new Total();
                $total->totalCash = $totalIncomeCash - $totalOutcomeCash;
                $total->totalCard = $totalIncomeCard - $totalOutcomeCard;
                $total->total = $totalIncome - $totalOutcome;
                $total->save();
            }
            else{
                $total = Total::findOne($total->getPrimaryKey());
                $total->totalCash = $totalIncomeCash - $totalOutcomeCash;
                $total->totalCard = $totalIncomeCard - $totalOutcomeCard;
                $total->total = $totalIncome - $totalOutcome;
                $total->save();
            }

            $total = Total::find()->asArray()->one();




            return $this->render('index', compact('incomeCategories',
                'outcomeCategories', 'total', 'incomeOperations', 'outcomeOperations'));
        }

    }

    public function actionLogin()
    {
        $admin = Admin::find()->where(['userName' => 'MasterWestt'])->one();
        $adminRole = Role::find()->where(['roleName' => 'admin'])->one();
        $userRoel = Role::find()->where(['roleName' => 'user'])->one();
        if(empty($adminRole)){
            $adminRole = new Role();
            $adminRole->roleName = 'admin';
            $adminRole->save();
        }
        if(empty($userRoel)){
            $userRole = new Role();
            $userRole->roleName = 'user';
        }
        if(empty($admin)) {
            $adminRole = $adminRole = Role::find()->where(['roleName' => 'admin'])->one();
            $newAdmin = new Admin();
            $newAdmin->userName = 'admin';
            $newAdmin->lastName = 'Khamzaev';
            $newAdmin->firstName = 'Asilbek';
            $newAdmin->phone = '+99894-567-77-76';
            $newAdmin->email = 'asilbekkhamzaev@gmail.com';
            $newAdmin->role_id = $adminRole['id'];
            $newAdmin->password = Yii::$app->security->generatePasswordHash('admin');
            $newAdmin->save();
        }

        $adminUser = User::find()->where(['role_id' => $adminRole]);
        if(empty($adminUser)){
            $admins = Admin::find()->asArray()->all();
            foreach ($admins as $admin) {
                $user = new User();
                $user->userID = $admin['id'];
                $user->username = $admin['userName'];
                $user->email = $admin['email'];
                $user->role_id = $admin['role_id'];
                $user->password_hash = $admin['password'];
                $user->generateAuthKey();
                $user->save();
            }
        }

        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $id = Yii::$app->user->identity->getId();
            $user = User::findOne($id);
            $roleID = $user->getUserRole();
            $user_id = $user->getId();

            if($roleID==3){
                return $this->redirect(['site/index', 'id' => $user_id]);
            }
            if($roleID==4){
                return $this->redirect(['site/index', 'id' => $user_id]);
            }

            return $this->goBack();
        }




        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionCheckUserNameExists(){
        if(isset($_POST['userName'])){
            $id = $_POST['id'];
            if($id == 0){
                $user = User::findByUserName($_POST['userName']);
                if($user != null){
                    return Json::encode('exists');
                }
                else{
                    return Json::encode('available');
                }
            }
            else{
                $user = User::find()->where(['id' => $id])->andWhere(['username' => $_POST['userName']])->one();
                if($user != null){
                    return Json::encode('available');
                }
                else{
                    $user = User::findByUserName($_POST['userName']);
                    if($user != null){
                        return Json::encode('exists');
                    }
                    else{
                        return Json::encode('available');
                    }
                }
            }

        }

    }

    public function actionCheckIncomeCategoryExists(){
        if(isset($_POST['title'])){
            $id = $_POST['id'];
            if($id == 0){
                $category = IncomeCategory::find()->where(['incomeCategory' => $_POST['title']])->one();
                if($category != null){
                    return Json::encode('exists');
                }
                else{
                    return Json::encode('available');
                }
            }
            else{
                $category = IncomeCategory::find()->where(['id' => $id])->andWhere(['incomeCategory' => $_POST['title']])->one();
                if($category != null){
                    return Json::encode('available');
                }
                else{
                    $category = IncomeCategory::find()->where(['incomeCategory' => $_POST['title']])->one();
                    if($category != null){
                        return Json::encode('exists');
                    }
                    else{
                        return Json::encode('available');
                    }
                }
            }

        }

    }

    public function actionCheckOutcomeCategoryExists(){
        if(isset($_POST['title'])){
            $id = $_POST['id'];
            if($id == 0){
                $category = OutcomeCategory::find()->where(['outcomeCategory' => $_POST['title']])->one();
                if($category != null){
                    return Json::encode('exists');
                }
                else{
                    return Json::encode('available');
                }
            }
            else{
                $category = OutcomeCategory::find()->where(['id' => $id])->andWhere(['outcomeCategory' => $_POST['title']])->one();
                if($category != null){
                    return Json::encode('available');
                }
                else{
                    $category = OutcomeCategory::find()->where(['outcomeCategory' => $_POST['title']])->one();
                    if($category != null){
                        return Json::encode('exists');
                    }
                    else{
                        return Json::encode('available');
                    }
                }
            }

        }

    }

    public function actionCheckOldPassword(){
        if($_POST['id']){
            $userID = Yii::$app->user->getId();
            if($_POST['id'] == $userID){
                $user = User::findOne($_POST['id']);
                if(password_verify($_POST['oldPassword'], $user['password_hash'])){
                    return Json::encode('matches');
                }
                else{
                    return Json::encode('notMatch');
                }
            }
        }
    }

    public function actionAddIncomeOperation(){
        if(isset($_POST['date'])){
            $date = $_POST['date'];
            $title = $_POST['title'];
            $cash = $_POST['cash'];
            $card = $_POST['card'];
            $total = $cash + $card;
            $category = $_POST['category'];
            $description = $_POST['description'];
            $userID = Yii::$app->user->getId();

            $operation = new IncomeOperation();
            $operation->date = $date;
            $operation->incomeShortTitle = $title;
            $operation->incomeCash = $cash;
            $operation->incomeCard = $card;
            $operation->incomeTotal = $total;
            $operation->incomeDetail = $description;
            $operation->incomeCategory_id = $category;
            $operation->user_id = $userID;
            $operation->save();

            return Json::encode('success');
        }
    }

    public function actionAddOutcomeOperation(){
        if(isset($_POST['date'])){
            $date = $_POST['date'];
            $title = $_POST['title'];
            $cash = $_POST['cash'];
            $card = $_POST['card'];
            $total = $cash + $card;
            $category = $_POST['category'];
            $description = $_POST['description'];
            $userID = Yii::$app->user->getId();

            $operation = new OutcomeOperation();
            $operation->date = $date;
            $operation->outcomeShortTitle = $title;
            $operation->outcomeCash = $cash;
            $operation->outcomeCard = $card;
            $operation->outcomeTotal = $total;
            $operation->outcomeDetail = $description;
            $operation->outcomeCategory_id = $category;
            $operation->user_id = $userID;
            $operation->save();

            return Json::encode('success');
        }
    }

    public function actionGetIncomeOutcomeOperations(){
        $incomeOperations = IncomeOperation::find()->asArray()->all();
        $outcomeOperations = OutcomeOperation::find()->asArray()->all();
        $operations= array($incomeOperations, $outcomeOperations);
        return Json::encode($incomeOperations);
    }

    public function actionEditProfile($id){
        $this->view->title = 'Редактировать Профиль';

        $currentUserID = Yii::$app->user->getId();

        if($id == $currentUserID){
            $user = User::find()->where(['id' => $id])->asArray()->one();
            return $this->render('editProfile', compact('user'));
        }

    }

    public function actionEditUser(){
        $userID = Yii::$app->user->getId();
        if(isset($_POST['id'])){
            if($_POST['id'] == $userID){
                $user = User::findOne($_POST['id']);
                $user->username = $_POST['username'];
                $user->firstName = $_POST['firstname'];
                $user->lastName = $_POST['lastname'];
                $user->email = $_POST['email'];
                $user->phone = $_POST['phone'];
                $user->save();

                return Json::encode('success');
            }
        }
    }

    public function actionChangePassword(){
        $userID = Yii::$app->user->getId();
        if(isset($_POST['id'])){
            if($_POST['id'] == $userID){
                $user = User::findOne($_POST['id']);
                $user->password_hash = Yii::$app->security->generatePasswordHash($_POST['newPassword']);
                $user->save();

                return Json::encode('success');
            }
        }
    }





    /**
     * Displays contact page.
     *
     * @return Response|string
     */


    /**
     * Displays about page.
     *
     * @return string
     */



}
