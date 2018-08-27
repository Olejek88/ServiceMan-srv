<?php
namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use creocoder\nestedsets\NestedSetsBehavior;

class Tree extends \yii\db\ActiveRecord
{
        use \kartik\tree\models\TreeTrait {
            isDisabled as parentIsDisabled; // note the alias
	}
     
    /**
     * @var string the classname for the TreeQuery that implements the NestedSetQueryBehavior.
     * If not set this will default to `kartik	ree\models\TreeQuery`.
     */
    public static $treeQueryClass; // change if you need to set your own TreeQuery
     
    /**
     * @var bool whether to HTML encode the tree node names. Defaults to `true`.
     */
    public $encodeNodeNames = true;
     
    /**
     * @var bool whether to HTML purify the tree node icon content before saving.
     * Defaults to `true`.
     */
    public $purifyNodeIcons = true;
     
    /**
     * @var array activation errors for the node
     */
    public $nodeActivationErrors = [];
     
    /**
     * @var array node removal errors
     */
    public $nodeRemovalErrors = [];
     
    /**
     * @var bool attribute to cache the `active` state before a model update. Defaults to `true`.
     */
    public $activeOrig = true;
        
    /**
     * @inheritdoc
     */
    public static function tableName()
	{
         return 'tree';
        }        
}