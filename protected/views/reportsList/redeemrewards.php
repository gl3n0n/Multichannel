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
//	array('label'=>'Breakdown of Points Gained',       'url'=>array('pointsgainbal')),
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
<h1>List of Redemeed Rewards</h1>
<fieldset class='filterSrchBold'>
	<legend id='DIVFILTER'>
	<h3>
	Search Filter(s)
	</h3>
	</legend>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("reportsList/redeemrewards"),
	'method'=>'get',
)); 

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
?>
	<fieldset class='filterSrch'>
		<legend>Point System Name</legend>
		<input type="text" id='byPointsName' 
		 style="width:200px;"
		 name="byPointsName" 
		 placeholder="PointsSystemName" 
		 title="Search Point System Name"
		 value="<?=Yii::app()->request->getParam('byPointsName')?>"
		 />
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Reward Details Name</legend>
		<input type="text" id='byRewardDetailsName' 
		 style="width:200px;"
		 name="byRewardDetailsName"  
		 placeholder="RewardDetailsName" 
		 title="Search Reward Details Name" 
		 value="<?=Yii::app()->request->getParam('byRewardDetailsName')?>"
		 />
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>Customer Name</legend>
		<input type="text" id='byCustomer' 
		 style="width:200px;"
		 name="byCustomer" 
		 placeholder="CustomerName" 
		 title="Search Customer Name"
		 value="<?=Yii::app()->request->getParam('byCustomer')?>"
		 />
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

<?php 
$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		array(
		'name' => 'Customer ID',
		'value' => '$data["CustomerId"]',
		),
		array(
		'name' => 'Customer Name',
		'value' => '$data["CustomerName"]',
		),	
		array(
		'name' => 'Customer',
		'value' => '$data["Email"]',
		),
		array(
		'name'  => 'Points System Name',
		'value' => 'CHtml::link($data["PointsSystemName"],
			    Yii::app()->createUrl("reportsList/ptslog/".$data["PointsId"]))',
		'type'  => 'raw',
		),		
			
		array(
		'name' => 'Reward Config Name',
		'value' => '$data["DetailsName"]',
		),
		array(
		'name' => 'Reward Detail ID',
		'value' => '$data["DetailsId"]',
		),
		array(
			'name' => 'Points Equiv',
			'value' => '$data["Pts"]',
			),
		array(
			'name' => 'Date Redeemed',
			'value' => '$data["DateRedeemed"]',
		),			
	),	
)); ?>
