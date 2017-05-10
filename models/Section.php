<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "section".
 *
 * @property integer     $id
 * @property string      $name
 *
 * @property Promotion[] $promotions
 */
class Section extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'section';
    }

    /**
     * @return array
     */
    public static function getSectionsForSelect()
    {
        return ArrayHelper::map(Section::find()->asArray()->all(), 'id', 'name');
    }

    /**
     * @return array
     */
    public static function getSectionsForSelectOfParse()
    {
        return ArrayHelper::map(Section::find()->asArray()->all(), 'name', 'name');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'   => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPromotions()
    {
        return $this->hasMany(Promotion::className(), ['section_id' => 'id']);
    }
}
