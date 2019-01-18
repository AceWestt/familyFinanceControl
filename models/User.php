<?php

namespace app\models;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    /* public $id;
     public $username;
     public $email;
     public $password;
     public $authKey;
     public $accessToken;




     private static $users = [
         '100' => [
             'id' => '100',
             'username' => 'admin',
             'password' => 'admin',
             'authKey' => 'test100key',
             'accessToken' => '100-token',
         ],
         '101' => [
             'id' => '101',
             'username' => 'demo',
             'password' => 'demo',
             'authKey' => 'test101key',
             'accessToken' => '101-token',
         ],
     ];


     /**
      * {@inheritdoc}
      */

    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public static function tableName()
    {
        return '{{%user}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    public function rules()
    {
        return [['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],];
    }

    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * {@inheritdoc}
     */
    public  static  function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUserName($username){
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getUserID()
    {
        return $this->userID;
    }

    public function getUserRole(){
        return $this->role_id;
    }

    public function getUserFullName(){
        return $this->lastName.' '.$this->firstName;
    }

    public  function  getUserPhone(){
        return $this->phone;
    }

    public function getUserDoB(){
        return $this->dob;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password){
        return \Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password){
        $this->password_hash = \Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey(){
        $this->auth_key = \Yii::$app->security->generateRandomString();
    }
}
