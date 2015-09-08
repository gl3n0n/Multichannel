<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Points System' =>array('index'),
	$model->PointsId,
);

$this->menu=array(
	array('label'=>'List Points System',   'url'=>array('index')),
	array('label'=>'Create Points System', 'url'=>array('create')),
	array('label'=>'Update Points System', 'url'=>array('update', 'id'=>$model->PointsId)),
	array('label'=>'Delete Points System', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->PointsId),
	'confirm'=>'Are you sure you want to delete this item?')),	

	
);
?>
<h1>View Points System#<?php echo $model->PointsId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'PointsId',
		'Name',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value'=> $model->byCreateUsers!=null?($model->byCreateUsers->Username):(""),
		 ),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value'=> $model->byUpdateUsers!=null?($model->byUpdateUsers->Username):(""),
		 ),
		 
	),
)); ?>
