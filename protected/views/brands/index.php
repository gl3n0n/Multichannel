<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Brands',
);

$this->menu=array(
	array('label'=>'Create Brands', 'url'=>array('create')),
	//array('label'=>'Manage Brands', 'url'=>array('admin')),
);
?>

<h1>Brands</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("brands/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Brand Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="BrandName" title="Search Brand">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	'BrandId',
	'ClientId',
	array(
		'name'  => 'BrandName',
		'value' => 'CHtml::link($data->BrandName,Yii::app()->createUrl("brands/update",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	'Description',
	'DurationFrom',
	'DurationTo',
	'Status',
	'DateCreated',
	'CreatedBy',
	'DateUpdated',
	'UpdatedBy',
	),
)); ?>

