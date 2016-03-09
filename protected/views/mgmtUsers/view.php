<?php
/* @var $this ChannelsController */
/* @var $model Channels */

$this->breadcrumbs=array(
	'Users' =>array('index'),
	$model->UserId,
);

$this->menu=array(
	array('label'=>'List Users',       'url'=>array('index')),
	array('label'=>'Create User',      'url'=>array('create')),
	array('label'=>'Update User',      'url'=>array('update',     'id'=>$model->UserId)),
	array('label'=>'Change Password',  'url'=>array('changepass', 'id'=>$model->UserId)),
);

?>
<h1>View <?php echo $model->Username; ?></h1>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'UserId',
		array(
			'name'  => 'ClientId',
			'value' => ($model->clientInfo!=null)?($model->clientInfo->CompanyName):(""),
			),
		'Username',
		'FirstName',
		'MiddleName',
		'LastName',
		'Email',
		'ContactNumber',
		'AccessType',
		'Status',
		array(
			'name' => 'Last Updated',
			'value' => $model->DateUpdated,
			),
	),
)); 
?>