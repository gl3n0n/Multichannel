<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Scheduled Event Post' =>array('index'),
	$model->SchedId,
);

$this->menu=array(
	array('label'=>'List Scheduled Event Post',   'url'=>array('index')),
	array('label'=>'Create Scheduled Event Post', 'url'=>array('create')),
	array('label'=>'Update Scheduled Event Post', 'url'=>array('update', 'id'=>$model->SchedId)),
	array('label'=>'Delete Scheduled Event Post', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->SchedId),
	'confirm'=>'Are you sure you want to delete this item?')),	

	
);
?>

<h1>View Scheduled Event Post #<?php echo $model->SchedId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'SchedId',
		'Title',
		'Description',
		array(
			'name' => 'StartDate',
			'value' => substr($model->StartDate,0,10),
		     ),		
		array(
			'name' => 'EndDate',
			'value' => substr($model->EndDate,0,10),
		     ),		
		'AwardName',
		'AwardType',
		'Value',
		'PointsId',
		'CouponId',
		'RewardId',
		array(
			'name' => 'ClientId',
			'value' => $model->sClients->CompanyName,
		     ),		
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->sCreateUsers->Username,
		     ),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->sUpdateUsers->Username,
		     ),
		  array(
                'name'  => 'Summary',
                'value' => CHtml::link('Customer List',Yii::app()->createUrl("schedEventPost/summary",array("id"=>$model->SchedId))),
                'type'  => 'raw',
        	),
		),
));
?>
