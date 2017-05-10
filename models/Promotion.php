<?php

namespace app\models;

use app\components\HandlerOfDataOfPromotion;
use app\components\parser\NgsProvider;
use Yii;

/**
 * This is the model class for table "promotion".
 *
 * @property integer $id
 * @property integer $section_id
 * @property string  $promotion_id
 * @property string  $title
 * @property string  $description
 *
 * @property Section $section
 */
class Promotion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'promotion';
    }

    /**
     * @param string $section
     */
    public static function parse($section = null)
    {
        Yii::$app->parser->parseData(new NgsProvider(), new HandlerOfDataOfPromotion(), [
            'section' => Section::findOne($section)->name,
        ]);
    }

    public static function getModel($condition)
    {
        $model = static::findOne($condition);

        return $model ?: new static();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['section_id'], 'integer'],
            [['description'], 'string'],
            [['title', 'promotion_id'], 'string', 'max' => 255],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => Section::className(), 'targetAttribute' => ['section_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'section_id'  => 'Section ID',
            'title'       => 'Title',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(Section::className(), ['id' => 'section_id']);
    }
}
