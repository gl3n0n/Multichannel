<?php
/* @var $this ChannelsController */
/* @var $model Channels */

$this->breadcrumbs=array(
	'Channels'=>array('index'),
	'Manage',
);

$this->menu=array(
	array('label'=>'List Channels', 'url'=>array('index')),
	array('label'=>'Create Channels', 'url'=>array('create')),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#channels-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Channels</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'channels-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'ChannelId',
		'BrandId',
		'CampaignId',
		'ChannelName',
		'Description',
		'DurationFrom',
		/*
		'DurationTo',
		'Type',
		'Status',
		'DateCreated',
		'CreatedBy',
		'DateUpdated',
		'UpdatedBy',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
