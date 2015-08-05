<?php
/* @var $this CouponController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupons',
);

$this->menu=array(
	array('label'=>'Create Coupon', 'url'=>array('create')),
	// array('label'=>'Manage Coupon', 'url'=>array('admin')),
);
?>

<h1>Coupons</h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		'CouponId',
		'Code',
		'Type',
		'TypeId',
		'Source',
		'ExpiryDate',
		'Status',
		'DateCreated',
		'CreatedBy',
		'UpdatedBy',
		'Image',
		'Quantity',
		'LimitPerUser',
		'File',
	),	
)); ?>
