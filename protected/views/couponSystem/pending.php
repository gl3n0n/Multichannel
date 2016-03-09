<?php
/* @var $this CouponController */
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
		//array('label'=>'Pending Coupon System', 'url'=>array('pending')),
		);
}
?>

<h1>Pending Coupon System</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("couponSystem/pending"),
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
	'dataProvider'=> $dataProvider,
	'columns'=>array(
		array(
			'name'  => 'CouponId',
			'value' => 'CHtml::link($data->CouponId,Yii::app()->createUrl("couponSystem/view",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
		),
	'CouponName',
	array(
		'name'  => 'Points System Name',
		'value' => '(($data->byPoints != null)?($data->byPoints->Name):("") )',
	     ),	
	array(
		'name'  => 'ClientId',
		'value' => '($data->byClients!=null)?($data->byClients->CompanyName):("")',
		),	
	'CouponType',
	'Source',
	'Type',
	'PointsValue',
	'Quantity',
	'LimitPerUser',
	'ExpiryDate',
	'Status',
    /**    array(
                'name' => 'Image',
                'type' => 'raw',
                'value'=> '( ($data->Image!=null)?(CHtml::link('.
                          'CHtml::image($data->Image,"",array("border" => "0px","width"=>"120px","height"=>"120px"))'.
                          ',$data->Image)):(""))',
        ),
	'Quantity',
	'LimitPerUser',**/
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
