<?php
/* @var $this CouponController */
/* @var $model Coupon */

$this->breadcrumbs=array(
	'Coupons'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Coupon', 'url'=>array('index')),
	// array('label'=>'Manage Coupon', 'url'=>array('admin')),
);
?>

<h1>Create Coupon</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'client_list'=>$client_list, 'brand_list'=>$brand_id, 'campaign_list'=>$campaign_id, 'channel_list'=>$channel_id)); ?>