<?php
/* @var $this ChannelsController */
/* @var $model Channels */

$this->breadcrumbs=array(
	'Channels'=>array('index'),
	$model->ChannelId,
);

$this->menu=array(
	array('label'=>'List Channels', 'url'=>array('index')),
	array('label'=>'Create Channels', 'url'=>array('create')),
	array('label'=>'Update Channels', 'url'=>array('update', 'id'=>$model->ChannelId)),
	// array('label'=>'Delete Channels', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->ChannelId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Channels', 'url'=>array('admin')),
);
?>

<h1>View <?php echo $model->ChannelName; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'ChannelId',
		array(
			'name' => 'ClientId',
			'value' => $model->channelClients->CompanyName,
			),
		array(
			'name' => 'BrandId',
			'value' => $model->channelBrands->BrandName,
			),
		array(
			'name' => 'CampaignId',
			'value' => $model->channelCampaigns->CampaignName,
			),
		'ChannelName',
		'Description',
		'DurationFrom',
		'DurationTo',
		'Type',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->channelCreateUsers->Username,
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->channelUpdateUsers->Username,
			),
	),
)); ?>
