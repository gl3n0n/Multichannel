<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points System',
);

$this->menu=array(
	array('label'=>'Create Points System', 'url'=>array('create')),
);
?>

<h1>Points System</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("pointsSystem/index"),
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
			'name'  => 'PointsId',
			'value' => 'CHtml::link($data->PointsId,Yii::app()->createUrl("pointsSystem/view",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
		),
		'Name',
		array(
			'name' => 'ClientId',
			'value'=> '$data->byClients!=null?($data->byClients->CompanyName):("")',
			),		
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

