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
<h1>List(s) of Customer Participation</h1>
<fieldset class='filterSrchBold'>
	<legend id='DIVFILTER'>
	<h3>
	Search Filter(s)
	</h3>
	<br/>
	</legend>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/campaignpart"),
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
</div>



<h5>Group By Client</h5>
<?php 
if(!empty($downloadCSV["CLIENT"]))
{
?>
<br/>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("reportsList/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV["CLIENT"]?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>

		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<br/>
<?php
}//show download
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProviderClient,
	'columns'=>array(
		array(
			'name' => 'Client Id',
			'value' => '$data["ClientId"]',
			),
		array(
			'name' => 'Client Name',
			'value' => '$data["CompanyName"]',
			),
		array(
			'name' => 'Points System Name',
			'value' => '$data["PointsName"]',
			),
		array(
			'name'  => 'Number of Participants',
			'value' => 'CHtml::link($data["participants"],
						Yii::app()->createUrl("reportsList/custparticipation1/", array("PointsId" => $data["PointsId"],"ClientId" => $data["ClientId"] ) ) )',
			'type'  => 'raw',
		),		
		array(
		'name'  => 'Last Activity',
		'value' => '$data["DateCreated"]',
		),

	),	
)); ?>
<h5>Group By Brand</h5>
<?php 
if(!empty($downloadCSV["BRAND"]))
{
?>
<br/>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("reportsList/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV["BRAND"]?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>

		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<br/>
<?php
}//show download
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProviderBrand,
	'columns'=>array(
		array(
			'name' => 'Brand Id',
			'value' => '$data["BrandId"]',
			),
		array(
			'name' => 'Brand Name',
			'value' => '$data["BrandName"]',
			),
		array(
			'name' => 'Client Name',
			'value' => '$data["CompanyName"]',
			),
		array(
			'name' => 'Points System Name',
			'value' => '$data["PointsName"]',
			),
		array(
			'name'  => 'Number of Participants',
			'value' => 'CHtml::link($data["participants"],
						Yii::app()->createUrl("reportsList/custparticipation2/", array("BrandId"=>$data["BrandId"],"PointsId" => $data["PointsId"],"ClientId" => $data["ClientId"]) ) )',
			'type'  => 'raw',
		),		
		array(
		'name'  => 'Last Activity',
		'value' => '$data["DateCreated"]',
		),

	),	
)); ?>

<h5>Group By Campaign</h5>
<?php 
if(!empty($downloadCSV["CAMPAIGN"]))
{
?>
<br/>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("reportsList/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV["CAMPAIGN"]?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>

		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<br/>
<?php
}//show download
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProviderCampaign,
	'columns'=>array(
		array(
			'name' => 'Campaign Id',
			'value' => '$data["CampaignId"]',
			),
		array(
			'name' => 'Campaign Name',
			'value' => '$data["CampaignName"]',
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
			'name' => 'Points System Name',
			'value' => '$data["PointsName"]',
			),
		array(
			'name'  => 'Number of Participants',
			'value' => 'CHtml::link($data["participants"],
						Yii::app()->createUrl("reportsList/custparticipation3/", array("CampaignId"=>$data["CampaignId"],"PointsId" => $data["PointsId"],"ClientId" => $data["ClientId"]) ) )',
			'type'  => 'raw',
		),		

		array(
		'name'  => 'Last Activity',
		'value' => '$data["DateCreated"]',
		),

	),	
)); ?>

<h5>Group By Channel</h5>
<?php 
if(!empty($downloadCSV["CHANNEL"]))
{
?>
<br/>
	<div>
	<fieldset class='filterSrch'>
	<?php $form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl("reportsList/index"),
		'method'=>'get',
	)); ?>
		<fieldset class='filterSrch'>
			<legend>CSV</legend>
			<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV["CHANNEL"]?>');">
			DOWNLOAD CSV 
			</a>
		</fieldset>

		<iframe id="csvdownloader" style="display:none"
		 width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	<?php $this->endWidget(); ?>
	</fieldset>
</div>
<br/>
<?php
}//show download
?>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProviderChannel,
	'columns'=>array(
		array(
			'name' => 'Channel Id',
			'value' => '$data["ChannelId"]',
			),
		array(
			'name' => 'Channel Name',
			'value' => '$data["ChannelName"]',
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
			'name' => 'Points System Name',
			'value' => '$data["PointsName"]',
			),
		array(
			'name'  => 'Number of Participants',
			'value' => 'CHtml::link($data["participants"],
						Yii::app()->createUrl("reportsList/custparticipation4/", array("ChannelId"=>$data["ChannelId"],"PointsId" => $data["PointsId"],"ClientId" => $data["ClientId"]) ) )',
			'type'  => 'raw',
		),		
		
		array(
		'name'  => 'Last Activity',
		'value' => '$data["DateCreated"]',
		),

	),	
)); ?>
