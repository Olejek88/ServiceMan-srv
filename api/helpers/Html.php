<?php

namespace api\helpers;

/**
 * Class Html
 * @package api\helpers
 */
class Html extends \yii\helpers\Html
{
    /**
     * Удаляет html теги кроме $allowableTags
     * @param $html
     * @param string $allowableTags
     * @return string
     */
    public static function removeHtmlTags($html, $allowableTags = '<h2><strong><p><br><em>')
    {
        $html = str_replace('</div>', '</div><br />', $html);
        return strip_tags($html, $allowableTags);
    }

}