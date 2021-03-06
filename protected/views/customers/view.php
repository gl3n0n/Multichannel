<?php
/* @var $this CustomersController */
/* @var $model Customers */

$this->breadcrumbs=array(
	'Customers'=>array('index'),
	$model->CustomerId,
);

$this->menu=array(
	array('label'=>'Add/Deduct Points', 'url'=>array('addsub', 'id'=>$model->CustomerId)),
	//array('label'=>'Add/Deduct Points', 'url'=>array('/GeneratedCoupons/?customer_id=' . $model->CustomerId)),
	//array('label'=>'List Redeemed Coupons', 'url'=>array('/GeneratedCoupons/?customer_id=' . $model->CustomerId)),
	//array('label'=>'List Redeemed Rewards', 'url'=>array('index')),
	//array('label'=>'Create Customers', 'url'=>array('create')),
	//array('label'=>'Update Customers', 'url'=>array('update', 'id'=>$model->CustomerId)),
	//array('label'=>'Delete Customers', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->CustomerId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Customers', 'url'=>array('admin')),
);
?>

<h1>View <?php echo $model->FirstName . ' ' . $model->LastName;  ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'CustomerId',
		array(
			'name' => 'Customer Name',
			'type'=>'raw',
			'value'=> CHtml::link("View Subscriptions", Yii::app()->createUrl("customerSubscriptions/?customer_id=".$model->CustomerId)),
			),
		array(
			'name' => 'ClientId',
			'value' => $model->custClients!=null?$model->custClients->CompanyName:"",
		),			
		'FirstName',
		'MiddleName',
		'LastName',
		'Gender',
		'ContactNumber',
		'Address',
		'Email',
		'FBId',
		'TwitterHandle',
		array(
		'name' => 'Total Points',
		'type'=>'raw',
		'value'=> CHtml::link("$total (View Logs)", Yii::app()->createUrl("reportsList/customeractivity/?customer_id=".$model->CustomerId)),
		),
		'Status',

	),
)); ?>
