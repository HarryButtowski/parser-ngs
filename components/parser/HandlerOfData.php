<?php

namespace app\components\parser;

/**
 * Interface HandlerOfData
 * @package app\components\parser
 */
interface HandlerOfData
{
    /**
     * @param array $data
     *
     * @return boolean
     */
    public function setData($data);

    /**
     * @return string
     */
    public function getState();
}