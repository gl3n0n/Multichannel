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
	echo $form->errorSummary($submodel); 
	?>
	<div class="row">
		<?php echo $form->labelEx($model,'Username'); ?>
		<?php echo $form->textField($model,'Username',array('maxlength'=>50)); ?>
		<?php echo $form->error($model,'Username'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'Password'); ?>
		<?php echo $form->passwordField($model,'Password',array('maxlength'=>50)); ?>
		<?php echo $form->error($model,'Password'); ?>
	</div>

        <div class="row">
                <?php echo $form->labelEx($model,'ConfirmPassword'); ?>
                <?php echo $form->passwordField($model,'ConfirmPassword',array('maxlength'=>50)); ?>
                <?php echo $form->error($model,'ConfirmPassword'); ?>
        </div>


	<div class="row">
		<?php echo $form->labelEx($model,'FirstName'); ?>
		<?php echo $form->textField($model,'FirstName',array('maxlength'=>50)); ?>
		<?php echo $form->error($model,'FirstName'); ?>

		<?php echo $form->labelEx($model,'MiddleName'); ?>
		<?php echo $form->textField($model,'MiddleName',array('maxlength'=>50)); ?>
		<?php echo $form->error($model,'MiddleName'); ?>

		<?php echo $form->labelEx($model,'LastName'); ?>
		<?php echo $form->textField($model,'LastName',array('maxlength'=>50)); ?>
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
		<?php echo $form->textField($model,'Address',array('maxlength'=>255)); ?>
		<?php echo $form->error($model,'Address'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Email'); ?>
		<?php echo $form->textField($model,'Email',array('maxlength'=>30)); ?>
		<?php echo $form->error($model,'Email'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'AccessType'); ?>
		<?php echo CHtml::dropDownList('Users[AccessType]', array(
				'id'=>'Users_AccessType',
			),
			array(
				'ADMIN'      => 'ADMIN',
				'SUPERADMIN' => 'SUPERADMIN',
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

	<div class="row" id="create-user-company-info" name="create-user-company-info" style="display:none">
		<hr>

		<h3>Company Information</h3>
	
		<div class="row">
		<?php echo $form->labelEx($model,'ClientId'); ?>
		<?php echo $form->dropDownList($submodel,'ClientId',
					$client_list,
					array(
						'style'    => 'width:203px;',
						'options'  => array($model->ClientId => array('selected'=>true)),
						'prompt'   => '-- Select a Client --',
					),
					array('empty' => '-- Pls Select --') );
			  
		    ?>
		<?php echo $form->error($model,'ClientId'); ?>

		</div>
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
	
	
	$("#Users_AccessType").change(function(){
	    hideClientInfo();
	});
	
	$("#Users_AccessType").click(function(){
	    hideClientInfo();
	});

	//hide
	function hideClientInfo()
	{
	    var choice = $("#Users_AccessType" ).val();
		$("#create-user-company-info").hide();
	    if(choice.match(/^(ADMIN)$/g))
	    {
			$("#create-user-company-info").show();
	    }
	}
	
	//init
	$("#create-user-company-info").hide(); 
	hideClientInfo();
});
</script>
