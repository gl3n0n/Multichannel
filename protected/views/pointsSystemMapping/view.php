<?php
/* @var $this BrandsController */
/* @var $model Brands */

$this->breadcrumbs=array(
	'Points System Mapping' =>array('index'),
	$model->PointMappingId,
);

$this->menu=array(
	array('label'=>'List Points System Mapping',   'url'=>array('index')),
	array('label'=>'Create Points System Mapping', 'url'=>array('create')),
	array('label'=>'Update Points System Mapping', 'url'=>array('update', 'id'=>$model->PointMappingId)),
	/*
	array('label'=>'Delete Points System Mapping', 'url'=>'#', 
	'linkOptions'=>array('submit'=>array('delete','id'=>$model->PointMappingId),
	'confirm'=>'Are you sure you want to delete this item?')),	
	*/
	
);
?>
<h1>View Points System Mapping#<?php echo $model->PointMappingId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'PointMappingId',
		array(
		'name' => 'PointsId',
		'value'=> $model->byPointsSystem!=null?($model->byPointsSystem->Name):(""),
		),		
		array(
			'name' => 'ClientId',
			'value'=> $model->byClients!=null?($model->byClients->CompanyName):(""),
			),		
		array(
			'name' => 'BrandId',
			'value'=> $model->byBrands!=null?($model->byBrands->BrandName):(""),
			),		
		array(
			'name' => 'CampaignId',
			'value'=> $model->byCampaigns!=null?($model->byCampaigns->CampaignName):(""),
			),		
		array(
			'name' => 'ChannelId',
			'value'=> $model->byChannels!=null?($model->byChannels->ChannelName):(""),
			),							
		'Status',
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
