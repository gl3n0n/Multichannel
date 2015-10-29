<?php
/* @var $this RewardsListController */
/* @var $model RewardsList */

$this->breadcrumbs=array(
	'Rewards Lists'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List RewardsList', 'url'=>array('index')),
	array('label'=>'Manage RewardsList', 'url'=>array('admin')),
);
?>

<h1>Create RewardsList</h1>

<?php $this->renderPartial('_form', array('model'=>$model,'client_list'=>$client_list)); ?>
