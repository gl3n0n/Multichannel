<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points System Mapping',
);

$this->menu=array(
	array('label'=>'Create Points System Mapping', 'url'=>array('create')),
);
?>

<h1>Points System Mapping</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("pointsSystemMapping/index"),
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
			'name'  => 'PointMappingId',
			'value' => 'CHtml::link($data->PointMappingId,Yii::app()->createUrl("pointsSystemMapping/view",array("id"=>$data->primaryKey)))',
			'type'  => 'raw',
		),
		
		array(
		'name' => 'PointsId',
		'value'=> '$data->byPointsSystem!=null?($data->byPointsSystem->Name):("")',
		),		
		array(
			'name' => 'ClientId',
			'value'=> '$data->byClients!=null?($data->byClients->CompanyName):("")',
			),		
		array(
			'name' => 'BrandId',
			'value'=> '$data->byBrands!=null?($data->byBrands->BrandName):("")',
			),		
		array(
			'name' => 'CampaignId',
			'value'=> '$data->byCampaigns!=null?($data->byCampaigns->CampaignName):("")',
			),		
		array(
			'name' => 'ChannelId',
			'value'=> '$data->byChannels!=null?($data->byChannels->ChannelName):("")',
			),							
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
)); 
?>

