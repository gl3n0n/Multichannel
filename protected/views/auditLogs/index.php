<?php
/* @var $this ReportsController */

$this->breadcrumbs=array(
	'Audit Logs',
);


//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'View Logs', 'url'=>array('index')),
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
	</legend>
<div id='reportFilter'>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("auditLogs/index"),
	'method'=>'get',
)); ?>
	<fieldset class='filterSrch'>
		<legend>Search User Name</legend>
		<input type="text" id='byUserName' 
		 style="width:200px;"
		 name="byUserName" id="byUserName" placeholder="byUserName" title="Search User Name">
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
		'UrlQry',
        array(
		'name'  => 'CreatedBy',
		'value' => '($data->byUsers != null)?($data->byUsers->Username):("")',
		'type'  => 'raw',
	),
		'DateCreated',
    ),
)); ?>
