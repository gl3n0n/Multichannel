<?php
/* @var $this ClientsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Clients',
);

$this->menu=array(
	array('label'=>'Create Clients', 'url'=>array('create')),
	// array('label'=>'Manage Clients', 'url'=>array('admin')),
);
?>

<h1>Clients</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("clients/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Company Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="CompanyName" title="Search Company Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
			'ClientId',
			//'CompanyName',
			array(
			'name'  => 'CompanyName',
			'value' => 'CHtml::link($data->CompanyName,Yii::app()->createUrl("clients/update",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
			),
			'Address',
			'Email',
			'Landline',
			'Status',
			'DateCreated',
			'CreatedBy',
			'DateUpdated',
			'UpdatedBy',
	),
)); ?>
