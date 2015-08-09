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
)); ?>
	<fieldset>
		<legend>Search Title</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Title" title="Search Title">
		<button type="submit">Search</button>
	</fieldset>
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
	'name' => 'Image',
	'type' => 'raw',
	'value'=> 'CHtml::link('.
		  'CHtml::image($data->Image,"",array("width"=>"120px"))'.
		  ',$data->Image)',
	),
	'Availability',
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
