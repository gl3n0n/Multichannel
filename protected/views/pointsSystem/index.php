<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Points System',
);

$this->menu=array(
	array('label'=>'Create Points System', 'url'=>array('create')),
);
?>

<h1>Points System</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("pointsSystem/index"),
	'method'=>'get',
)); 

include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');

?>
<?php $this->endWidget(); ?>
</div>
<?php 

$this->widget('zii.widgets.grid.CGridView', array(
		'dataProvider'=> $dataProvider,
		'columns'=>array(
			array(
				'name'  => 'PointsId',
				'value' => 'CHtml::link($data->PointsId,Yii::app()->createUrl("pointsSystem/view",array("id"=>$data->primaryKey)))',
				'type'  => 'raw',
			     ),
			array(
				'name'  => 'Name',
				'value' => 'CHtml::link($data->Name,Yii::app()->createUrl("reportsList/ptslog",array("id"=>$data->primaryKey)))',
				'type'  => 'raw',
			     ),
			array(
				'name' => 'ClientId',
				'value'=> '$data->byClients!=null?($data->byClients->CompanyName):("")',
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
