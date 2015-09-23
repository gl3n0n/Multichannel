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
)); ?>
	<fieldset>
		<legend>Search Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Name" title="Search Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); 
if(0)
{
	echo "<h>".@var_export($dataProvider->getData(),true);
	exit;
}
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
			'name'  => 'CouponId',
			'value' => 'CHtml::link($data->byCoupon!=null?$data->byCoupon->CouponName:"",Yii::app()->createUrl("couponSystem/view",array("id"=>$data->CouponId)))',
			'type'  => 'raw',
	),
	array(
	'name' => 'ClientId',
	'value'=> '$data->byClients!=null?$data->byClients->CompanyName:""',
	),
	'Name',
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

