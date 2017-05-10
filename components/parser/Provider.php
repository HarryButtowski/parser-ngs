<?php

namespace app\components\parser;

use SebastianBergmann\CodeCoverage\Node\Iterator;

/**
 * Interface Provider
 * @package app\components\parser
 */
interface Provider
{
    /**
     * @param array $params
     *
     * @return Iterator
     */
    public function getGenerator($params);
}
