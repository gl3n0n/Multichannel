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
));

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');



$this->endWidget(); 
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'ChannelId',
	array(
	'name'  => 'ChannelName',
	'value' => 'CHtml::link($data->ChannelName,Yii::app()->createUrl("channels/update",array("id"=>$data->primaryKey)))',
	'type'  => 'raw',
	),	
	'Description',
	array(
	'name' => 'Client Name',
	'value' =>'$data->channelClients->CompanyName',
	),	
	array(
	'name' => 'Brand Name',
	'value'=> '$data->channelBrands->BrandName',
	),	
	array(
	'name'  => 'Campaign Name',
	'value' => '$data->channelCampaigns->CampaignName',
	),	
	'Type',
	array(
	'name' => 'Start Date',
	'value'=> '$data->DurationFrom',
	),	
	array(
	'name' => 'End Date',
	'value'=> '$data->DurationTo',
	),	
	'Status',
	'DateCreated',
	array(
	'name' => 'CreatedBy',
	'value'=> '$data->channelCreateUsers->Username',
	),	
	'DateUpdated',
	array(
	'name' => 'UpdatedBy',
	'value'=> '$data->channelUpdateUsers->Username',
	),	
	),
)); ?>
