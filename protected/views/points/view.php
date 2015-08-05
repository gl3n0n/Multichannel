<?php
/* @var $this PointsController */
/* @var $model Points */
$this->breadcrumbs=array(
	'Points'=>array('index'),
	$model->PointsId,
);
if (Yii::app()->utils->getUserInfo('AccessType') == 'ADMIN')
{
$this->menu=array(
	array('label'=>'List Points', 'url'=>array('index')),
	array('label'=>'Create Points', 'url'=>array('create')),
	array('label'=>'Update Points', 'url'=>array('update', 'id'=>$model->PointsId)),
	
	array('label'=>'Manage Customer Points', 'url'=>array('/pointsLog/pointid?points_id=' . $model->ChannelId)),
	//array('label'=>'View Customer Points Log', 'url'=>array('/pointsLog/pointid?points_id=' . $model->ChannelId)),
	// array('label'=>'Delete Points', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->PointsId),'confirm'=>'Are you sure you want to delete this item?')),
	//array('label'=>'Manage Points', 'url'=>array('admin')),
);
}
else
{
	$this->menu=array(
	array('label'=>'List Points', 'url'=>array('index')),
	array('label'=>'Create Points', 'url'=>array('create')),
	array('label'=>'Update Points', 'url'=>array('update', 'id'=>$model->PointsId)),
);
}

?>

<h1>View Points #<?php echo $model->PointsId; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'PointsId',
		array(
			'name' => 'ClientId',
			'value' => $model->pointClients->CompanyName,
			),
		array(
			'name' => 'BrandId',
			'value' => $model->pointBrands->BrandName,
			),
		array(
			'name' => 'CampaignId',
			'value' => $model->pointCampaigns->CampaignName,
			),
		array(
			'name' => 'ChannelId',
			'value' => $model->pointChannels->ChannelName,
			),
		
		'From',
		'To',
		'Value',
		'PointAction',
		'PointsLimit',
		'PointCapping',
		'Status',
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => $model->pointCreateUsers->Username,
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => $model->pointUpdateUsers->Username,
			),
	),
)); ?>
