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
			'name'  => 'ClientId',
			'value' => ($model->byClients != null)?($model->byClients->CompanyName):(""),
			'type'  => 'raw',
		),		
		'GetPost',
		'UserType',
		'UserAgent',
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
		'DateCreated',
	),
)); ?>
