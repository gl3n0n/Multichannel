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

<h1>Customer Activity History</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/customeractivity"),
	'method'=>'get',
)); 


include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bycustomer-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-datecreated-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-datecreated-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');


$this->endWidget(); 
?>
<script>

    // Controll submit form event
    function generateCoupon(id)
    {
    	document.getElementById("mainFrm"+id).submit();
    }
</script>	
</div>
<div>
<?php 
if(!empty($downloadCSV))
{

?>
	<div>
	<br/>
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



<?php 
$this->widget('CGridViewEtc', array(
	'id' => 'gen-approve-view',
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
		array(
		'name' => 'Customer ID',
		'value' => 'CHtml::link($data["CustomerId"],
			    Yii::app()->createUrl("reportsList/custhistory/".$data["CustomerId"]))',
		'type'  => 'raw',
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
		'name' => 'Points System Name',
		'value' => '$data["PointsSystemName"]',
		),
		array(
		'name'  => 'Client Name',
		'value' => '$data["CompanyName"]',
		),
		array(
		'name'  => 'Brand Name',
		'value' => '$data["BrandName"]',
		),
		array(
		'name'  => 'Campaign Name',
		'value' => '$data["CampaignName"]',
		),
		array(
		'name'  => 'Channel Name',
		'value' => '$data["ChannelName"]',
		),
		array(
		'name'  => 'Points',
		'value' => '$data["PointsLogHist"]',
		),
		array(
		'name'  => 'Gain/Deduction Type',
		'value' => '$data["LogType"]',
		),
     	   	/**array(
		    'name'  => 'Balance',
		    'type'  => 'raw',
		    'value'=> '$data["Balance"]',
		),
		array(
		    'name'  => 'Last Activity',
		    'type'  => 'raw',
		    'value'=> '$data["PointsSystemDate"]',
		),**/
		array(
		'name'  => 'Date of Last Activity',
		'value' => '$data["LastActivity"]',
		),
	),
)); 




?>
