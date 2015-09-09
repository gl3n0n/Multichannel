<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */

$this->breadcrumbs=array(
	'Reward Details'=>array('index'),
	$model->Name=>array('view','id'=>$model->RewardConfigId),
	'Update',
);

$this->menu=array(
	array('label'=>'List RewardDetails', 'url'=>array('index')),
	array('label'=>'Create RewardDetails', 'url'=>array('create')),
	array('label'=>'View RewardDetails', 'url'=>array('view', 'id'=>$model->RewardConfigId)),
	array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>Update <?php echo $model->Name; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>