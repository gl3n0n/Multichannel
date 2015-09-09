<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Points System'=>array('index'),
	$model->PointsId =>array('view','id'=>$model->PointsId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Points System',   'url'=>array('index')),
	array('label'=>'Create Points System', 'url'=>array('create')),
	array('label'=>'Update Points System', 'url'=>array('update', 'id'=>$model->PointsId)),
	/*
	array('label'=>'Delete Points System', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->PointsId),
	'confirm'=>'Are you sure you want to delete this item?')),	
	*/
	
);
?>

<h1>Update  <?php echo $model->Name; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'client_list'   =>$client_list,
)); 

?>