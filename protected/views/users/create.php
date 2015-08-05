<?php
/* @var $this UsersController */

$this->breadcrumbs=array(
	'Users'=>array('/users'),
	'Create',
);

?>
<h1>Create User</h1>

<?php echo $this->renderPartial('_formCreate', array('model'=>$model, 'submodel'=>$clientsModel)); ?>
