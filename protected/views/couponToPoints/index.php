<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupon To Points',
);

$this->menu=array(
	array('label'=>'Create Coupon To Points', 'url'=>array('create')),
);
?>

<h1>Coupon To Points</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("couponToPoints/index"),
	'method'=>'get',
)); 



include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bycoupon-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bypointsystem-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');

$this->endWidget(); 
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	array(
		'name'  => 'CtpId',
		'value' => 'CHtml::link($data->CtpId,Yii::app()->createUrl("couponToPoints/update",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	array(
			'name' => 'Coupon To Points Name',
			'value' => '$data->Name',
			'type'  => 'raw',
	),	
	array(
			'name' => 'Coupon Name',
			'value' => 'CHtml::link($data->byCoupon!=null?$data->byCoupon->CouponName:"",Yii::app()->createUrl("couponSystem/view",array("id"=>$data->CouponId)))',
			'type'  => 'raw',
	),
	array(
	'name' => 'Points System Name',
	'value'=> '($data->byPoints!=null and @count($data->byPoints))?$data->byPoints[0]->Name:""',
	),	
	array(
	'name' => 'ClientId',
	'value'=> '$data->byClients!=null?$data->byClients->CompanyName:""',
	),
	'Value',
	'StartDate',
	'EndDate',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> '$data->byCreateUsers!=null?$data->byCreateUsers->Username:""',
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> '$data->byUpdateUsers!=null?$data->byUpdateUsers->Username:""',
	),
    ),
)); 
?>

