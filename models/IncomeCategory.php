<?php
/**
 * Created by PhpStorm.
 * User: Ace
 * Date: 09.01.2019
 * Time: 11:01
 */

namespace app\models;


use yii\db\ActiveRecord;

class IncomeCategory extends ActiveRecord
{
    public static function tableName()
    {
        return 'IncomeCategory';
    }

}