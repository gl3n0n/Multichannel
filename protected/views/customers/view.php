<?php
/* @var $this CustomersController */
/* @var $model Customers */

$this->breadcrumbs=array(
	'Customers'=>array('index'),
	$model->CustomerId,
);

$this->menu=array(
	// array('label'=>'List Redeemed Coupons', 'url'=>array('/GeneratedCoupons/?customer_id=' . $model->CustomerId)),
	// array('label'=>'List Redeemed Rewards', 'url'=>array('index')),
	//array('label'=>'Create Customers', 'url'=>array('create')),
	//array('label'=>'Update Customers', 'url'=>array('update', 'id'=>$model->CustomerId)),
	//array('label'=>'Delete Customers', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->CustomerId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Customers', 'url'=>array('admin')),
);
?>

<h1>View Customers #<?php echo $model->CustomerId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'CustomerId',
		array(
			'name' => 'CustomerId',
			'type'=>'raw',
			'value'=> CHtml::link('View Subscriptions', '../customerSubscriptions/?customer_id=' . $model->CustomerId),
			),
		'FirstName',
		'MiddleName',
		'LastName',
		'Gender',
		'ContactNumber',
		'Address',
		'Email',
		'FBId',
		array(
		'name' => 'Total Points',
		'type'=>'raw',
		'value'=> CHtml::link("$total (View Logs)", '../reports/customeractivity/?customer_id=' . $model->CustomerId),
		),

	),
)); ?>
