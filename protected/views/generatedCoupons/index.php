<?php
/* @var $this GeneratedCouponsController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Generated Coupons',
);

$this->menu=array(
	array('label'=>'Create GeneratedCoupons', 'url'=>array('create')),
	array('label'=>'Manage GeneratedCoupons', 'url'=>array('admin')),
);
?>

<h1>Generated Coupons</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
