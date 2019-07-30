<?php

namespace common\components;


interface IPhoto
{
    /**
     * @return string
     */
    public static function getImageRoot();

    /**
     * @return string
     */
    public function getImagePath();

    /**
     * @return string
     */
    public function getImageUrl();
}