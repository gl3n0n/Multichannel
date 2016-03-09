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
		array('label'=>'List   Coupon System',  'url'=>array('index')),
		array('label'=>'Create Coupon System',  'url'=>array('create')),
//		array('label'=>'Pending Coupon System', 'url'=>array('pending')),
	);
}
?>

<h1>Generated Coupons <?=$couponName?></h1>


<script>
function downloadCSV(csvPath) 
{
	var iframe;
	iframe = document.getElementById("csvdownloader");
	if (iframe == null) {
		iframe = document.createElement('iframe');
		iframe.id = "csvdownloader";
		iframe.style.visibility = 'hidden';
		document.body.appendChild(iframe);
	}
	iframe.src = csvPath;
	return true;
}
$( document ).ready(function() {
    
	
	//$("#reportFilter").show();
    $("#DIVFILTER").click(function(){
	$("#reportFilter").toggle();
    });
});
</script>
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
if(!empty($downloadCSV))
{

?>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("tableQuery/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>
		<br/>
		<br/>
		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<?php
}//show download
?>

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
			'name' => 'Code',
			'type' => 'raw',
			'value'=> '$data["Code"]',
		), 
		array(
		    'name'  => 'Details',
		    'type'  => 'raw',
		    'value' => '$this->grid->etcButtonCoupon($data,$this->grid->etc["custList"])',
		),
		'CouponType',
		'PointEquivalent',
		'ExpiryDate',
		'Status',
		'DateRedeemed',
		/**
		array(
			'name'  => 'Coupon',
			'value' => 'CHtml::link($data["CouponName"],Yii::app()->createUrl("couponSystem/view",array("id"=>$data["CouponId"])))',
			'type'  => 'raw',
		),**/
	),
)); 




?>
