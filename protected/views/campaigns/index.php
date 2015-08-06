<?php
/* @var $this CampaignsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Campaigns',
);

$this->menu=array(
	array('label'=>'Create Campaigns', 'url'=>array('create')),
	// array('label'=>'Manage Campaigns', 'url'=>array('admin')),
);
?>

<h1>Campaigns</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("campaigns/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Campaign Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="CampaignName" title="Search Campaign Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'CampaignId',
	'ClientId',
	'BrandId',
	//'CampaignName',
	array(
	'name'  => 'CampaignName',
	'value' => 'CHtml::link($data->CampaignName,Yii::app()->createUrl("campaigns/update",array("id"=>$data->primaryKey)))',
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
