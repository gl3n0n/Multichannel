<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Points System Mapping'=>array('index'),
	$model->PointMappingId =>array('view','id'=>$model->PointMappingId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Points System Mapping',   'url'=>array('index')),
	array('label'=>'Create Points System Mapping', 'url'=>array('create')),
	//array('label'=>'Update Points System Mapping', 'url'=>array('update', 'id'=>$model->PointMappingId)),
	array('label'=>'Delete Points System Mapping', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->PointMappingId),
	'confirm'=>'Are you sure you want to delete this item?')),	
);
?>

<h1>Update Points System Mapping <?php echo $model->PointMappingId; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model,
)); 

?>