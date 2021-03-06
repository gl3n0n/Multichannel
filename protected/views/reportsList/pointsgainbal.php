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



<h1>Breakdown of Points Gained</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/pointsgainbal"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<br/>
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
<br/>
<br/>




<?php
$sumall = 0;
$dataProvider->setPagination(false);
foreach($dataProvider->getData() as $kk )
{
	$sumall += @intval($kk["Total"]);
}
echo "<h2>Current Total Points: $sumall</h3>";
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		array(
			'name' => 'Customer',
			'value' => '$data["Email"]',
			),			
		array(
			'name'  => 'Points',
			'value' => '$data["Total"]',
		),	
		array(
			'name' => 'ClientId',
			'value' => '$data["CompanyName"]',
			),
		array(
			'name' => 'BrandId',
			'value' => '$data["BrandName"]',
			),
		array(
			'name'  => 'CampaignId',
			'value' => '$data["CampaignName"]',
		),			
		array(
			'name' => 'ChannelId',
			'value' => '$data["ChannelName"]',
			),
				
	),	
)); ?>
