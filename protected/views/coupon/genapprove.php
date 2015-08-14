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
		array('label'=>'Create Coupon',    'url'=>array('create')),
		array('label'=>'Pending Coupons',  'url'=>array('pending')),
		array('label'=>'Redeemed Coupons', 'url'=>array('redeemedview')),

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
	'action'=>Yii::app()->createUrl("coupon/genapprove"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
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
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
		array(
			'name' => 'Generated Coupon Id',
			'type' => 'raw',
			'value'=> '$data["GeneratedCouponId"]',
		), 
		array(
			'name' => 'Coupon Id',
			'type' => 'raw',
			'value'=> '$data["CouponId"]',
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
		/**
		array(
		    'name'  => 'QR-Code',
		    'type'  => 'raw',
		    'value' => '$this->grid->getQrCodeImage($data)',
		),**/

		
		
	),
)); 




?>
