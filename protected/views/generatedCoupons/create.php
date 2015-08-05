<?php
/* @var $this GeneratedCouponsController */
/* @var $model GeneratedCoupons */

$this->breadcrumbs=array(
	'Generated Coupons'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List GeneratedCoupons', 'url'=>array('index')),
	array('label'=>'Manage GeneratedCoupons', 'url'=>array('admin')),
);
?>

<h1>Create GeneratedCoupons</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>