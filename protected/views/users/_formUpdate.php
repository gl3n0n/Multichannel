<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-_formUpdate-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// See class documentation of CActiveForm for details on this,
	// you need to use the performAjaxValidation()-method described there.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if(Yii::app()->user->hasFlash('user-update-success')): ?>
        <div style="background-color: #82ED8A; color: #2E8F34; font-weight: bold; padding: 0.5em; border: 2px solid #2E8F34;">
          <?php echo Yii::app()->user->getFlash('user-update-success'); ?>
        </div>
        <?php endif; ?>
        <?php if(Yii::app()->user->hasFlash('user-update-error')): ?>
        <div style="background-color: #FC9797; color: #BF1111; font-weight: bold; padding: 0.5em; border: 2px solid #BF1111;">
          <?php echo Yii::app()->user->getFlash('user-update-error'); ?>
        </div>
        <?php endif; ?>
	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Username'); ?>
		<?php echo $model->Username; ?>
	</div>

        <div class="row">
                <?php echo $form->labelEx($model,'FirstName'); ?>
                <?php echo $form->textField($model,'FirstName'); ?>
                <?php echo $form->error($model,'FirstName'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'MiddleName'); ?>
                <?php echo $form->textField($model,'MiddleName'); ?>
                <?php echo $form->error($model,'MiddleName'); ?>
        </div>

        <div class="row">
                <?php echo $form->labelEx($model,'LastName'); ?>
                <?php echo $form->textField($model,'LastName'); ?>
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
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
