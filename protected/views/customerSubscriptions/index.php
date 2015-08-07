<?php
/* @var $this CustomerSubscriptionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customer Subscriptions',
);

$this->menu=array(
	// array('label'=>'Create CustomerSubscriptions', 'url'=>array('create')),
	// array('label'=>'Manage CustomerSubscriptions', 'url'=>array('admin')),
);
?>

<h1>Customer Reports</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("customerSubscriptions/index"),
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
		'SubscriptionId',
		//'CustomerId',
		array(
		'name' => 'CustomerId',
		'value' => '$data->subsCustomers->FirstName',
		),
		array(
			'name' => 'ClientId',
			'value' => '$data->subsClients->CompanyName',
			),
		array(
			'name' => 'BrandId',
			'value' => '$data->subsBrands->BrandName',
			),
		array(
			'name' => 'CampaignId',
			'value' => '$data->subsCampaigns->CampaignName',
			),
		array(
			'name' => 'ChannelId',
			'value' => '$data->subsChannels->ChannelName',
			),
		'Status',
	),	
)); ?>
