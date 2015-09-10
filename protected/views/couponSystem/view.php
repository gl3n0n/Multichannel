<?php
/* @var $this CouponController */
/* @var $model Coupon */

$this->breadcrumbs=array(
	'Coupon System'=>array('index'),
	$model->CouponId,
);

$this->menu=array(
	array('label'=>'List Coupon System', 'url'=>array('index')),
	array('label'=>'Create Coupon System', 'url'=>array('create')),
	array('label'=>'Update Coupon System', 'url'=>array('update', 'id'=>$model->CouponId)),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu[] =	array('label'=>'Pending Coupon System', 'url'=>array('pending'));
}


?>

<h1>View Coupon System #<?php echo $model->CouponId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'CouponId',
		'CouponName',
		array(
			'name'  => 'PointsId',
			'value' => CHtml::link($model->PointsId,Yii::app()->createUrl("pointsSystem/view",array("id"=>$model->PointsId))),
			'type'  => 'raw',
		),
		'Type',
		'TypeId',
		'Source',
		'ExpiryDate',
		'CodeLength',
		'CouponType',
		'PointsValue',
		'Status',
		array(
		'name'  => 'ClientId',
		'value' => ($model->byClients!=null)?($model->byClients->CompanyName):(""),
		),	
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
		array(
		'name' => 'File',
		'type' => 'raw',
		'value'=> ($model->File != null)?(basename($model->File)):(""),
		),		
	),
)); ?>
