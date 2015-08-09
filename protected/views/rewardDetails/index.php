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
	'action'=>Yii::app()->createUrl("rewardDetails/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Channel Name</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="ChannelName" title="Search Channel Name">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
		//'RewardConfigId',
		array(
			'name' => 'RewardConfigId',
			'type' => 'raw',
			'value'=> 'CHtml::link($data->RewardConfigId,Yii::app()->createUrl("rewardDetails/update",array("id"=>$data->primaryKey)))',
			), 

		array(
			'name' => 'RewardId',
			'value' => '$data->rdetailRewardslists->Title',
			),
		array(
			'name' => 'ChannelId',
			'value' => '$data->rdetailChannels->ChannelName',
			),
		'Inventory',
		'Limitations',
		'Value',
		'Availability',
		'Status',
		'DateCreated',
		array(
		'name' => 'CreatedBy',
		'value'=> '($data->rdetailCreateUsers != null )?($data->rdetailCreateUsers->Username):("")',
		),
		'DateUpdated',
		array(
		'name' => 'UpdatedBy',
		'value'=> '($data->rdetailUpdateUsers != null )?($data->rdetailUpdateUsers->Username):("")',
		),
	),
)); ?>
