<?php
/* @var $this BrandsController */
/* @var $model Brands */
/* @var $form CActiveForm */
echo Yii::app()->params['jQueryInclude'];
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'brands-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

    
	<div class="row">
		<?php echo $form->labelEx($model,'CouponId'); ?>
		<?php echo $form->dropDownList($model,'CouponId',$coupon_list,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->CouponId" => array('selected'=>true)),
        	)); 
        	?>
		<?php echo $form->error($model,'CouponId'); ?>
	</div>
	
 

	<div class="row">
		<?php echo $form->labelEx($model,  'Name'); ?>
		<?php echo $form->textField($model,'Name',array(
		'style'    => 'width:200px;',
		'maxlength'=>255
		)); 
		?>
		<?php echo $form->error($model,'Name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,  'Value'); ?>
		<?php echo $form->textField($model,'Value',array(
			'style'     => 'width:200px;',
			'maxlength' => 11
		));
		?>
		<?php echo $form->error($model,'Value'); ?>
	</div>
	
	<div class="row">
	 <?php echo $form->labelEx($model,'Status'); ?>
		    <?php echo CHtml::dropDownList('CouponToPoints[Status]', 
			$model->scenario === 'update' ? $model->Status : 'ACTIVE', 
			ZHtml::enumItem($model, 'Status'),array( 'style' => 'width:200px;')); 
		    ?>
	   <?php echo $form->error($model,'Status'); ?>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>


//dynamic loading
$( document ).ready(function() {

});

</script>