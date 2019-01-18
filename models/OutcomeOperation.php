<?php
/**
 * Created by PhpStorm.
 * User: Ace
 * Date: 10.01.2019
 * Time: 17:34
 */

namespace app\models;


use yii\db\ActiveRecord;

class OutcomeOperation extends ActiveRecord
{
    public static function tableName()
    {
        return 'outcomeOperation';
    }

}