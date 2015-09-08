<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Points System' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'Points System', 'url'=>array('index')),
);
?>

<h1>Create Points System</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'client_list'   =>$client_list,
)); 

?>