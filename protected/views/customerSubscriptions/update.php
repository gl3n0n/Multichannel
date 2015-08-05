<?php
/* @var $this CustomerSubscriptionsController */
/* @var $model CustomerSubscriptions */

$this->breadcrumbs=array(
	'Customer Subscriptions'=>array('index'),
	$model->SubscriptionId=>array('view','id'=>$model->SubscriptionId),
	'Update',
);

$this->menu=array(
	array('label'=>'List CustomerSubscriptions', 'url'=>array('index')),
	array('label'=>'Create CustomerSubscriptions', 'url'=>array('create')),
	array('label'=>'View CustomerSubscriptions', 'url'=>array('view', 'id'=>$model->SubscriptionId)),
	array('label'=>'Manage CustomerSubscriptions', 'url'=>array('admin')),
);
?>

<h1>Update CustomerSubscriptions <?php echo $model->SubscriptionId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>