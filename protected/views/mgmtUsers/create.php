<?php
/* @var $this UsersController */

$this->breadcrumbs=array(
	'Users'=>array('/mgmtUsers'),
	'Create',
);

?>
<h1>Create User</h1>

<?php echo $this->renderPartial('_formCreate', 
	array('model'       => $model, 
		 'clientsModel' => $clientsModel,
		 'submodel'     => $clientsModel,
		 'client_list'  => $client_list,
		 )); ?>
