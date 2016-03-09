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

    <?php if($model->scenario === 'insert'): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'Coupon System Name'); ?>
		<?php echo $form->dropDownList($model,'CouponId',$coupon_list,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->CouponId" => array('selected'=>true)),
        	)); 
        	?>
		<?php echo $form->error($model,'CouponId'); ?>
	</div>
	
	<?php else: ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'Coupon System Name'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,
        		'maxlength'=>20,
        		'disabled'=>true, 
        		'value'=>$model->byCoupon->CouponName,
        		'style' => 'width:200px;')); ?>
        <?php echo $form->error($model,'CouponId'); 
		echo CHtml::hiddenField('CouponId', $model->CouponId, array('id'=>'CouponToPoints[CouponId]'));
		?>
	  </div>
	  
	<?php endif; ?>
	
 

	<div class="row">
		<?php echo $form->labelEx($model,  'Coupon To Points Name'); ?>
		<?php echo $form->textField($model,'Name',array(
		'style'    => 'width:200px;',
		'maxlength'=>255
		)); 
		?>
		<?php echo $form->error($model,'Name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,  'Coupon To Points Value'); ?>
		<?php echo $form->textField($model,'Value',array(
			'style'     => 'width:200px;',
			'maxlength' => 11
		));
		?>
		<?php echo $form->error($model,'Value'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'StartDate'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'StartDate',
		   'model'=>$model,
			'attribute'=>'StartDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#EndDate").datepicker("option", "minDate", selectedDate); }',            
           )
       ));	?>
		<?php echo $form->error($model,'StartDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'EndDate'); ?>
		<?php
			 $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'EndDate',
		   'model'=>$model,
			'attribute'=>'EndDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#StartDate").datepicker("option", "maxDate", selectedDate); }',
           )
       ));	?>
		<?php echo $form->error($model,'EndDate'); ?>
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
