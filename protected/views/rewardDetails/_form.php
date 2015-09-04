<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reward-details-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php
    foreach(Yii::app()->user->getFlashes() as $key => $message) {
        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
    }
    ?>

	<?php echo $form->errorSummary($model); ?>
<?php if($model->scenario === 'insert'): // These are displayed when user is creating a new coupon. ?>
	<div class="row">
		<?php echo $form->labelEx($model,'RewardId'); ?>
		<?php echo $form->dropDownList($model,'RewardId',$rewards_list); ?>
		<?php echo $form->error($model,'RewardId'); ?>
	</div>
	
    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'ClientId'); ?>
		<?php $this->renderPartial('application.components.view.checkboxlist', array(
			'model'=>$model,
			'listData'=>$client_list,
			'attributeName'=>'ClientId',
		)); ?>
		<?php echo $form->error($model,'ClientId'); ?>
	</div>
    <?php endif;?>

	<div class="row">
		<?php echo $form->labelEx($model,'BrandId'); ?>
		<?php $this->renderPartial('application.components.view.checkboxlist', array(
			'model'=>$model,
			'listData'=>(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert') ? array() : $brand_list,
			'attributeName'=>'BrandId',
		)); ?>
		<?php echo $form->error($model,'BrandId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'CampaignId'); ?>
		<?php $this->renderPartial('application.components.view.checkboxlist', array(
			'model'=>$model,
			'listData'=>array(),
			'attributeName'=>'CampaignId',
		)); ?>
		<?php echo $form->error($model,'CampaignId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'ChannelId'); ?>
		<?php $this->renderPartial('application.components.view.checkboxlist', array(
			'model'=>$model,
			'listData'=>array(),
			'attributeName'=>'ChannelId',
		)); ?>
		<?php echo $form->error($model,'ChannelId'); ?>
	</div>
<?php endif; // End Create scenario ?>
	<div class="row">
		<?php echo $form->labelEx($model,'Inventory'); ?>
		<?php echo $form->textField($model,'Inventory',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Inventory'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Limitations'); ?>
		<?php echo $form->textField($model,'Limitations',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'Limitations'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Value'); ?>
		<?php echo $form->textField($model,'Value',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'Value'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Availability'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
			'model'=>$model,
			'attribute'=>'Availability',
			'value'=>$model->Availability,
			//additional javascript options for the date picker plugin
			'options'=>array(
			'dateFormat'=>'yy-mm-dd', // yy-mm-dd
			'showAnim'=>'fold',
            // 'debug'=>true,
			'datepickerOptions'=>array('changeMonth'=>true, 'changeYear'=>true),
			),
			// 'htmlOptions'=>array('style'=>'height:15px;'),
			));
			// - See more at: http://arjunphp.com/add-date-picker-text-field-yii/#sthash.8djSHyAQ.dpuf		?>
		<?php echo $form->error($model,'Availability'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

<script type="text/javascript">

var RewardDetails = function() {
    var self = this;
    
    self.moduleName = "RewardDetails";
    
    self.itemPattern  = "#{mod}_{fld}_Container input[id^={mod}_{fld}_]".replace(/\{mod\}/g, self.moduleName);
    self.rewardItem   = "#{mod}_RewardId".replace(/\{mod\}/g, self.moduleName);
    self.clientItem   = self.itemPattern.replace(/\{fld\}/g, "ClientId");
    self.brandItem    = self.itemPattern.replace(/\{fld\}/g, "BrandId");
    self.campaignItem = self.itemPattern.replace(/\{fld\}/g, "CampaignId");
    self.channelItem  = self.itemPattern.replace(/\{fld\}/g, "ChannelId");
    
    self.lstContainerPtrn = "#{mod}_{fld}_Container".replace(/\{mod\}/g, self.moduleName);
    self.clientContainer   = self.lstContainerPtrn.replace(/\{fld\}/g, "ClientId");
    self.brandContainer    = self.lstContainerPtrn.replace(/\{fld\}/g, "BrandId");
    self.campaignContainer = self.lstContainerPtrn.replace(/\{fld\}/g, "CampaignId");
    self.channelContainer  = self.lstContainerPtrn.replace(/\{fld\}/g, "ChannelId");
    
    self.init = function() {
        self.addEvents();
    };
    
    self.addEvents = function() {
        jQuery(self.clientItem).off().on("click", function() {
            var selection = self.selection("ClientId");
            self.brandList(selection);
        });
        
        jQuery(self.brandItem).off().on("click", function() {
            var selection = self.selection("BrandId");
            self.campaignList(selection);
        });

        jQuery(self.campaignItem).off().on("click", function() {
            var selBrands = self.selection("BrandId");
            var selCampaigns = self.selection("CampaignId");
            self.channelList(selBrands, selCampaigns);
        });

        jQuery(self.clientItem).off().on("change", function() {
            var selection = self.selection("ClientId");
            self.brandList(selection);
        });
        
        jQuery(self.brandItem).off().on("change", function() {
            var selection = self.selection("BrandId");
            self.campaignList(selection);
        });

        jQuery(self.campaignItem).off().on("change", function() {
            var selBrands = self.selection("BrandId");
            var selCampaigns = self.selection("CampaignId");
            self.channelList(selBrands, selCampaigns);
        });



    };
    
    self.selection = function(field) {
        var checkedItems = self.itemPattern.replace(/\{fld\}/g, field) + ":checked";
        return jQuery(checkedItems).map( function() { return this.value; }).get();
    };
    
    self.brandList = function(client) {
        var requestObj = {
            url: BaseUrl + "rewardDetails/getbrands",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            ClientId: client
        };
        requestObj.beforeSend = function() {
            jQuery(self.brandContainer).html("Loading...");
            jQuery(self.campaignContainer).empty();
            jQuery(self.channelContainer).empty();
        };
        requestObj.success = function(response) {
            self.refreshList("BrandId", response);
        };
        jQuery.ajax(requestObj);
    };
    
    self.campaignList = function(brand) {
        var requestObj = {
            url: BaseUrl + "rewardDetails/getcampaigns",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            BrandId: brand
        };
        requestObj.beforeSend = function() {
            jQuery(self.campaignContainer).html("Loading...");
            jQuery(self.channelContainer).empty();
        };
        requestObj.success = function(response) {
            self.refreshList("CampaignId", response);
        };
        jQuery.ajax(requestObj);
    };
    
    self.channelList = function(brand, campaign) {
        var requestObj = {
            url: BaseUrl + "rewardDetails/getchannels",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            BrandId: brand,
            CampaignId: campaign
        };
        requestObj.beforeSend = function() {
            jQuery(self.channelContainer).html("Loading...");
        };
        requestObj.success = function(response) {
            self.refreshList("ChannelId", response);
        };
        jQuery.ajax(requestObj);
    };
    
    self.refreshList = function(field, data) {
        var container = self.lstContainerPtrn.replace(/\{fld\}/g, field);
        jQuery(container).empty();
        
        if(!data) jQuery(container).html("--No data--");
        
        jQuery.each(data, function(idx, val) {
          var itemContainer = jQuery("<div/>");
   
          itemContainer
            .append( // make checkbox
              jQuery("<input>", { type: "checkbox", id: self.moduleName + "_" + field + "_" + idx,
                value: idx, name: self.moduleName + "[" + field + "][]" })
            )
            .append("&nbsp;")
            .append( // make label
              jQuery("<label/>", { "for": self.moduleName + "_" + field + "_" + idx, text: val })
                .css("display", "inline-block")
            )

          jQuery(container).append(itemContainer);
        });

        self.addEvents();


    };
}

var rewardDetails = new RewardDetails();
rewardDetails.init();

</script>
</div><!-- form -->