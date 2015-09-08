<?php
/* @var $this BrandsController */
/* @var $model Brands */
/* @var $form CActiveForm */
if($model->scenario === 'insert')
{
   echo Yii::app()->params['jQueryInclude'];
}


?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'pointssystem-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
	<div class="row">
	<?php echo $form->labelEx($model,'ClientId'); ?>
	<?php echo $form->dropDownList($model,'ClientId',$client_list,
		array(
		    'style'     => 'width:200px;',
		    'onChange'  => 'getBrands()',
		    'prompt'    => '-- Pls Select --',
		),
		array('empty' => '-- Pls Select --')); 
		?>
	<?php echo $form->error($model,'ClientId'); ?>
	</div>
	<?php endif; ?>

	<div class="row">
		<?php echo $form->labelEx($model,  'Name'); ?>
		<?php echo $form->textField($model,'Name',array(
		'style'    => 'width:200px;',
		'maxlength'=> 25
		)); 
		?>
		<?php echo $form->error($model,'Name'); ?>
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
