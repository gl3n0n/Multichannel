<?php
/* @var $this RewardsListController */
/* @var $model RewardsList */

// $this->breadcrumbs=array(
//  'Rewards Lists'=>array('index'),
//  'Create',
// );

$this->menu=array(
    array('label'=>'List RewardsList', 'url'=>array('index')),
    array('label'=>'Manage RewardsList', 'url'=>array('admin')),
);
?>

<h1>Create</h1>

<?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
?>

<?php echo $_form; ?>