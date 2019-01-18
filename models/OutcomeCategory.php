<?php
/**
 * Created by PhpStorm.
 * User: Ace
 * Date: 09.01.2019
 * Time: 13:01
 */

namespace app\models;


use yii\db\ActiveRecord;

class OutcomeCategory extends ActiveRecord
{
    public static function tableName()
    {
        return 'outcomeCategory';
    }

}