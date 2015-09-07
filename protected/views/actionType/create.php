<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Action Type' =>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Action Type', 'url'=>array('index')),
);
?>

<h1>Create Action Type</h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'pointslist'    =>$pointslist,
)); 

?>