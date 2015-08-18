<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Scheduled Post' =>array('index'),
	$model->SchedId,
);

$this->menu=array(
	array('label'=>'List Scheduled Post',   'url'=>array('index')),
	array('label'=>'Create Scheduled Post', 'url'=>array('create')),
	array('label'=>'Update Scheduled Post', 'url'=>array('update', 'id'=>$model->SchedId)),
	array('label'=>'Delete Scheduled Post', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->SchedId),
	'confirm'=>'Are you sure you want to delete this item?')),	

	
);
?>

<h1>View Scheduled Post #<?php echo $model->SchedId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'SchedId',
		'Title',
		'Description',
		'EventDate',
		'EventType',
		'RepeatType',
		'AwardType',
		'PointsId',
		'CouponId',
		'RewardId',
		array(
		'name' => 'ClientId',
		'value' => $model->schedClients->CompanyName,
		),		
		array(
		'name' => 'BrandId',
		'value' => $model->schedBrands->BrandName,
		),
		array(
		'name' => 'CampaignId',
		'value' => $model->schedCampaigns->CampaignName,
		),
		array(
		'name' => 'ChannelId',
		'value' => $model->schedChannels->ChannelName,
		),
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->schedCreateUsers->Username,
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->schedUpdateUsers->Username,
			),
	),
)); ?>
