<?php
/* @var $this GeneratedCouponsController */
/* @var $model GeneratedCoupons */

$this->breadcrumbs=array(
	'Generated Coupons'=>array('index'),
	$model->GeneratedCouponId=>array('view','id'=>$model->GeneratedCouponId),
	'Update',
);

$this->menu=array(
	array('label'=>'List GeneratedCoupons', 'url'=>array('index')),
	array('label'=>'Create GeneratedCoupons', 'url'=>array('create')),
	array('label'=>'View GeneratedCoupons', 'url'=>array('view', 'id'=>$model->GeneratedCouponId)),
	array('label'=>'Manage GeneratedCoupons', 'url'=>array('admin')),
);
?>

<h1>Update GeneratedCoupons <?php echo $model->GeneratedCouponId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>