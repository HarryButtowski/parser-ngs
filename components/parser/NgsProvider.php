<?php

namespace app\components\parser;

use DOMDocument;
use DOMElement;
use DOMNodeList;

/**
 * Class NgsProvider
 * @package app\components\parser
 */
class NgsProvider implements Provider
{
    const STATUS_DATA_ALREADY_EXIST = 'exist';
    const TIME_FOR_RUN_PARSE        = 20;

    /** @var string */
    private $_url = 'http://do.ngs.ru';

    /** @var  string */
    private $_nmaeSection;

    /** @var  string */
    private $_urlSection;

    /** @var  integer */
    private $_startTime;

    /**
     * @inheritdoc
     */
    public function getGenerator($params)
    {
        $this->_nmaeSection = isset($params['section']) ? $params['section'] : '';

        if ($urlOfSection = $this->getUrlOfSection($this->_url, $this->_nmaeSection)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $urlOfSection);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $html     = curl_exec($curl);
            $curlInfo = curl_getinfo($curl);

            if (isset($curlInfo['http_code']) && $curlInfo['http_code'] == 200) {
                $parsedUrl         = parse_url($curlInfo['url']);
                $this->_urlSection = sprintf('%s://%s', $parsedUrl['scheme'], $parsedUrl['host']);
                $doc               = new DOMDocument();

                if (@$doc->loadHTML($html)) {
                    $generator = $this->getGeneratorByItemsFromPage($doc);
                    foreach ($generator as $item) {
                        if ($response = yield $item) {
                            $generator->send($response);
                        }
                    }
                }
            }
        }

