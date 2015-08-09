<?php
/* @var $this PointsLogController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points Logs',
);

$this->menu=array(
	// array('label'=>'Create PointsLog', 'url'=>array('create/?id='.$model->PointsId)),
	// array('label'=>'Manage PointsLog', 'url'=>array('admin')),
);

?>

<h1>Points Logs</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("pointsLog/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'PointLogId',
 	array(
		'name' => 'PointsId',
		'value' => '$data->pointlogPoints->Value',
		),
   'SubscriptionId',
 	array(
		'name' => 'ClientId',
		'value' => '$data->pointlogClients->CompanyName',
		),
 	array(
		'name' => 'BrandId',
		'value' => '$data->pointlogBrands->BrandName',
		),
 	array(
		'name' => 'CampaignId',
		'value' => '$data->pointlogCampaigns->CampaignName',
		),
 	array(
		'name' => 'ChannelId',
		'value' => '$data->pointlogChannels->ChannelName',
		),
	'DateCreated',
   'CreatedBy',
	'DateUpdated',
	'UpdatedBy',
	),
)); ?>
