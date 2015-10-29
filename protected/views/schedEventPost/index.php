<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Scheduled Event Post',
);

$this->menu=array(
	array('label'=>'Create Scheduled Event Post', 'url'=>array('create')),
);
?>

<h1>Scheduled Event Post</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("schedEventPost/index"),
	'method'=>'get',
));




include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bypointsystem-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');

$this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
	array(
		'name'  => 'SchedId',
		'value' => 'CHtml::link($data->SchedId,Yii::app()->createUrl("schedEventPost/view",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	array(
	'name' => 'Scheduled Event Name',
	'value'=> 'trim($data->Title)',
	),	
	array(
	'name' => 'Scheduled Event Description',
	'value'=> 'trim($data->Description)',
	),	
	array(
		'name' => 'Points System Name',
		'value'=> '$data->sPoint!=null?($data->sPoint->Name):("")',
	),	
	array(
	'name' => 'Client Name',
	'value'=> '($data->sClients!=null)?($data->sClients->CompanyName):("")',
	),
	array(
		'name' => 'Grouping Type',
		'value'=> '$data->AwardName',
	),	
	'Value',
	'AwardType',
	array(
	'name' => 'StartDate',
	'value'=> 'substr($data->StartDate,0,10)',
	),
	array(
	'name' => 'EndDate',
	'value'=> 'substr($data->EndDate,0,10)',
	),
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> '($data->sCreateUsers!=null)?($data->sCreateUsers->Username):()',
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> '($data->sUpdateUsers!=null)?($data->sUpdateUsers->Username):()',
	),
	
	),
)); 
?>

