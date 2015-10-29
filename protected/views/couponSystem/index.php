<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupon System',
);

$this->menu=array(
	array('label'=>'Create Coupon System', 'url'=>array('create')),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
		array('label'=>'Create Coupon System',  'url'=>array('create')),
		array('label'=>'Pending Coupon System', 'url'=>array('pending')),
	);
}
?>
<script>

//remove images not shown? its a browser issue though
$( document ).ready(function() {
	var images = document.getElementsByTagName("img");
	for (i = 0; i < images.length; i++) {
		var self  = images[i];
		self.onerror = function () {
			self.parentNode.removeChild(self);
		}
	}
});


</script>
<h1>Coupon System</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("couponSystem/index"),
	'method'=>'get',
)); 

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bycoupon-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bypointsystem-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bycoupontype-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');

$this->endWidget(); 
?>
</div>
<?php 

if(Yii::app()->user->AccessType === "SUPERADMIN")
{

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
	array(
		'name'  => 'CouponId',
		'value' => 'CHtml::link($data->CouponId,Yii::app()->createUrl("couponSystem/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	'CouponName',
	'Type',
	'TypeId',
	'ExpiryDate',
	'Status',
	array(
		'name'  => 'Points System Name',
		'value' => '(($data->byPoints != null)?($data->byPoints->Name):("") )',
	     ),
	'CouponType',
	array(
		'name'  => 'ClientId',
		'value' => '($data->byClients!=null)?($data->byClients->CompanyName):("")',
		),	
	array(
		'name' => 'Image',
		'type' => 'raw',
		'value'=> '( ($data->Image!=null)?(CHtml::link('.
			  'CHtml::image($data->Image,"",array("border" => "0px","width"=>"120px","height"=>"120px"))'.
			  ',$data->Image)):(""))',
	),
	'Quantity',
	'LimitPerUser',
	array(
		'name' => 'Operation',
		'type' => 'raw',
		'value'=> '($data->Status !== "PENDING")?
			(($data->edit_flag==1)?(CHtml::link("Update Approved",Yii::app()->createUrl("couponSystem/approveupdate/",array("uid"=>$data->primaryKey)))):
			((CHtml::link("View Generated",Yii::app()->createUrl("couponSystem/generatedview/",array("uid"=>$data->primaryKey)))))):
			(CHtml::link("Approve",Yii::app()->createUrl("couponSystem/approve/",array("uid"=>$data->primaryKey))))'
	), 
       ),
)); 

}
else
{


$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
	array(
		'name'  => 'CouponId',
		'value' => 'CHtml::link($data->CouponId,Yii::app()->createUrl("couponSystem/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	'CouponName',
	'Type',
	'TypeId',
	'ExpiryDate',
	'Status',
	array(
		'name'  => 'Points System Name',
		'value' => '(($data->byPoints != null)?($data->byPoints->Name):("") )',
	     ),
	'CouponType',
	array(
		'name'  => 'ClientId',
		'value' => '($data->byClients!=null)?($data->byClients->CompanyName):("")',
		),	
	array(
		'name' => 'Image',
		'type' => 'raw',
		'value'=> '( ($data->Image!=null)?(CHtml::link('.
			  'CHtml::image($data->Image,"",array("border" => "0px","width"=>"120px","height"=>"120px"))'.
			  ',$data->Image)):(""))',
	),
	'Quantity',
	'LimitPerUser',
	array(
		'name' => 'Operation',
		'type' => 'raw',
		'value'=> '(($data->Status !== "PENDING")?((CHtml::link("View Generated",Yii::app()->createUrl("couponSystem/generatedview/",array("uid"=>$data->primaryKey))))):
		(" "))'
	), 
       ),
)); 

}

?>

