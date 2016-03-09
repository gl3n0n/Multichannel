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
<h1>Customer Participation <?=$whatMode?></h1>
<div>
<?php 
if(!empty($downloadCSV))
{

?>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("reportsList/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>

		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<?php
}//show download
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProviderChannel,
	'columns'=>array(
		array(
			'name' => 'Customer Id',
			'value' => '$data["CustomerId"]',
			),
		array(
			'name' => 'Customer Name',
			'value' => '$data["CustomerName"]',
			),
		array(
			'name' => 'Customer Email',
			'value' => '$data["Email"]',
			),
		array(
			'name' => 'Client Name',
			'value' => '$data["CompanyName"]',
			),
		array(
			'name'  => 'Brand Name',
			'value' => '$data["BrandName"]',
		),
		array(
			'name' => 'Campaign Name',
			'value' => '$data["CampaignName"]',
			),
		array(
			'name'  => 'Channel Name',
			'value' => '$data["ChannelName"]',
		),
		array(
			'name' => 'Points System Name',
			'value' => '$data["PointsName"]',
			),
			
		array(
		'name'  => 'Last Activity',
		'value' => '$data["DateCreated"]',
		),

	),	
)); ?>
