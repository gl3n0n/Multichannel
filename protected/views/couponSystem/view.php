<?php
/* @var $this CouponController */
/* @var $model Coupon */

$this->breadcrumbs=array(
	'Coupon System'=>array('index'),
	$model->CouponId,
);

$this->menu=array(
	array('label'=>'List Coupon System', 'url'=>array('index')),
	array('label'=>'Create Coupon System', 'url'=>array('create')),
	array('label'=>'Update Coupon System', 'url'=>array('update', 'id'=>$model->CouponId)),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu[] =	array('label'=>'Pending Coupon System', 'url'=>array('pending'));
}


?>
<script>
function downloadCSV(csvPath) 
{
	var iframe;
	iframe = document.getElementById("csvdownloader");
	if (iframe == null) {
		iframe = document.createElement('iframe');
		iframe.id = "csvdownloader";
		iframe.style.visibility = 'hidden';
		document.body.appendChild(iframe);
	}
	iframe.src = csvPath;
	return true;
}
$( document ).ready(function() {
  
});
</script>

<h1>View <?php echo $model->CouponName; ?></h1>
<div>
<?php 
if(!empty($downloadCSV))
{
?>
	<div>
		<fieldset class='filterSrch'>
		<a href="#" onclick="downloadCSV('<?php echo Yii::app()->createUrl("reportsList/csv")?>/?fn=<?php echo $downloadCSV?>');">
			DOWNLOAD CSV 
		</a>
		</fieldset>
		<iframe id="csvdownloader" style="display:none"
				width=0 height=0 style="hidden" frameborder=0 marginheight=0 marginwidth=0 scrolling=no></iframe>
	</div>
<?php
}//show download
?>
</div>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'CouponId',
		'CouponName',
		array(
			'name'  => 'Points System Id',
			'value' => CHtml::link($model->PointsId,Yii::app()->createUrl("pointsSystem/view",array("id"=>$model->PointsId))),
			'type'  => 'raw',
		),
		array(
			'name' => 'Points System Name',
			'value' => (($model->byPoints != null)?($model->byPoints->Name):('') ),
			),		
		        array(
                'name'  => 'Coupon Mode',
                'value' => $model->TypeId,
        ),
		        array(
                'name'  => 'Code Type',
                'value' => $model->Type,
        ),
		'Source',
		'ExpiryDate',
		'CodeLength',
		'CouponType',
		'PointsValue',
		'CouponUrl',
		'Status',
		array(
		'name'  => 'ClientId',
		'value' => ($model->byClients!=null)?($model->byClients->CompanyName):(""),
		),	
		'DateCreated',
		array(
			'name' => 'CreatedBy',
			'value' => (($model->couponCreateUsers != null)?($model->couponCreateUsers->Username):('') ),
			),
		'DateUpdated',
		array(
			'name' => 'UpdatedBy',
			'value' => (($model->couponUpdateUsers != null)?($model->couponUpdateUsers->Username):('') ),
			),
		array(
		'name' => 'Image',
		'type' => 'raw',
		'value'=> CHtml::image($model->Image,"",array("width"=>"120px") )
		),		
		'Quantity',
		'LimitPerUser',
		array(
		'name' => 'File',
		'type' => 'raw',
		'value'=> ($model->File != null)?(basename($model->File)):(""),
		),		
	),
)); ?>
