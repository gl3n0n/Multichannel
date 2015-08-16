<?php
/* @var $this BrandsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupon To Points',
);

$this->menu=array(
	array('label'=>'Create Coupon To Points', 'url'=>array('create')),
);
?>

<h1>Coupon To Points</h1>
<div>
<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("couponToPoints/index"),
	'method'=>'get',
)); ?>
	<fieldset>
		<legend>Search Title</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Title" title="Search Title">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); 
if(0)
{
	echo "<h>".@var_export($dataProvider->getData(),true);
	exit;
}
?>
</div>
<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=> $dataProvider,
	//'itemView'=>'_view',
	'columns'=>array(
	array(
		'name'  => 'CtpId',
		'value' => 'CHtml::link($data->CtpId,Yii::app()->createUrl("couponToPoints/update",array("id"=>$data->primaryKey)))',
		'type'  => 'raw',
	),
	'Title',
	'CouponRequired',
	'CouponId',
	'PointsValue',
	'Status',
	'DateCreated',
	array(
		'name' => 'CreatedBy',
		'value'=> '$data->p2couponCreateUsers!=null ? $data->p2couponCreateUsers->Username : ""',
	),
	'DateUpdated',
	array(
		'name' => 'UpdatedBy',
		'value'=> '$data->p2couponUpdateUsers!=null ? $data->p2couponUpdateUsers->Username : ""',
	),
	),
)); 
?>

