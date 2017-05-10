<?php

namespace app\components;

use app\components\parser\HandlerOfData;
use app\components\parser\NgsProvider;
use app\models\Promotion;
use app\models\Section;

/**
 * Class HandlerOfDataOfPromotion
 * @package app\components
 */
class HandlerOfDataOfPromotion implements HandlerOfData
{
    /** @var  string */
    private $_state;

    private $_attributes = [
        'section_id',
        'title',
        'description',
    ];

    /**
     * @inheritdoc
     */
    public function setData($data)
    {
        $this->_state = '';
        $sectionId    = $this->getSectionId($data);
        $promotion    = Promotion::getModel(['promotion_id' => $data['id'], 'section_id' => $sectionId]);

        if ($promotion->isNewRecord) {
            $attributes                 = array_intersect_key($data, array_flip($this->_attributes));
            $attributes['section_id']   = $sectionId;
            $attributes['promotion_id'] = $data['id'];

            $promotion->attributes = $attributes;
            $promotion->save();

            return true;
        } else {
            $this->_state = NgsProvider::STATUS_DATA_ALREADY_EXIST;

            return false;
        }
    }

    /**
     * @param array $data
     *
     * @return integer
     */
    private function getSectionId($data)
    {
        $section = Section::findOne(['name' => $data['section']]);

        if (!$section) {
            $section       = new Section();
            $section->name = $data['section'];
            $section->save();
        }

        return $section['id'];
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->_state;
    }
}