<?php
/* @var $this GeneratedCouponsController */
/* @var $model GeneratedCoupons */

$this->breadcrumbs=array(
	'Generated Coupons'=>array('index'),
	$model->GeneratedCouponId,
);

$this->menu=array(
	array('label'=>'List GeneratedCoupons', 'url'=>array('index')),
	array('label'=>'Create GeneratedCoupons', 'url'=>array('create')),
	array('label'=>'Update GeneratedCoupons', 'url'=>array('update', 'id'=>$model->GeneratedCouponId)),
	array('label'=>'Delete GeneratedCoupons', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->GeneratedCouponId),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage GeneratedCoupons', 'url'=>array('admin')),
);
?>

<h1>View GeneratedCoupons #<?php echo $model->GeneratedCouponId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'GeneratedCouponId',
		'CouponId',
		'CustomerId',
		'Status',
		'Code',
		'DateCreated',
		'CreatedBy',
		'DateUpdated',
		'UpdatedBy',
		'DateRedeemed',
		'CouponMappingId',
	),
)); ?>
