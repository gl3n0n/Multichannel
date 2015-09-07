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
	'id'=>'my-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
 

	<div class="row">
		<?php echo $form->labelEx($model,  'Name'); ?>
		<?php echo $form->textField($model,'Name',array(
		'style'    => 'width:200px;',
		'maxlength'=>25
		)); 
		?>
		<?php echo $form->error($model,'Name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		<?php echo $form->dropDownList($model,'PointsId',$pointslist,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->PointsId" => array('selected'=>true)),
        	    'prompt'    => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'PointsId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,  'Value'); ?>
		<?php echo $form->textField($model,'Value',array(
		'style'    => 'width:200px;',
		'maxlength'=> 11,
		)); 
		?>
		<?php echo $form->error($model,'Value'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,  'PointsAction'); ?>
		<?php echo $form->textField($model,'PointsAction',array(
		'style'    => 'width:200px;',
		'maxlength'=> 25,
		)); 
		?>
		<?php echo $form->error($model,'PointsAction'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'PointsCapping'); 
		$stlist = $model->getDropDownList();
		?>
		<?php echo $form->dropDownList($model,'PointsCapping',
			$stlist['PointsCapping'],
			array(
			    'style'   => 'width:200px;',
			    'options' => array("$model->PointsCapping" => array('selected'=>true)),
			    'prompt'    => '-- Pls Select --',
			),
			array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'PointsCapping'); ?>
	</div>

	 
	<div class="row">
		<?php echo $form->labelEx($model,'StartDate'); ?>
		<?php
	    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name'  => 'StartDate',
           //'value' => substr($model->StartDate,0,10),
	   'model' => $model,
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
           )
       ));	?>
		<?php echo $form->error($model,'StartDate'); ?>
	</div>
	

	<div class="row">
		<?php echo $form->labelEx($model,'EndDate'); ?>
		<?php
	    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name'  => 'EndDate',
           //'value' => substr($model->EndDate,0,10),
	   'model' => $model,
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
           )
       ));	?>
		<?php echo $form->error($model,'EndDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,  'Points Limit (Put 0 if Unlimited)'); ?>
		<?php echo $form->textField($model,'PointsLimit',array(
		'style'    => 'width:200px;',
		'maxlength'=> 11,
		)); 
		?>
		<?php echo $form->error($model,'PointsLimit'); ?>
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
