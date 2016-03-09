<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
if($model->scenario === 'insert')
{
   echo Yii::app()->params['jQueryInclude'];
}
?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-_formCreate-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// See class documentation of CActiveForm for details on this,
	// you need to use the performAjaxValidation()-method described there.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php 
	
	echo $form->errorSummary($model); 
	?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'ClientID'); ?>
		<?php echo (($model->clientInfo!=null)?($model->clientInfo->CompanyName):("") ); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Username'); ?>
		<?php echo $model->Username; ?>
	</div>

        <div class="row">
                <?php echo $form->labelEx($model,'FirstName'); ?>
                <?php echo $form->textField($model,'FirstName',array('maxlength'=>50)); ?>
                <?php echo $form->error($model,'FirstName'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'MiddleName'); ?>
                <?php echo $form->textField($model,'MiddleName',array('maxlength'=>50)); ?>
                <?php echo $form->error($model,'MiddleName'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'LastName'); ?>
                <?php echo $form->textField($model,'LastName',array('maxlength'=>50)); ?>
                <?php echo $form->error($model,'Lastname'); ?>
        </div>


        <div class="row">
                <?php echo $form->labelEx($model,'ContactNumber'); ?>
                <?php echo $form->textField($model,'ContactNumber'); ?>
                <?php echo $form->error($model,'ContactNumber'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'AccessType'); ?>
                <?php echo $model->AccessType; ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'Status'); ?>
                <?php echo $form->dropDownList($model, 'Status', ZHtml::enumItem($model, 'Status')); ?>
                <?php echo $form->error($model,'Status'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'DateUpdated'); ?>
                <?php echo $model->DateUpdated; ?>
        </div>

	
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('id'=>'new-user-submit','style'=>'width:200px;')); ?>
	</div>

<?php $this->endWidget(); ?>
<br>
</div>
<!-- form -->

<script>
//dynamic loading
$( document ).ready(function() {
	 
});
</script>