        return;
    }

    /**
     * @param string $urlPage
     * @param string $section
     * @param array  $urlPages
     *
     * @return string
     */
    private function getUrlOfSection($urlPage, $section, $urlPages = [])
    {
        $url         = '';
        $urlPages    = $urlPages ?: [$urlPage];     //страницы по которым нужно пройтись
        $newUrlPages = [];                          //адреса новых страниц на случай если не найдется раздел на текущей странице
        $section     = trim(strtolower($section));
        $doc         = new DOMDocument();

        foreach ($urlPages as $urlPage) {
            if (@$doc->loadHTMLFile($urlPage)) {
                /** @var DOMElement $li */
                foreach ($this->getSectionsFromPage($doc) as $li) {
                    if ($this->isSection($li)) {
                        if ($this->isCurrentSection($li, $section)) {
                            $url = $this->getUrlOfCurrentSection($li);

                            break;
                        }

                        $newUrlPages[] = $this->getUrlOfCurrentSection($li);
                    }
                }
            }

            if ($url) {
                break;
            }
        }

        if (!$url && $newUrlPages) {
            $url = $this->getUrlOfSection('', $section, $newUrlPages);
        }

        return $url;
    }

    /**
     * @param DOMDocument $doc
     *
     * @return DOMNodeList
     */
    private function getSectionsFromPage($doc)
    {
        return $doc->getElementsByTagName('li');
    }

    /**
     * @param DOMElement $li
     *
     * @return bool
     */
    private function isSection($li)
    {
        return ($li->getAttribute('class') == 'do-categories__item');
    }

    /**
     * @param DOMElement $li
     * @param string     $section
     *
     * @return bool
     */
    private function isCurrentSection($li, $section)
    {
        return (trim(strtolower($li->nodeValue)) == $section);
    }

    /**
     * @param DOMElement $li
     *
     * @return string
     */
    private function getUrlOfCurrentSection($li)
    {
        $url = '';

        /** @var DOMElement[] $links */
        if ($links = $li->getElementsByTagName('a')) {
            $url = $links[0]->getAttribute('href');

            $url = (strpos($url, 'http:/') === false) ? $this->_url . $url : ''; //если ссылка абсолютная, то значит ведет на сайт с другой структурой, под который нужно писать отдельный парсер
        }

        return $url;
    }

    /**
     * @param DOMDocument $doc
     */
    private function getGeneratorByItemsFromPage($doc)
    {
        $this->_startTime = $this->_startTime ?: time();

        /** @var DOMElement $li */
        foreach ($this->getPromotionsFromPage($doc) as $li) {
            if ($this->isNotTimeOut() && $this->isPromotion($li)) {
                $url       = $this->_urlSection . $li->getAttribute('data-advert-link');
                $promotion = new DOMDocument();

                if (@$promotion->loadHTMLFile($url)) {
                    if ($content = $this->getContentOfPromotion($promotion)) {
                        $id          = $this->getIdFromContentOfPromotion($content);
                        $title       = $this->getTitleFromContentOfPromotion($content);
                        $description = $this->getDescriptionFromContentOfPromotion($content);

                        $response = yield [
                            'id'          => $id,
                            'section'     => $this->_nmaeSection,
                            'title'       => $title,
                            'description' => $description,
                        ];

                        if ($response == static::STATUS_DATA_ALREADY_EXIST) {
                            break;
                        }
                    }
                }
            }
        }

        if ($this->isNotTimeOut()) {
            if ($nextPage = $this->getNextPageFromCurrentPage($doc)) {
                $generator = $this->getGeneratorByItemsFromPage($nextPage);

                foreach ($generator as $item) {
                    if ($response = yield $item) {
                        $generator->send($response);
                    }
                }
            }
        }

        return;
    }

    /**
     * @param DOMDocument $doc
     *
     * @return DOMNodeList
     */
    private function getPromotionsFromPage($doc)
    {
        return $doc->getElementsByTagName('li');
    }

    private function isNotTimeOut()
    {
        return ((time() - $this->_startTime) < static::TIME_FOR_RUN_PARSE);
    }

    /**
     * @param DOMElement $li
     *
     * @return bool
     */
    private function isPromotion($li)
    {
        return ($li->getAttribute('itemprop') == 'itemListElement');
    }

    /**
     * @param DOMDocument $promotion
     *
     * @return DOMElement|null
     */
    private function getContentOfPromotion($promotion)
    {
        return $this->getElementByNameAndAttribute($promotion, 'div', 'class', 'do-main');
    }

    /**
     * @param DOMElement|DOMDocument $doc
     * @param string                 $elementName
     * @param string                 $attributeName
     * @param string                 $attributeValue
     *
     * @return DOMElement|null
     */
    private function getElementByNameAndAttribute($doc, $elementName, $attributeName, $attributeValue)
    {
        $result = null;

        /** @var DOMElement $element */
        foreach ($doc->getElementsByTagName($elementName) as $element) {
            if (preg_match(sprintf('/(^|\s)%s(\s|$)/', $attributeValue), $element->getAttribute($attributeName))) {
                $result = $element;
            }
        }

        return $result;
    }

    /**
     * @param DOMElement $content
     *
     * @return string
     */
    private function getIdFromContentOfPromotion($content)
    {
        if ($h2 = $this->getElementByNameAndAttribute($content, 'h2', 'data-id', '.*')) {
            return $h2->getAttribute('data-id');
        }
    }

    /**
     * @param DOMElement $content
     *
     * @return string
     */
    private function getTitleFromContentOfPromotion($content)
    {
        if ($span = $this->getElementByNameAndAttribute($content, 'span', 'class', 'do-advert__title-text')) {
            return $span->nodeValue;
        }
    }

    /**
     * @param DOMElement $content
     *
     * @return string
     */
    private function getDescriptionFromContentOfPromotion($content)
    {
        if ($div = $this->getElementByNameAndAttribute($content, 'div', 'class', 'do-advert__desc')) {
            return $div->nodeValue;
        }
    }

    /**
     * @param DOMDocument $doc
     *
     * @return DOMDocument|null
     */
    private function getNextPageFromCurrentPage($doc)
    {
        $nextPage = null;

        if ($a = $this->getElementByNameAndAttribute($doc, 'a', 'data-role', 'show-next-adverts')) {
            $nextPage = new DOMDocument();
            $url      = $this->_urlSection . $a->getAttribute('href');
            @$nextPage->loadHTMLFile($url);
        }

        return $nextPage;
    }

    /**
     * @deprecated
     *
     * @param DOMDocument $doc
     *
     * @return array
     */
    private function getItemsFromPage($doc)
    {
        $items = [];

        $this->_startTime = $this->_startTime ?: time();

        /** @var DOMElement $li */
        foreach ($this->getPromotionsFromPage($doc) as $li) {
            if ($this->isPromotion($li)) {
                $url       = $this->_urlSection . $li->getAttribute('data-advert-link');
                $promotion = new DOMDocument();

                if ($this->isNotTimeOut() && @$promotion->loadHTMLFile($url)) {
                    if ($content = $this->getContentOfPromotion($promotion)) {
                        $id          = $this->getIdFromContentOfPromotion($content);
                        $title       = $this->getTitleFromContentOfPromotion($content);
                        $description = $this->getDescriptionFromContentOfPromotion($content);

                        $items[] = [
                            'id'          => $id,
                            'section'     => $this->_nmaeSection,
                            'title'       => $title,
                            'description' => $description,
                        ];
                    }
                }
            }
        }

        if ($this->isNotTimeOut()) {
            $nextPage = $this->getNextPageFromCurrentPage($doc);
            $items    = array_merge($items, $this->getItemsFromPage($nextPage));
        }

        return $items;
    }
}