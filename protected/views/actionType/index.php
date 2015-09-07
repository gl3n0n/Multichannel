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
)); ?>
	<fieldset>
		<legend>Search Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Name" title="Search Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
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
		'PointsAction',
		'PointsCapping',
		'PointsLimit',
		'StartDate',
		'EndDate',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value'=> '$data->byCreateUsers!=null?($data->byCreateUsers->Username):("")',
		 ),
       ),
)); 
?>

