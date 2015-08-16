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
	array('label'=>'Customer Activity Report',         'url'=>array('customeractivity')),
	);
}
?>
<h1>Customer Activity</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/customeractivity"),
	'method'=>'get',
)); ?>
<!--//
	<fieldset>
		<legend>Search By Code</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Code" title="Search Code">
		<button type="submit">Search</button>
	</fieldset>
//-->	
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
			'name' => 'CustomerId',
			'type' => 'raw',
			'value'=> '$data["CustomerId"]',
		), 
		array(
			'name' => 'SubscriptionId',
			'type' => 'raw',
			'value'=> '$data["SubscriptionId"]',
		), 
		array(
			'name' => 'SubsriptionStatus',
			'type' => 'raw',
			'value'=> '$data["SubsriptionStatus"]',
		), 
		array(
		    'name'  => 'Balance',
		    'type'  => 'raw',
		    'value'=> '$data["Balance"]',
		),
		array(
		    'name'  => 'Used',
		    'type'  => 'raw',
		    'value'=> '$data["Used"]',
		),
		array(
		    'name'  => 'Total',
		    'type'  => 'raw',
		    'value'=> '$data["Total"]',
		),
		array(
		    'name'  => 'Points',
		    'type'  => 'raw',
		    'value'=> '$data["Points"]',
		),
	),
)); 




?>
