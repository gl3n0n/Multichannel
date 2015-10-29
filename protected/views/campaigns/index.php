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
	//'ClientId',
	
	//'BrandId',
	array(
	'name' => 'Brand Name',
	'value'=> '$data->campaignBrands->BrandName',
	),
	//'CampaignName',
	array(
	'name'  => 'Campaign Name',
	'value' => 'CHtml::link($data->CampaignName,Yii::app()->createUrl("campaigns/update",array("id"=>$data->primaryKey)))',
	'type'  => 'raw',
	),	
	array(
	'name' => 'Campaign Description',
	'value'=> '$data->Description',
	),
	array(
	'name' => 'Client Name',
	'value'=> '$data->campaignClients->CompanyName',
	),
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
	//'CreatedBy',
	array(
		'name' => 'CreatedBy',
		'value'=> '$data->campaignCreateUsers->Username',
	),	
	//'UpdatedBy',
	array(
	'name'  => 'UpdatedBy',
	'value' => '$data->campaignUpdateUsers->Username',
	),
     ),
)); 
?>
