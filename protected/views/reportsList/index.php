<?php
/* @var $this ReportsController */

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
<h1>Reports</h1>
<fieldset class='filterSrchBold'>
	<legend id='DIVFILTER'>
	<h3>
	Search Filter(s)
	</h3>
	</legend>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/index"),
	'method'=>'get',
)); ?>
	<fieldset class='filterSrch'>
		<legend>Search Customer Name / Email</legend>
		<input type="text" id='byCustomerName' 
		 style="width:200px;"
		 name="byCustomerName" 
		 id="list-search" 
		 placeholder="CustomerName" 
		 title="Search Customer Name/Email"
		 value="<?=Yii::app()->request->getParam('byCustomerName')?>"
		 />
		<!--
		<button type="submit" id='btnByCustomerName'>Search</button>
		//-->
		<br/>
	</fieldset>
<!--//	
<?php $this->endWidget(); ?>
</div>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/index"),
	'method'=>'get',
)); ?>
//-->
	<fieldset class='filterSrch'>
		<legend>Search Brand Name</legend>
		<input type="text" id='byBrand' 
		 style="width:200px;"
		 name="byBrand" id="list-search" 
		 placeholder="BrandName" 
		 title="Search Brand Name" 
		 value="<?=Yii::app()->request->getParam('byBrand')?>"
		 />
		<!--
		<button type="submit" id='btnByBrandName'>Search</button>
		//-->
		<br/>
	</fieldset>
<!--//	
<?php $this->endWidget(); ?>
</div>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/index"),
	'method'=>'get',
)); ?>
//-->
	<fieldset class='filterSrch'>
		<legend>Search Campaign Name</legend>
		<input type="text" id='byCampaign' 
		 style="width:200px;"
		 name="byCampaign" 
		 id="list-search" 
		 placeholder="CampaignName" 
		 title="Search Campaign Name"
		 value="<?=Yii::app()->request->getParam('byCampaign')?>"
		 />
		<!--
		<button type="submit" id='btnByCampaignName'>Search</button>
		//-->
		<br/>
	</fieldset>

<!--//	
<?php $this->endWidget(); ?>
</div>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/index"),
	'method'=>'get',
)); ?>
//-->
	<fieldset class='filterSrch'>
		<legend>Search Channel Name</legend>
		<input type="text" id='byChannel' 
		 style="width:200px;"
		 name="byChannel" id="list-search" 
		 placeholder="ChannelName" 
		 title="Search Channel Name"
		 value="<?=Yii::app()->request->getParam('byChannel')?>"
		 />
		<!--
		<button type="submit" id='btnByChannel'>Search</button>
		//-->
		<br/>
	</fieldset>
	<fieldset class='filterSrch'>
		<br/>
		<button type="submit" id='btnByChannel' style="width:200px;">
		Search
		</button>
		<br/>
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
</div>
<?php
}//show download
?>

</div>
</fieldset>
<?php $this->widget('zii.widgets.grid.CGridView', array(
       'dataProvider'=>$dataProvider,
       'columns' => array(
	array(
		'name'  => 'Customer Id',
		'value' => '$data["CustomerId"]',
		'type'  => 'raw',
		),       
	array(
		'name'  => 'CustomerNm',
		'value' => '$data["FirstName"] ." " .$data["LastName"]',
		'type'  => 'raw',
		),	
	array(
		'name'  => 'EmailAdd',
		'value' => 'CHtml::link($data["Email"],
			    Yii::app()->createUrl("customers/".$data["CustomerId"]))',
		'type'  => 'raw',
		),	
	'SubscriptionId',
	array(
	'name'  => 'ClientId',
	'value' => '$data["CompanyName"]',
	'type'  => 'raw',
	),
	array(
	'name'  => 'BrandId',
	'value' => '$data["BrandName"]',
	'type'  => 'raw',
	),
	array(
	'name'  => 'CampaignId',
	'value' => '$data["CampaignName"]',
	'type'  => 'raw',
	),
	array(
	'name'  => 'ChannelId',
	'value' => '$data["ChannelName"]',
	'type'  => 'raw',
	),
	'PointsId',
	'Value',
	'DateCreated',
    ),
)); ?>
