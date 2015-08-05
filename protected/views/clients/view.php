<?php
/* @var $this ClientsController */
/* @var $model Clients */

$this->breadcrumbs=array(
	'Clients'=>array('index'),
	$model->ClientId,
);

$this->menu=array(
	array('label'=>'List Clients', 'url'=>array('index')),
	// array('label'=>'Create Clients', 'url'=>array('create')),
	// array('label'=>'Update Clients', 'url'=>array('update', 'id'=>$model->ClientId)),
	// array('label'=>'Delete Clients', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->ClientId),'confirm'=>'Are you sure you want to delete this item?')),
	// array('label'=>'Manage Clients', 'url'=>array('admin')),
);
?>

<h1>View Clients #<?php echo $model->ClientId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'ClientId',
		'CompanyName',
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
