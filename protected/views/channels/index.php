<?php
/* @var $this ChannelsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Channels',
);

$this->menu=array(
	array('label'=>'Create Channels', 'url'=>array('create')),
	// array('label'=>'Manage Channels', 'url'=>array('admin')),
);
?>

<h1>Channels</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'ChannelId',
	'ClientId',
	'BrandId',
	'CampaignId',
	'ChannelName',
	'Description',
	'DurationFrom',
	'DurationTo',
	'Type',
	'Status',
	'DateCreated',
	'CreatedBy',
	'UpdatedBy',
	),
)); ?>
