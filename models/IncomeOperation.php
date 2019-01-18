<?php
/**
 * Created by PhpStorm.
 * User: Ace
 * Date: 10.01.2019
 * Time: 17:27
 */

namespace app\models;


use yii\db\ActiveRecord;

class IncomeOperation extends ActiveRecord
{
    public static function tableName()
    {
        return 'incomeOperation';
    }

}