<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon To Points' =>array('index'),
	$model->CtpId,
);

$this->menu=array(
	array('label'=>'List Coupon To Points',   'url'=>array('index')),
	array('label'=>'Create Coupon To Points', 'url'=>array('create')),
	array('label'=>'Update Coupon To Points', 'url'=>array('update', 'id'=>$model->CtpId)),
);
?>

<h1>View Coupon To Points #<?php echo $model->CtpId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
	array(
		'name'  => 'CtpId',
		'value' => CHtml::link($model->CtpId,Yii::app()->createUrl("couponToPoints/update",array("id"=>$model->primaryKey))),
		'type'  => 'raw',
	),
	'Title',
	'CouponRequired',
	'CouponId',
	'PointsValue',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> $model->p2couponCreateUsers!=null?$model->p2couponCreateUsers->Username:"",
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> $model->p2couponUpdateUsers!=null?$model->p2couponUpdateUsers->Username:"",
	),
     ),
)); ?>
