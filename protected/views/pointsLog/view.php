<?php
/* @var $this PointsLogController */
/* @var $model PointsLog */

$this->breadcrumbs=array(
	'Points Logs'=>array('index'),
	$model->PointLogId,
);

$this->menu=array(
	// array('label'=>'List PointsLog', 'url'=>array('index')),
	array('label'=>'Add Customer Points', 'url'=>array('create', 'id'=>$model->PointLogId)),
	// array('label'=>'Update PointsLog', 'url'=>array('update', 'id'=>$model->PointLogId)),
	array('label'=>'Delete Customer Points', 'url'=>array('remove', 'id'=>$model->PointLogId)),
	// array('label'=>'Manage PointsLog', 'url'=>array('admin')),
);
?>

<h1>View PointsLog #<?php echo $model->PointLogId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'PointLogId',
		array(
			'name' => 'CustomerId',
			'value' => $model->pointlogCustomers->Email,
			),
		array(
			'name' => 'ClientId',
			'value' => $model->pointlogClients->CompanyName,
			),
		array(
			'name' => 'BrandId',
			'value' => $model->pointlogBrands->BrandName,
			),
		array(
			'name' => 'CampaignId',
			'value' => $model->pointlogCampaigns->CampaignName,
			),
		array(
			'name' => 'ChannelId',
			'value' => $model->pointlogChannels->ChannelName,
			),
			array(
			'name' => 'PointsId',
			'value' => $model->pointlogPoints->Value,
			),
		'DateCreated',
	),
)); ?>
