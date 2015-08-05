<?php
/* @var $this CustomerSubscriptionsController */
/* @var $model CustomerSubscriptions */

$this->breadcrumbs=array(
	'Customer Subscriptions'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List CustomerSubscriptions', 'url'=>array('index')),
	array('label'=>'Manage CustomerSubscriptions', 'url'=>array('admin')),
);
?>

<h1>Create CustomerSubscriptions</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>