<?php
/* @var $this RewardDetailsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Reward Details',
);

$this->menu=array(
	array('label'=>'Create RewardDetails', 'url'=>array('create')),
	// array('label'=>'Manage RewardDetails', 'url'=>array('admin')),
);
?>

<h1>Reward Details</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("RewardDetails/index"),
	'method'=>'get',
)); 


include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');


?>

<?php $this->endWidget(); ?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	'columns'=>array(
		array(
			'name'  => 'RewardConfigId',
			'value' => 'CHtml::link($data->RewardConfigId,Yii::app()->createUrl("rewardDetails/view",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
		),
		'Name',
		array(
			'name' => 'PointsId',
			'value'=> '$data->byPointsSystem!=null?($data->byPointsSystem->Name):("")',
			),
		array(
			'name' => 'ClientId',
			'value'=> '$data->byClients!=null?($data->byClients->CompanyName):("")',
			),
		array(
			'name' => 'RewardId',
			'value'=> '$data->byRewards!=null?($data->byRewards->Title):("")',
			),
		'Inventory',
		'Value',
		'Limitations',
		'StartDate',
		'EndDate',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value'=> '$data->byCreateUsers!=null?($data->byCreateUsers->Username):("")',
		 ),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value'=> '$data->byUpdateUsers!=null?($data->byUpdateUsers->Username):("")',
		 ),
	),
)); ?>
