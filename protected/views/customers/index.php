<?php
/* @var $this CustomersController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Customers',
);

$this->menu=array(
	//array('label'=>'Create Customers', 'url'=>array('create')),
	//array('label'=>'Manage Customers', 'url'=>array('admin')),
);

?>

<h1>Customers</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("customers/index"),
	'method'=>'get',
)); 

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bycustomer-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byemail-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-birthdaterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-birthdaterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-datecreated-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-datecreated-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');


$this->endWidget(); 
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
	array(
		'name' => 'CustomerId',
		'type' => 'raw',
		'value' => 'CHtml::link($data->CustomerId,Yii::app()->createUrl("customers/view",array("id"=>$data->primaryKey)))',
	),	
	// 'ClientId',
	'FirstName',
	'MiddleName',
	'LastName',
	'Gender',
	'ContactNumber',
	'Address',
	'Email',
	'BirthDate',
	'FBId',
	'TwitterHandle',
	array(
		'name' => 'Client Name',
		'value' => '$data->custClients!=null?$data->custClients->CompanyName:""',
	),
	array(
		'name' => 'Subscriptions',
		'type' => 'raw',
		'value'=> 'CHtml::link("View Subscriptions",Yii::app()->createUrl("customerSubscriptions/?customer_id=".$data->CustomerId))',
		),
	'Status',
	'DateCreated'
	),
	
)); ?>
