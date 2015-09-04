<?php
/* @var $this CouponController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupons',
);

$this->menu=array(
	array('label'=>'Create Coupon', 'url'=>array('create')),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'Create Coupon',   'url'=>array('create')),
	array('label'=>'Pending Coupons', 'url'=>array('pending')),
	);
}
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
	array(
	'name' => 'CouponId',
	'type' => 'raw',
	'value'=> 'CHtml::link($data->CouponId,Yii::app()->createUrl("coupon/view",array("id"=>$data->primaryKey)))',
	), 
	'Type',
	'TypeId',
	'Source',
	'ExpiryDate',
	'Status',
	array(
		'name'  => 'ClientId',
		'value' => '($data->couponClients!=null)?((@count($data->couponClients)>0)?($data->couponClients[0]->CompanyName):("")):("")',
		),	
	/*'DateCreated',
	array(
		'name'  => 'CreatedBy',
		'value' => '$data->couponCreateUsers->Username',
		),
	'DateUpdated',
	array(
		'name'  => 'UpdatedBy',
		'value' => '($data->couponUpdateUsers!=null)?($data->couponUpdateUsers->Username):("")',
		),**/
	array(
		'name' => 'Image',
		'type' => 'raw',
		'value'=> 'CHtml::link('.
			  'CHtml::image($data->Image,"",array("width"=>"120px"))'.
			  ',$data->Image)',
	),
	'Quantity',
	'LimitPerUser',
	//'File',
	),	
)); ?>
