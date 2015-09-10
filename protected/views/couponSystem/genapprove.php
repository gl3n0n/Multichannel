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
		array('label'=>'Pending Coupon System', 'url'=>array('pending')),
	);
}
?>

<h1>Generated Coupons</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("coupon/generatedview"),
	'method'=>'get',
)); ?>
	<fieldset>
	<!--//
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
	//-->
	</fieldset>
<?php $this->endWidget(); ?>
<script>

    // Controll submit form event
    function generateCoupon(id)
    {
    	document.getElementById("mainFrm"+id).submit();
    }


</script>

</div>
<?php 
if(0)
{
	foreach($dataProvider->getData() as $row)
	{
	echo "<hr>";
	echo @var_export($row,true);
	}
	exit;
}

$this->widget('CGridViewEtc', array(
	'id' => 'gen-approve-view',
	'dataProvider'=>$dataProvider,
	'etc' => $mapping,
	'columns'=>array(
		array(
			'name' => 'Generated Coupon Id',
			'type' => 'raw',
			'value'=> '$data["GeneratedCouponId"]',
		), 
		array(
			'name'  => 'Coupon',
			'value' => 'CHtml::link($data["CouponName"],Yii::app()->createUrl("couponSystem/view",array("id"=>$data["CouponId"])))',
			'type'  => 'raw',
		),
		array(
			'name' => 'Code',
			'type' => 'raw',
			'value'=> '$data["Code"]',
		), 
		array(
		    'name'  => 'Details',
		    'type'  => 'raw',
		    'value' => '$this->grid->etcButtonCoupon($data,$this->grid->etc["custList"])',
		),
	),
)); 




?>
