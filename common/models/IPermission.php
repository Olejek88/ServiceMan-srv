<?php


namespace common\models;


interface IPermission
{
    /**
     * @return array
     */
    function getPermissions();

    /**
     * @return array
     */
    function getActionPermissions();
}