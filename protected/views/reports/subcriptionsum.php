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
<h1>Customer Points Logs</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/subcriptionsum"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search By Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Customer" title="Search Channel Name">
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
	'id' => 'gen-subscription-view',
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
		array(
			'name' => 'Point Log Id',
			'type' =>'raw',
			'value'=> '$data["PointLogId"]',
			),
		array(
			'name' => 'Customer Name',
			'type' => 'raw',
			'value'=> '$data["CompanyName"]',
		), 		
		array(
			'name' => 'Subscription',
			'type' => 'raw',
			'value'=> '$data["SubscriptionId"]',
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
		    'name'  => 'PointsId',
		    'type'  => 'raw',
		    'value'=> '$data["PointsId"]',
		),
		array(
		    'name'  => 'Points',
		    'type'  => 'raw',
		    'value'=> '$data["Points"]',
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
