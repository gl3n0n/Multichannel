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
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("coupon/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'CouponId',
	'Type',
	'TypeId',
	'Source',
	'ExpiryDate',
	'Status',
	'DateCreated',
	array(
		'name'  => 'CreatedBy',
		'value' => '$data->couponCreateUsers->Username',
		),
	'DateUpdated',
	array(
		'name'  => 'UpdatedBy',
		'value' => '($data->couponUpdateUsers!=null)?($data->couponUpdateUsers->Username):("")',
		),
	array(
		'name' => 'Image',
		'type' => 'raw',
		'value'=> 'CHtml::link('.
			  'CHtml::image($data->Image,"",array("width"=>"120px"))'.
			  ',$data->Image)',
	),
	'Quantity',
	'LimitPerUser',
	'File',
	),	
)); ?>
