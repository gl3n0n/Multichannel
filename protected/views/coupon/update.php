<?php
/* @var $this CouponController */
/* @var $model Coupon */

$this->breadcrumbs=array(
	'Coupons'=>array('index'),
	$model->CouponId=>array('view','id'=>$model->CouponId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Coupon', 'url'=>array('index')),
	array('label'=>'Create Coupon', 'url'=>array('create')),
	array('label'=>'View Coupon', 'url'=>array('view', 'id'=>$model->CouponId)),
	// array('label'=>'Manage Coupon', 'url'=>array('admin')),
);
?>

<h1>Update Coupon <?php echo $model->CouponId; ?></h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>