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
	array('label'=>'List of Campaigns Participated',   'url'=>array('campaignpart')),
	array('label'=>'List of Redemeed Rewards',         'url'=>array('redeemrewards')),
	array('label'=>'List of Redemeed Coupons',         'url'=>array('redeemcoupons')),
	array('label'=>'Customer Activity Report',         'url'=>array('customeractivity')),
	);
}
?>
<h1>Customer Points History</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/custhistory"),
	'method'=>'get',
)); ?>
	<fieldset>
	<!--//
		<legend>Search By Customer</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Customer" title="Search Customer">
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
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
		array(
		'name' => 'Customer ID',
		'value' => '$data["CustomerId"]',
		'type'  => 'raw',
		),		
		array(
		'name' => 'Customer Name',
		'value' => '$data["CustomerName"]',
		),	
		array(
		'name'  => 'Points System',
		'value' => 'CHtml::link($data["PointsSystemName"],
			    Yii::app()->createUrl("reportsList/ptslog/".$data["PointsId"]))',
		'type'  => 'raw',
		),		
		array(
		    'name'  => 'Client Name',
		    'type'  => 'raw',
		    'value'=> '$data["CompanyName"]',
		),
		array(
		    'name'  => 'Campaign Name',
		    'type'  => 'raw',
		    'value'=> '$data["CampaignName"]',
		),
		array(
		    'name'  => 'Brand Name',
		    'type'  => 'raw',
		    'value'=> '$data["BrandName"]',
		),
		array(
		    'name'  => 'Channel Name',
		    'type'  => 'raw',
		    'value'=> '$data["ChannelName"]',
		),
		array(
		    'name'  => 'Points',
		    'type'  => 'raw',
		    'value'=> '$data["TotalPoints"]',
		),
		
		array(
		    'name'  => 'Type',
		    'type'  => 'raw',
		    'value'=> '$data["LogType"]',
		),
		array(
		    'name'  => 'Date of Last Activity',
		    'type'  => 'raw',
		    'value'=> '$data["PointsSystemDate"]',
		),
	),
)); 




?>
