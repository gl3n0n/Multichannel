<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Action Type' =>array('index'),
	$model->ActiontypeId,
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

<h1>View Action Type#<?php echo $model->ActiontypeId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'ActiontypeId',
		'Name',
		array(
			'name' => 'PointsId',
			'value'=> $model->byPoints!=null?($model->byPoints->Name):(""),
			),
		array(
			'name' => 'ClientId',
			'value'=> $model->byClients!=null?($model->byClients->CompanyName):(""),
			),		
		'Value',
		'PointsAction',
		'PointsCapping',
		'PointsLimit',
		'StartDate',
		'EndDate',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value'=> $model->byCreateUsers!=null?($model->byCreateUsers->Username):(""),
		 ),
	),
)); ?>
