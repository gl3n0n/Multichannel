<?php
/* @var $this CustomersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customers',
);

$this->menu=array(
	array('label'=>'Create Customers', 'url'=>array('create')),
	array('label'=>'Manage Customers', 'url'=>array('admin')),
);
?>

<h1>Customers</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("customers/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Customer Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="CustomerName" title="Search Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
	array(
		'name' => 'CustomerId',
		'type' => 'raw',
		'value' => 'CHtml::link($data->CustomerId,Yii::app()->createUrl("customers/update",array("id"=>$data->primaryKey)))',
	),	
	array(
		'name' => 'Subscriptions',
		'type' => 'raw',
		'value'=> 'CHtml::link("View Subscriptions","customerSubscriptions/?customer_id=$data->CustomerId")',
		),
	'FirstName',
	'MiddleName',
	'LastName',
	'Gender',
	'ContactNumber',
	'Address',
	'Email',
	'FBId'
	),
	
)); ?>
