<?php
/* @var $this PointsLogController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points Logs',
);

$this->menu=array(
	array('label'=>'Create PointsLog', 'url'=>array('create1')),
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
	array(
		'name'  => 'PointLogId',
		'value' => 'CHtml::link($data->PointLogId,Yii::app()->createUrl("pointsLog/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
		),	
	array(
		'name'  => 'CustomerId',
		'value' => 'CHtml::link($data->pointlogCustomers->Email,Yii::app()->createAbsoluteUrl("customers/$data->CustomerId"))',
		'type'  => 'raw',
		),	
	'SubscriptionId',
	array(
	'name'  => 'ClientId',
	'value' => '$data->pointlogClients->CompanyName',
	'type'  => 'raw',
	),
	array(
	'name'  => 'BrandId',
	'value' => '$data->pointlogBrands->BrandName',
	'type'  => 'raw',
	),
	array(
	'name'  => 'CampaignId',
	'value' => '$data->pointlogCampaigns->CampaignName',
	'type'  => 'raw',
	),
	array(
	'name'  => 'ChannelId',
	'value' => '$data->pointlogChannels->ChannelName',
	'type'  => 'raw',
	),
	'PointsId',
	array(
		'name' => 'CreatedBy',
		'value'=> '($data->pointlogCreateUsers != null)?($data->pointlogCreateUsers->Username):("")',
	),
    ),
)); ?>
