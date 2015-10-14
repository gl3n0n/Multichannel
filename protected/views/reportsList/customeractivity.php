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
	'action'=>Yii::app()->createUrl("reportsList/customeractivity"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search By Customer</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Customer" title="Search Customer">
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
			'name' => 'Subscription',
			'type' =>'raw',
			//'value'=> 'CHtml::link($data["SubscriptionId"], "../reportsList/subcriptionsum/?subscribid=".$data["SubscriptionId"])',
			'value' => 'CHtml::link($data["SubscriptionId"],Yii::app()->createUrl("reportsList/subcriptionsum",array("subscribid"=>$data["SubscriptionId"])))'
			),
		array(
			'name' => 'Customer ID',
			'type' => 'raw',
			'value'=> '$data["CustomerId"]',
		), 
		array(
			'name' => 'Customer Name',
			'type' => 'raw',
			'value'=> '$data["FirstName"] . " " .  $data["LastName"]',
		), 
		array(
			'name' => 'Client',
			'type' => 'raw',
			'value'=> '$data["CompanyName"]',
		), 		
		array(
			'name' => 'Brand',
			'type' => 'raw',
			'value'=> '$data["BrandName"]',
		), 		
		array(
			'name' => 'Campaign',
			'type' => 'raw',
			'value'=> '$data["CampaignName"]',
		), 		
		array(
			'name' => 'Channel',
			'type' => 'raw',
			'value'=> '$data["ChannelName"]',
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
		/**
		array(
		    'name'  => 'Points',
		    'type'  => 'raw',
		    'value'=> '$data["Points"]',
		),**/
	),
)); 




?>
