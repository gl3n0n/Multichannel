<?php
/* @var $this RewardsListController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Rewards Lists',
);

$this->menu=array(
	array('label'=>'Create RewardsList', 'url'=>array('create')),
	// array('label'=>'Manage RewardsList', 'url'=>array('admin')),
);
?>

<h1>Rewards Lists</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("rewardsList/index"),
	'method'=>'get',
)); 

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bytitle-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');

?>
	
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	//'RewardId',
	array(
	'name' => 'RewardId',
	'type' => 'raw',
	'value'=> 'CHtml::link($data->RewardId,Yii::app()->createUrl("rewardsList/update",array("id"=>$data->primaryKey)))',
	), 
	
	'Title',
	'Description',
	array(
		'name' => 'ClientId',
		'value'=> '($data->rewardClients != null )?($data->rewardClients->CompanyName):("")',
		),
	'Availability',
	array(
	'name' => 'Image',
	'type' => 'raw',
	'value'=> 'CHtml::link('.
		  'CHtml::image($data->Image,"",array("width"=>"120px" ,"height"=>"120px"))'.
		  ',$data->Image)',
	),
	'Status',
	'DateCreated',
	array(
		'name'  => 'CreatedBy',
		'value'=> '($data->rewardCreateUsers != null )?($data->rewardCreateUsers->Username):("")',
		),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> '($data->rewardUpdateUsers != null )?($data->rewardUpdateUsers->Username):("")',
	),
			),
)); ?>
