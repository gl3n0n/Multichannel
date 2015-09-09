<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */

$this->breadcrumbs=array(
	'Reward Details'=>array('index'),
	$model->Name,
);

$this->menu=array(
	array('label'=>'List RewardDetails', 'url'=>array('index')),
	array('label'=>'Create RewardDetails', 'url'=>array('create')),
	array('label'=>'Update RewardDetails', 'url'=>array('update', 'id'=>$model->RewardConfigId)),
	// array('label'=>'Delete RewardDetails', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->RewardConfigId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>View <?php echo $model->Name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'RewardConfigId',
		'Name',
		array(
			'name' => 'RewardId',
			'value'=> $model->byRewards!=null?($model->byRewards->Title):(""),
			),
		array(
			'name' => 'PointsId',
			'value'=> $model->byPointsSystem!=null?($model->byPointsSystem->Name):(""),
			),
		array(
			'name' => 'ClientId',
			'value'=> $model->byClients!=null?($model->byClients->CompanyName):(""),
			),
		'Inventory',
		'Limitations',
		'Value',
		'StartDate',
		'EndDate',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value'=> $model->byCreateUsers!=null?($model->byCreateUsers->Username):(""),
		 ),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value'=> $model->byUpdateUsers!=null?($model->byUpdateUsers->Username):(""),
		 ),
	),
)); ?>
