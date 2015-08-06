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
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("channels/index"),
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
	'ChannelId',
	'ClientId',
	'BrandId',
	'CampaignId',
	//'ChannelName',
	array(
	'name'  => 'ChannelName',
	'value' => 'CHtml::link($data->ChannelName,Yii::app()->createUrl("channels/update",array("id"=>$data->primaryKey)))',
	'type'  => 'raw',
	),	

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
