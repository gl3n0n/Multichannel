<?php
/* @var $this RewardsListController */
/* @var $model RewardsList */

$this->breadcrumbs=array(
	'Rewards Lists'=>array('index'),
	$model->Title=>array('view','id'=>$model->RewardId),
	'Update',
);

$this->menu=array(
	array('label'=>'List RewardsList', 'url'=>array('index')),
	array('label'=>'Create RewardsList', 'url'=>array('create')),
	array('label'=>'View RewardsList', 'url'=>array('view', 'id'=>$model->RewardId)),
	//array('label'=>'Manage RewardsList', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->Title; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>