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

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
