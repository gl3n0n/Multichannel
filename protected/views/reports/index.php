<?php
/* @var $this ReportsController */

$this->breadcrumbs=array(
	'Reports',
);

?>
<h1>Reports</h1>

<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/index"),
	'method'=>'get',
)); ?>
	<fieldset style="padding: 0.2em 0.5em;border:1px solid #aaaaaa;color:blue;font-size:90%;text-align:left;height:40px;">
		<legend>Search Customer Name / Email</legend>
		<input type="text" id='byCustomerName' name="byCustomerName" id="list-search" placeholder="CustomerName" title="Search Customer Name/Email">
		<button type="submit" id='btnByCustomerName'>Search</button>
		<br/>
	</fieldset>
	
<?php $this->endWidget(); ?>
</div>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/index"),
	'method'=>'get',
)); ?>
	<fieldset style="padding: 0.2em 0.5em;border:1px solid #aaaaaa;color:blue;font-size:90%;text-align:left;height:40px;">
		<legend>Search Brand Name</legend>
		<input type="text" id='byBrand' name="byBrand" id="list-search" placeholder="BrandName" title="Search Brand Name">
		<button type="submit" id='btnByBrandName'>Search</button>
		<br/>
	</fieldset>
	
<?php $this->endWidget(); ?>
</div>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/index"),
	'method'=>'get',
)); ?>
	<fieldset style="padding: 0.2em 0.5em;border:1px solid #aaaaaa;color:blue;font-size:90%;text-align:left;height:40px;">
		<legend>Search Campaign Name</legend>
		<input type="text" id='byCampaign' name="byCampaign" id="list-search" placeholder="CampaignName" title="Search Campaign Name">
		<button type="submit" id='btnByCampaignName'>Search</button>
		<br/>
	</fieldset>
	
<?php $this->endWidget(); ?>
</div>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/index"),
	'method'=>'get',
)); ?>
	<fieldset style="padding: 0.2em 0.5em;border:1px solid #aaaaaa;color:blue;font-size:90%;text-align:left;height:40px;">
		<legend>Search Channel Name</legend>
		<input type="text" id='byChannel' name="byChannel" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit" id='btnByChannel'>Search</button>
		<br/>
	</fieldset>
	
<?php $this->endWidget(); ?>
</div>
<?php 
if(!empty($downloadCSV))
{


?>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reports/index"),
	'method'=>'get',
)); ?>
	<fieldset style="padding: 0.2em 0.5em;border:1px solid #aaaaaa;color:blue;font-size:90%;text-align:left;height:40px;">
		<legend>CSV</legend>
		<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reports/csv")?>/?fn=<?php echo $downloadCSV?>');">
		DOWNLOAD CSV 
		</a>
	</fieldset>
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
	</script>
	<iframe id="csvdownloader" style="display:none"
	 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
<?php $this->endWidget(); ?>
</div>
<?php
}//show download
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
       'dataProvider'=>$dataProvider,
       'columns' => array(
	array(
		'name'  => 'CustomerId',
		'value' => '$data->pointlogCustomers->Email',
		'type'  => 'raw',
		),	
	'SubscriptionId',
	array(
	'name'  => 'ClientId',
	'value' => '$data->pointlogClients->CompanyName',
	'type'  => 'raw',
	),
	array(
	'name'  => 'BrandId',
	'value' => '$data->pointlogBrands->BrandName',
	'type'  => 'raw',
	),
	array(
	'name'  => 'CampaignId',
	'value' => '$data->pointlogCampaigns->CampaignName',
	'type'  => 'raw',
	),
	array(
	'name'  => 'ChannelId',
	'value' => '$data->pointlogChannels->ChannelName',
	'type'  => 'raw',
	),
	'PointsId',
	array(
		'name' => 'CreatedBy',
		'value'=> '($data->pointlogCreateUsers != null)?($data->pointlogCreateUsers->Username):("")',
	),
    ),
)); ?>
