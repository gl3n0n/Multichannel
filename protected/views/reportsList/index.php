<?php
/* @var $this ReportsController */

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
<h1>Reports</h1>
<fieldset class='filterSrchBold'>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/index"),
	'method'=>'get',
)); 
include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
?>
	<fieldset class='filterSrch'>
		<legend>Brand Name</legend>
		<input type="text" id='byBrand' 
		 style="width:200px;"
		 name="byBrand" 
		 placeholder="BrandName" 
		 title="Search Brand Name" 
		 value="<?=Yii::app()->request->getParam('byBrand')?>"
		 />
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Campaign Name</legend>
		<input type="text" id='byCampaign' 
		 style="width:200px;"
		 name="byCampaign" 
		 id="list-search" 
		 placeholder="CampaignName" 
		 title="Search Campaign Name"
		 value="<?=Yii::app()->request->getParam('byCampaign')?>"
		 />
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Channel Name</legend>
		<input type="text" id='byChannel' 
		 style="width:200px;"
		 name="byChannel" 
		 placeholder="ChannelName" 
		 title="Search Channel Name"
		 value="<?=Yii::app()->request->getParam('byChannel')?>"
		 />
		<br/>
	</fieldset>
	<fieldset class='filterSrch'>
	<legend>Transaction Date (From)</legend>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
                'value'=>Yii::app()->request->getParam('byTranDateFr'),
				'id'   =>'byTranDateFr',
		        'name' =>'byTranDateFr',
				'options' => array(
					'showAnim' => "slideDown",
					'changeMonth' => true,
					'numberOfMonths' => 1,
					'showOn' => "button",
					'buttonImageOnly' => false,
					'dateFormat' => "yy-mm-dd",
					'showButtonPanel' => true      
       			),
				'htmlOptions'=>array(
						'style'=>'width:170px;',
					),				
           	));
       	?>
		<br/>
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Transaction Date (To)</legend>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
				'model'=>$model,
                'value'=>Yii::app()->request->getParam('byTranDateTo'),
				'id'   =>'byTranDateTo',
		        'name' =>'byTranDateTo',
				'options' => array(
					'showAnim' => "slideDown",
					'changeMonth' => true,
					'numberOfMonths' => 1,
					'showOn' => "button",
					'buttonImageOnly' => false,
					'dateFormat' => "yy-mm-dd",
					'showButtonPanel' => true      
       			),
				'htmlOptions'=>array(
						'style'=>'width:170px;',
					),				
				
           	));
       	?>
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
       'dataProvider'=>$dataProvider,
       'columns' => array(
	array(
		'name'  => 'CustomerId',
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
	array(
		'name'  => 'PointsName',
		'value' => 'CHtml::link($data["PointsName"],
			    Yii::app()->createUrl("reportsList/ptslog/".$data["PointsId"]))',
		'type'  => 'raw',
		),		
	array(
	'name'  => 'Client',
	'value' => '$data["CompanyName"]',
	'type'  => 'raw',
	),
	array(
	'name'  => 'Brand',
	'value' => '$data["BrandName"]',
	'type'  => 'raw',
	),
	array(
	'name'  => 'Campaign',
	'value' => '$data["CampaignName"]',
	'type'  => 'raw',
	),
	array(
	'name'  => 'Channel',
	'value' => '$data["ChannelName"]',
	'type'  => 'raw',
	),
	
	
	array(
	'name'  => 'Last Transaction',
	'value' => '$data["DateCreated"]',
	'type'  => 'raw',
	),
    ),
)); ?>
