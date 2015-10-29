<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Scheduled Event Post',
);

$this->menu=array(
	array('label'=>'List Scheduled Event Post',   'url'=>array('index')),
	array('label'=>'Create Scheduled Event Post', 'url'=>array('create')),
);
?>

<h1>Scheduled Event Post - Summary</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("schedEventPost/index"),
	'method'=>'get',
)); ?>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
	'CustomerId',
	'FirstName',
	'LastName',
	'Gender',
	'BirthDate',
	'AwardName',
	'AwardType',
	),
)); 
?>

