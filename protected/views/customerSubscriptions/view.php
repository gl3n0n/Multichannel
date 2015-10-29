<?php
/* @var $this CustomerSubscriptionsController */
/* @var $model CustomerSubscriptions */

$this->breadcrumbs=array(
	'Customer Subscriptions'=>array('index'),
	$model->SubscriptionId,
);

$this->menu=array(
	array('label'=>'List CustomerSubscriptions', 'url'=>array('index')),
	//array('label'=>'Create CustomerSubscriptions', 'url'=>array('create')),
	//array('label'=>'Update CustomerSubscriptions', 'url'=>array('update', 'id'=>$model->SubscriptionId)),
	//array('label'=>'Delete CustomerSubscriptions', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->SubscriptionId),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage CustomerSubscriptions', 'url'=>array('admin')),
);
?>

<h1>View CustomerSubscriptions #<?php echo $model->SubscriptionId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'SubscriptionId',
		array(
		'name'  => 'CustomerId',
		'value' => sprintf("%s %s",$model->subsCustomers->LastName,$model->subsCustomers->FirstName),
		),
		array(
			'name' => 'ClientId',
			'value' => $model->subsClients->CompanyName,
			),
		array(
			'name' => 'BrandId',
			'value' => $model->subsBrands->BrandName,
			),
		array(
			'name' => 'CampaignId',
			'value' => $model->subsCampaigns->CampaignName,
			),
		array(
			'name' => 'Point System Name',
			'value' => ($model->subsPoints!=null)?($model->subsPoints->Name):(""),
			),			
			'Status',
	),
)); ?>
