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
	'CampaignId',
	//'ClientId',
	
	
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
	//'BrandId',
	array(
	'name' => 'Brand Name',
	'value'=> '$data->campaignBrands->BrandName',
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
	//'CreatedBy',
	array(
		'name' => 'CreatedBy',
		'value'=> '$data->campaignCreateUsers->Username',
	),	
	'DateUpdated',
	//'UpdatedBy',
	array(
	'name'  => 'UpdatedBy',
	'value' => '$data->campaignUpdateUsers->Username',
	),
     ),
)); 
?>
