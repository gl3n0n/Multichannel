<?php
/* @var $this UsersController */
/* @var $model Users */
/* @var $form CActiveForm */
?>
<div class="form" id="new-user-form" style="display:none;">
<br>
<h2>Create New User</h2>
<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'users-_formCreate-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// See class documentation of CActiveForm for details on this,
	// you need to use the performAjaxValidation()-method described there.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model, $submodel); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Username'); ?>
		<?php echo $form->textField($model,'Username'); ?>
		<?php echo $form->error($model,'Username'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'Password'); ?>
		<?php echo $form->passwordField($model,'Password'); ?>
		<?php echo $form->error($model,'Password'); ?>
	</div>

        <div class="row">
                <?php echo $form->labelEx($model,'ConfirmPassword'); ?>
                <?php echo $form->passwordField($model,'ConfirmPassword'); ?>
                <?php echo $form->error($model,'ConfirmPassword'); ?>
        </div>


	<div class="row">
		<?php echo $form->labelEx($model,'FirstName'); ?>
		<?php echo $form->textField($model,'FirstName'); ?>
		<?php echo $form->error($model,'FirstName'); ?>

		<?php echo $form->labelEx($model,'MiddleName'); ?>
		<?php echo $form->textField($model,'MiddleName'); ?>
		<?php echo $form->error($model,'MiddleName'); ?>

		<?php echo $form->labelEx($model,'LastName'); ?>
		<?php echo $form->textField($model,'LastName'); ?>
		<?php echo $form->error($model,'LastName'); ?>
	</div>
<!--
	<div class="row">
		<?//php echo $form->labelEx($model,'Gender'); ?>
		<?//php echo ZHtml::enumDropDownList(Users::model(), 'Gender'); ?>
		<?//php echo $form->error($model,'Gender'); ?>
	</div>

	<div class="row">
		<?//php echo $form->labelEx($model,'Birthdate'); ?>
		<?//php echo $form->textField($model,'Birthdate'); ?>
		<?//php echo $form->error($model,'Birthdate'); ?>
	</div>
-->
	<div class="row">
		<?php echo $form->labelEx($model,'ContactNumber'); ?>
		<?php echo $form->textField($model,'ContactNumber'); ?>
		<?php echo $form->error($model,'ContactNumber'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Address'); ?>
		<?php echo $form->textField($model,'Address'); ?>
		<?php echo $form->error($model,'Address'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Email'); ?>
		<?php echo $form->textField($model,'Email'); ?>
		<?php echo $form->error($model,'Email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'AccessType'); ?>
		<?php echo CHtml::dropDownList('Users[AccessType]', array(
				'id'=>'Users_AccessType',
			),
			array(
				'SUPERADMIN' => 'SUPERADMIN',
				'ADMIN' => 'ADMIN',
			)
		); ?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo ZHtml::enumDropDownList(Users::model(), 'Status', array(
		    'name'=>'Users[Status]',
		    'value'=>'',
			)); ?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row" id="create-user-company-info">
		<hr>

		<h3>Company Information</h3>

		<div class="row">
			<?php echo $form->labelEx($submodel,'CompanyName'); ?>
			<?php echo $form->textField($submodel,'CompanyName'); ?>
			<?php echo $form->error($submodel,'CompanyName'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($submodel,'Status'); ?>
			<?php echo ZHtml::enumDropDownList($submodel, 'Status', array(
			    // 'id'=>'create-client-status',
			    'name'=>'Clients[Status]',
			    'value'=>'',
				)); ?>
			<?php echo $form->error($submodel,'Status'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($submodel,'Address'); ?>
			<?php echo $form->textField($submodel,'Address'); ?>
			<?php echo $form->error($submodel,'Address'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($submodel,'Landline'); ?>
			<?php echo $form->textField($submodel,'Landline'); ?>
			<?php echo $form->error($submodel,'Landline'); ?>
		</div>

		<div class="row">
			<?php echo $form->labelEx($submodel,'Email'); ?>
			<?php echo $form->textField($submodel,'Email'); ?>
			<?php echo $form->error($submodel,'Email'); ?>
		</div>
	</div>
	
	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit', array('id'=>'new-user-submit')); ?>
		<a href="javascript:void(0);" id="new-user-cancel-button">Cancel</a>
	</div>

<?php $this->endWidget(); ?>
<br>
</div><!-- form -->
