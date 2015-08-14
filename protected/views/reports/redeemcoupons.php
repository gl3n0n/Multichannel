<?php
/* @var $this CustomerSubscriptionsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Reports',
);

//overwrite
if(1)
{
	$this->menu=array(
	array('label'=>'Breakdown of Points Gained',       'url'=>array('pointsgainbal')),
	array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
	array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
	array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
	);
}
?>
<h1>Redeemed Coupons</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/redeemcoupons"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search By Code</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Code" title="Search Code">
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
			'name' => 'Redeemed By',
			'type' => 'raw',
			'value'=> '$data["Email"]',
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
		    'name'  => 'Brand Name',
		    'type'  => 'raw',
		    'value'=> '$data["BrandName"]',
		),
		array(
		    'name'  => 'Brand Name',
		    'type'  => 'raw',
		    'value'=> '$data["BrandName"]',
		),
		array(
		    'name'  => 'Campaign Name',
		    'type'  => 'raw',
		    'value'=> '$data["CampaignName"]',
		),
		array(
		    'name'  => 'Channel Name',
		    'type'  => 'raw',
		    'value'=> '$data["ChannelName"]',
		),
		array(
		    'name'  => 'Date Redeemed',
		    'type'  => 'raw',
		    'value'=> '$data["DateRedeemed"]',
		),
	),
)); 




?>
