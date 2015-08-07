<?php
/* @var $this PointsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points',
);

$this->menu=array(
	array('label'=>'Create Points', 'url'=>array('create')),
	//array('label'=>'Manage Points', 'url'=>array('admin')),
);
?>

<h1>Points</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("points/index"),
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
	'PointsId',
	array(
		'name' => 'ClientId',
		'value' => '$data->pointClients->CompanyName',
		),
	array(
		'name' => 'BrandId',
		'value' => '$data->pointBrands->BrandName',
		),
	array(
		'name' => 'CampaignId',
		'value' => '$data->pointCampaigns->CampaignName',
		),
	array(
		'name' => 'ChannelId',
		'value' => '$data->pointChannels->ChannelName',
		),

	'From',
	'To',
	'Value',
	'PointAction',
	'PointsLimit',
	'PointCapping',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value' => '$data->pointCreateUsers->Username',
		),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value' => '$data->pointUpdateUsers->Username',
	),
	),
)); ?>
