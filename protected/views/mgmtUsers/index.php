<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'User Management',
);

//overwrite
$this->menu = array(
		array('label'=>'Create New User',       'url'=>array('create')),
		array('label'=>'List of Users',         'url'=>array('index')),
);

?>
<h1>User Management</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("mgmtUsers/index"),
	'method'=>'get',
));
include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bysaccesstype-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');
$this->endWidget(); 
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
	array(
		'name'  => 'UserID',
		'value' => 'CHtml::link($data["UserId"],Yii::app()->createUrl("mgmtUsers/view",array("id"=>$data["UserId"])))',
		'type'  => 'raw',
	),
	array(
	'name' => 'UserName',
	'value'=> '$data["Username"]',
	),	
	array(
	'name' => 'Firstname',
	'value'=> '$data["FirstName"]',
	),	
	array(
	'name' => 'Lastname',
	'value'=> '$data["LastName"]',
	),	
	array(
	'name' => 'Email',
	'value'=> '$data["Email"]',
	),	
	'AccessType',
	array(
	'name' => 'Client Name',
	'value'=> '$data["CompanyName"]',
	),
	array(
	'name' => 'Status',
	'value'=> '$data["Status"]',
	),
	array(
	'name' => 'DateCreated',
	'value'=> '$data["DateCreated"]',
	),
	array(
	'name' => 'CreatedBy',
	'value'=> '$data["CreatedBy2"]',
	),
	array(
	'name' => 'DateUpdated',
	'value'=> '$data["DateUpdated"]',
	),
	array(
	'name' => 'UpdatedBy',
	'value'=> '$data["UpdatedBy2"]',
	),
	),
)); 
?>
