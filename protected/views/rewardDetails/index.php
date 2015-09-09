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
)); ?>
	<fieldset>
		<legend>Search Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Name" title="Search Name">
		<button type="submit">Search</button>
	</fieldset>
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
			'name' => 'RewardId',
			'value'=> '$data->byRewards!=null?($data->byRewards->Title):("")',
			),
		array(
			'name' => 'PointsId',
			'value'=> '$data->byPointsSystem!=null?($data->byPointsSystem->Name):("")',
			),
		array(
			'name' => 'ClientId',
			'value'=> '$data->byClients!=null?($data->byClients->CompanyName):("")',
			),
		'Inventory',
		'Limitations',
		'Value',
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
