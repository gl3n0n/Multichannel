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

<h1>View <?php echo $model->Name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
	array(
		'name'  => 'CtpId',
		'value' => CHtml::link($model->CtpId,Yii::app()->createUrl("couponToPoints/update",array("id"=>$model->primaryKey))),
		'type'  => 'raw',
	),
	array(
	'name' => 'CouponId',
	'value'=> $model->byCoupon!=null?$model->byCoupon->CouponName:"",
	),	
	array(
	'name' => 'ClientId',
	'value'=> $model->byClients!=null?$model->byClients->CompanyName:"",
	),
	'Name',
	'Value',
	'StartDate',
	'EndDate',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> $model->byCreateUsers!=null?$model->byCreateUsers->Username:"",
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> $model->byUpdateUsers!=null?$model->byUpdateUsers->Username:"",
	),
     ),
)); ?>
