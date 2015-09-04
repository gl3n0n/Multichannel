<?php
/* @var $this RewardsListController */
/* @var $model RewardsList */

$this->breadcrumbs=array(
	'Rewards Lists'=>array('index'),
	$model->Title,
);

$this->menu=array(
	array('label'=>'List RewardsList', 'url'=>array('index')),
	array('label'=>'Create RewardsList', 'url'=>array('create')),
	array('label'=>'Update RewardsList', 'url'=>array('update', 'id'=>$model->RewardId)),
	//array('label'=>'Delete RewardsList', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->RewardId),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage RewardsList', 'url'=>array('admin')),
);
?>

<h1>View <?php echo $model->Title; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'RewardId',
		'Title',
		'Description',
		array(
			'name'=>'Image',
			'type'=>'raw',
			'value'=> CHtml::image($model->Image),
			),
		array(
			'name' => 'ClientId',
			'value' => $model->rewardClients!=null?$model->rewardClients->CompanyName:"",
			),			
		'Availability',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->rewardUpdateUsers!=null?$model->rewardUpdateUsers->Username:"",
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->rewardUpdateUsers!=null?$model->rewardUpdateUsers->Username:"",
			),
	),
)); ?>
