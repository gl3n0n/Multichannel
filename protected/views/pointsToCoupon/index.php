<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Convert Points to Coupon',
);

$this->menu=array(
	array('label'=>'Create Convert Points to Coupon', 'url'=>array('create')),
);
?>

<h1>Convert Points to Coupon</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("pointsToCoupon/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Title</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Title" title="Search Title">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	array(
		'name'  => 'PtcId',
		'value' => 'CHtml::link($data->PtcId,Yii::app()->createUrl("pointsToCoupon/update",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	array(
	'name' => 'ClientId',
	'value'=> '$data->p2couponClients!=null?$data->p2couponClients->CompanyName:""',
	),
	'Title',
	'PointsRequired',
	'CouponId',
	'CouponValue',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> '$data->p2couponCreateUsers!=null?$data->p2couponCreateUsers->Username:""',
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> '$data->p2couponUpdateUsers!=null?$data->p2couponUpdateUsers->Username:""',
	),
	),
)); 
?>

