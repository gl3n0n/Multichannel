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

<h1>View Audit Logs #<?php echo $model->AuditId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'AuditId',
		array(
		'name'  => 'LogDate',
		'value' => ($model->LogDate != null)?(substr($model->LogDate,0,10)):(""),
		'type'  => 'raw',
		),
		array(
		'name'  => 'LogTime',
		'value' => ($model->LogDate != null)?(substr($model->LogDate,11)):(""),
		'type'  => 'raw',
		),
		array(
			'name'  => 'UserId',
			'value' => (($model->byUsers!=null)?($model->byUsers->Username):("")),
			'type'  => 'raw',
		),
		array(
		'name'  => 'ClientId',
		'value' => ($model->byClients != null)?($model->byClients->CompanyName):(""),
		'type'  => 'raw',
		),		
		array(
		'name'  => 'Module',
		'value' => ($model->ModPage != null)?(nl2br($model->ModPage)):(""),
		'type'  => 'raw',
		),		
		array(
		'name'  => 'Action',
		'value' => ($model->ModAction != null)?(nl2br($model->ModAction)):(""),
		'type'  => 'raw',
		),		
		array(
			'name'  => 'ClientId',
			'value' => ($model->byClients != null)?($model->byClients->CompanyName):(""),
			'type'  => 'raw',
		),		
		'GetPost',
		'UserType',
		array(
		'name'  => 'UserAgent',
		'value' => ($model->UserAgent!= null)?(nl2br($model->UserAgent)):(""),
		'type'  => 'raw',
		),		
		'IPAddr',
		array(
			'name'  => 'UrlData',
			'value' => ($model->UrlData != null)?(nl2br($model->UrlData)):(""),
			'type'  => 'raw',
		),		
		array(
			'name'  => 'UrlQry',
			'value' => ($model->UrlQry != null)?(nl2br($model->UrlQry)):(""),
			'type'  => 'raw',
		),		
		array(
			'name'  => 'CreatedBy',
			'value' => ($model->byUsers != null)?($model->byUsers->Username):(""),
			'type'  => 'raw',
		),
	
	),
)); ?>
