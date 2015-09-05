<?php
/* @var $this ReportsController */

$this->breadcrumbs=array(
	'Audit Logs',
);


//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'View Audit Logs', 'url'=>array('index')),
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
    $("#DIVFILTER").click(function(){
	$("#reportFilter").toggle();
    });
});
</script>
<h1>Audit Logs</h1>
<fieldset class='filterSrchBold'>
	<legend id='DIVFILTER'>
	<h3>Search Filter</h3>
	</legend>
	<br/>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("auditLogs/index"),
	'method'=>'get',
)); ?>
	
	<fieldset class='filterSrch'>
		<legend>
		  <em><h6>By User Name</h6></em>
		</legend>
		<input type="text" id='byUserName' 
		 style="width:200px;"
		 name="byUserName" id="byUserName" placeholder="Created By" 
		 title="Search User Name"
		 value="<?=Yii::app()->request->getParam('byUserName')?>"
		 />
		 <br/>
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>
		<em><h6>By IP Address</h6></em>
		</legend>
		<input type="text" id='byIPAddress' 
		 style="width:200px;"
		 name="byIPAddress" id="byIPAddress" placeholder="IP Address" title="Search IP Address"
		 value="<?=Yii::app()->request->getParam('byIPAddress')?>"
		 />
		 <br/>
	</fieldset>
	<fieldset class='filterSrch'>
		<legend>
		<em><h6>By Date Range</h6></em>
		</legend>

		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name'        => 'byDateFr',
			'attribute'   => 'byDateFr',
			'value'       => Yii::app()->request->getParam('byDateFr'),
			'htmlOptions' => array(
				'size'        => '15',// textField size
				'maxlength'   => '10',// textField maxlength
				'placeholder' => 'Date From',// textField maxlength
				
			),
			// additional javascript options for the date picker plugin
			'options'     => array(
			'showAnim'    => "slideDown",
			'changeMonth' => true,
			'numberOfMonths' => 1,
			'showOn'          => "button",
			'buttonImageOnly' => false,
			'dateFormat'      => "yy-mm-dd",
			'showButtonPanel' => true,
			)
		));	
		?>
		<br/>
		<?php
		$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'name'        => 'byDateTo',
			'attribute'   => 'byDateTo',
			'value'       => Yii::app()->request->getParam('byDateTo'),			
			'htmlOptions' => array(
			        'size'        => '15',// textField size
			        'maxlength'   => '10',// textField maxlength
			        'placeholder' => 'Date To',// textField maxlength
    			),
			// additional javascript options for the date picker plugin
			'options'     => array(
			'showAnim'    => "slideDown",
			'changeMonth' => true,
			'numberOfMonths' => 1,
			'showOn'          => "button",
			'buttonImageOnly' => false,
			'dateFormat'      => "yy-mm-dd",
			'showButtonPanel' => true,
			)
		));	
		?>
		 <br/>
	</fieldset>
	<fieldset class='filterSrch'>
		<br/>
		<button type="submit" id='btnSearch' style="width:200px;">
		Search
		</button>
		<br/>
		<br/>
	</fieldset>	
	
<?php $this->endWidget(); ?>
</div>
</div>
</fieldset>
<?php $this->widget('zii.widgets.grid.CGridView', array(
       'dataProvider'=>$dataProvider,
       'columns' => array(
		array(
		'name'  => 'AuditId',
		'value' => 'CHtml::link($data->AuditId,Yii::app()->createUrl("auditLogs/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
		),		
		array(
		'name'  => 'ClientId',
		'value' => '($data->byClients != null)?($data->byClients->CompanyName):("")',
		'type'  => 'raw',
		),		
		'GetPost',
		'UserType',
		'UserAgent',
		'IPAddr',
		array(
		'name'  => 'UrlData',
		'value' => '($data->UrlData != null)?(nl2br($data->UrlData)):("")',
		'type'  => 'raw',
		),		
		array(
		'name'  => 'UrlQry',
		'value' => '($data->UrlQry != null)?(nl2br($data->UrlQry)):("")',
		'type'  => 'raw',
		),		
        array(
		'name'  => 'CreatedBy',
		'value' => '($data->byUsers != null)?($data->byUsers->Username):("")',
		'type'  => 'raw',
	),
		'DateCreated',
    ),
)); ?>
