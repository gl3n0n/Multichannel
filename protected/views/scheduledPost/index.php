<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Scheduled Post',
);

$this->menu=array(
	array('label'=>'Create Scheduled Post', 'url'=>array('create')),
);
?>

<h1>Scheduled Post</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("scheduledPost/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Brand Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="BrandName" title="Search Brand">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	array(
		'name'  => 'SchedId',
		'value' => 'CHtml::link($data->SchedId,Yii::app()->createUrl("scheduledPost/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	'Title',
	'Description',
	'EventDate',
	'EventTime',
	'EventType',
	'RepeatType',
	'AwardType',
	array(
	'name' => 'CustomerId',
	'value'=> '$data->schedCustomers->Email',
	),
	array(
	'name' => 'ClientId',
	'value'=> '$data->schedClients->CompanyName',
	),
	array(
	'name' => 'BrandId',
	'value'=> '$data->schedBrands->BrandName',
	),
	array(
	'name' => 'CampaignId',
	'value'=> '$data->schedCampaigns->CampaignName',
	),
	array(
	'name' => 'ChannelId',
	'value'=> '$data->schedChannels->ChannelName',
	),
	//'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> '$data->schedCreateUsers->Username',
	),/**
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> '$data->schedUpdateUsers->Username',
	),**/
	),
)); 
?>

