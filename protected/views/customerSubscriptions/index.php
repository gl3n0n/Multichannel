<?php
/* @var $this CustomerSubscriptionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customer Subscriptions',
);

$this->menu=array(
	array('label'=>'List Customers', 'url'=>array('/customers')),
	// array('label'=>'Manage CustomerSubscriptions', 'url'=>array('admin')),
);
?>

<h1>Customer Subscriptions</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("customerSubscriptions/index"),
	'method'=>'get',
));

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bybrand-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bychannel-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bypointsystem-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');


$this->endWidget(); ?>
</div>
<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		array(
			'name'  => 'Subscription Id',
			'value' => 'CHtml::link($data["SubscriptionId"],Yii::app()->createUrl("customerSubscriptions/view",array("id"=>$data["SubscriptionId"])))',
			'type'  => 'raw',
		),
		array(
		'name'  => 'Customer Name',
		'value' => '$data["CustomerName"]',
		),
		array(
			'name'  => 'Client Name',
			'value' => '$data["ClientName"]',
			),
		array(
			'name'  => 'Brand Name',
			'value' => '$data["BrandName"]',
			),
		array(
			'name'  => 'Campaign Name',
			'value' => '$data["CampaignName"]',
			),
		array(
			'name'  => 'Channel Name',
			'value' => '$data["ChannelName"]',
			),
		array(
			'name'  => 'Point System Name',
			'value' => '$data["PointsSystemName"]',
			),			
		'Status',
		array(
			'name' => 'Date Joined',
			'value' => '$data["DateCreated"]',
			),	
	),	
)); 


/**
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		array(
			'name'  => 'Subscription Id',
			'value' => 'CHtml::link($data->SubscriptionId,Yii::app()->createUrl("customerSubscriptions/view",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
		),
		array(
		'name' => 'Customer Name',
		'value' => '$data->subsCustomers->FirstName . $data->subsCustomers->LastName',
		),
		array(
			'name' => 'Client Name',
			'value' => '$data->subsClients->CompanyName',
			),
		array(
			'name' => 'Brand Name',
			'value' => '$data->subsBrands->BrandName',
			),
		array(
			'name' => 'Campaign Name',
			'value' => '$data->subsCampaigns->CampaignName',
			),
		array(
			'name' => 'Channel Name',
			'value' => '$data->subsChannels->ChannelName',
			),
		array(
			'name' => 'Point System Name',
			'value' => '($data->subsPoints!=null)?($data->subsPoints->Name):("")',
			),			
		'Status',
		array(
			'name' => 'Date Joined',
			'value' => '$data->DateCreated',
			),	
	),	
)); 
**/


?>
