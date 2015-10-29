<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Action Type',
);

$this->menu=array(
	array('label'=>'Create Action Type', 'url'=>array('create')),
);
?>

<h1>Action Type</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("actionType/index"),
	'method'=>'get',
)); 

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');

$this->endWidget(); 
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
		array(
			'name'  => 'ActiontypeId',
			'value' => 'CHtml::link($data->ActiontypeId,Yii::app()->createUrl("actionType/view",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
		),
		'Name',
		array(
			'name' => 'PointsId',
			'value'=> '$data->byPoints!=null?($data->byPoints->Name):("")',
			),
		array(
			'name' => 'ClientId',
			'value'=> '$data->byClients!=null?($data->byClients->CompanyName):("")',
			),		
		'Value',
		'PointsCapping',
		'PointsLimit',
		'StartDate',
		'EndDate',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value'=> '$data->byCreateUsers!=null?($data->byCreateUsers->Username):("")',
		 ),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value'=> '$data->byUpdateUsers!=null?($data->byUpdateUsers->Username):("")',
		 ),
		 
       ),
)); 
?>

