<?php
/**
 * PHP Version 7.0
 *
 * @category Category
 * @package  Views
 * @author   Дмитрий Логачев <demonwork@yandex.ru>
 * @license  http://www.yiiframework.com/license/ License name
 * @link     http://www.toirus.ru
 */

use yii\helpers\Html;

/* @var $message string */

$this->title = Yii::t('app', 'Типы оборудования');
?>


    <h3><?php echo $message ?></h3>
    <br/>
<?php echo Html::a('Типы оборудования', '/equipment-type/index'); ?>