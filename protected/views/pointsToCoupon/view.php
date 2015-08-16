<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Coupon on Points' =>array('index'),
	$model->PtcId,
);

$this->menu=array(
	array('label'=>'List Coupon on Points',   'url'=>array('index')),
	array('label'=>'Create Coupon on Points', 'url'=>array('create')),
	array('label'=>'Update Coupon on Points', 'url'=>array('update', 'id'=>$model->PtcId)),
);
?>

<h1>View Coupon on Points #<?php echo $model->PtcId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
	array(
		'name'  => 'PtcId',
		'value' => CHtml::link($model->PtcId,Yii::app()->createUrl("pointsToCoupon/update",array("id"=>$model->primaryKey))),
		'type'  => 'raw',
	),
	'Title',
	'PointsRequired',
	'CouponId',
	'CouponValue',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> $model->p2couponCreateUsers!=null?$model->p2couponCreateUsers->Username:"",
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> $model->p2couponUpdateUsers !=null?$model->p2couponUpdateUsers->Username:"",
	),
     ),
)); ?>
