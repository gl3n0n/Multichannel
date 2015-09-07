<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Action Type'=>array('index'),
	$model->ActiontypeId =>array('view','id'=>$model->ActiontypeId),
	'Update',
);

$this->menu=array(
	array('label'=>'List Action Type',   'url'=>array('index')),
	array('label'=>'Create Action Type', 'url'=>array('create')),
	array('label'=>'Update Action Type', 'url'=>array('update', 'id'=>$model->ActiontypeId)),
	array('label'=>'Delete Action Type', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->ActiontypeId),
	'confirm'=>'Are you sure you want to delete this item?')),	

	
);
?>

<h1>Update Action Type <?php echo $model->ActiontypeId; ?></h1>

<?php 
$this->renderPartial('_form', array(
	'model'	        =>$model, 
	'pointslist'    =>$pointslist,
)); 

?>