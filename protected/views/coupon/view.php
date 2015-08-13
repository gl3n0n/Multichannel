<?php
/* @var $this CouponController */
/* @var $model Coupon */

$this->breadcrumbs=array(
	'Coupons'=>array('index'),
	$model->CouponId,
);

$this->menu=array(
	array('label'=>'List Coupon', 'url'=>array('index')),
	array('label'=>'Create Coupon', 'url'=>array('create')),
	array('label'=>'Update Coupon', 'url'=>array('update', 'id'=>$model->CouponId)),
	// array('label'=>'Delete Coupon', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->CouponId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Coupon', 'url'=>array('admin')),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu[] =	array('label'=>'Pending Coupons', 'url'=>array('pending'));
}

?>

<h1>View Coupon #<?php echo $model->CouponId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'CouponId',
		'Code',
		'Type',
		'TypeId',
		'Source',
		'ExpiryDate',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => (($model->couponCreateUsers != null)?($model->couponCreateUsers->Username):('') ),
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => (($model->couponUpdateUsers != null)?($model->couponUpdateUsers->Username):('') ),
			),
		array(
		'name' => 'Image',
		'type' => 'raw',
		'value'=> CHtml::image($model->Image,"",array("width"=>"120px") )
		),		
		'Quantity',
		'LimitPerUser',
		'File',
	),
)); ?>
