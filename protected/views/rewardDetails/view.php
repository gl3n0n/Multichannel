<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */

$this->breadcrumbs=array(
	'Reward Details'=>array('index'),
	$model->RewardConfigId,
);

$this->menu=array(
	array('label'=>'List RewardDetails', 'url'=>array('index')),
	array('label'=>'Create RewardDetails', 'url'=>array('create')),
	array('label'=>'Update RewardDetails', 'url'=>array('update', 'id'=>$model->RewardConfigId)),
	// array('label'=>'Delete RewardDetails', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->RewardConfigId),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>View RewardDetails #<?php echo $model->RewardConfigId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
	'RewardConfigId',
	array(
		'name' => 'RewardId',
		'value' => $model->rdetailRewardslists->Title,
		),
	array(
		'name' => 'ChannelId',
		'value' => $model->rdetailChannels->ChannelName,
		),
	'Inventory',
	'Limitations',
	'Value',
	'Availability',
	'Status',
	array(
		'name' => 'ClientId',
		'value'=> ($model->rdetailClients != null )?($model->rdetailClients->CompanyName):(""),
		),	
	'DateCreated',
	'CreatedBy',
	'DateUpdated',
	'UpdatedBy',
	),
)); ?>
