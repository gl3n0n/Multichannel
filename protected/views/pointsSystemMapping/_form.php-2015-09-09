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
	'id'=>'pointssystemmapping-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		
        	
		<?php 
			$pIdx = '';
			if($model->PointsId && $model->ClientId)
				$pIdx = sprintf("%s-%s",$model->PointsId,$model->ClientId);
				
			echo $form->dropDownList($model,'PointsId',$points_system_list, array(
			'empty'=>'--Select Points System--',
			'options' => array("$pIdx" => array('selected'=>true)),
			'ajax'=>array('type'=>'get',
			'url'=>$this->createUrl('/pointsSystemMapping/getbrands'),
			'data'=>'js:{"PointsId" : this.value }',
			'success'=>'js:function data(response) {
			    $("#' . get_class($model) . '_BrandId'.'").html("");
			    $("#' . get_class($model) . '_CampaignId'.'").html($("<option/>", {value: "", text: "--Select Points System--" })).attr("size", 1);

			    var curval = $("#' . get_class($model) . '_PointsId'.'").val();

			    if(response.length === 0 || ! curval)
			    {
				var promptText = "--Select a brand--";
			    }
			    else {
				var promptText = "--Select a brand--";
				$("#' . get_class($model) . '_CampaignId'.'")
				  .html($("<option/>", {value: "", text: "--Select a brand--" }))
				  .attr("size", 1);
			    }

			    $("#' . get_class($model) . '_BrandId'.'").html($("<option/>", {value: "", text: promptText }));

			    $.each(response, function(idx, val) {
				$("#' . get_class($model) . '_BrandId'.'").append($("<option/>", {
				    value: idx,
				    text: val
				}));
			    });
			}',
			),
		)); ?>        	
		<?php echo $form->error($model,'PointsId'); ?>
	</div>

	<div class="row">
	    <?php echo $form->labelEx($model,'BrandId'); ?>
	    <?php echo $form->dropDownList($model,'BrandId',$brand_list,array(
	      'prompt' => '--Select a brand--',
	    )); ?>
	    <?php echo $form->error($model,'BrandId'); ?>
	  </div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'CampaignId'); ?>
	    <?php $this->renderPartial('application.components.view.checkboxlist', array(
	      'model'        => $model,
	      'listData'     => $campaign_list,
	      'attributeName'=> 'CampaignId',
	    )); ?>
	    <?php echo $form->error($model,'CampaignId'); ?>
	  </div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'ChannelId'); ?>
	    <?php $this->renderPartial('application.components.view.checkboxlist', array(
	      'model'        => $model,
	      'listData'     => $channel_list,
	      'attributeName'=> 'ChannelId',
	    )); ?>
	    <?php echo $form->error($model,'ChannelId'); ?>
	  </div>

	 
	 <div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">


//dynamic loading
$( document ).ready(function() {
});


var CampaignList = function()
{
  var self = this;

  self.listData = {};
  self.brandId             = "#PointsSystemMapping_BrandId";
  self.campaignContainerId = "#PointsSystemMapping_CampaignId_Container";
  self.channelContainerId  = "#PointsSystemMapping_ChannelId_Container";
  self.chkCampaignItems    = '#PointsSystemMapping_CampaignId_Container input[id^=PointsSystemMapping_CampaignId_]';
  self.chkAllCampaignId

  self.init = function()
  {
    jQuery(self.channelContainerId).empty();
    jQuery(self.campaignContainerId).empty();
    self.addEvents();
  };

  self.addEvents = function()
  {
    jQuery(self.brandId)
      .off()
      .on("change", function() {
        self.getData(this.value, [
          self.render,
          self.addEvents
        ]);
      }); // End on

    jQuery(self.chkCampaignItems)
      .off()
      .on("change", function() {
        self.getChannelList(jQuery(self.brandId).val());
    });

    jQuery()
      .off()
      .on("change", function() {
        self.getChannelList(jQuery(self.brandId).val());
    });
  };

  self.getListData = function(BrandId, callbacks)
  {
    self.getData(BrandId);
  };

  self.getChannelList = function() {
      var requestObject = {
          url: BaseUrl + "pointsSystemMapping/getchannels",
          type: "GET",
          dataType: "json"
      };

      requestObject.data = { 
          BrandId: jQuery(self.brandId).val(),
          CampaignId: self.getCampaignIds()
      };

      requestObject.beforeSend = function() {
          jQuery(self.channelContainerId).html("Loading...");
      };

      requestObject.success = function(response) {
          self.refreshChannels(response);
      };

      jQuery.ajax(requestObject);
  };

  self.getData = function(BrandId, callbacks)
  {
    rData = {"BrandId": BrandId};

    // Send request
    jQuery.ajax({
      url: BaseUrl + "pointsSystemMapping/getcampaigns",
      data: rData,
      type: 'GET',
      dataType: 'json',
      beforeSend: function() {
        jQuery(self.campaignContainerId).html("Loading...");
      },
      success: function(response) {
        // Empty the contents of list
        jQuery(self.campaignContainerId).empty();

        if( response ) self.listData = response;

        if(callbacks) {
          jQuery.each(callbacks, function(idx, callback) {
            callback();
          });
        }

      }
    }); // End request

  };

  self.getCampaignIds = function() {
      return jQuery(self.chkCampaignItems + ":checked").map(function() { return this.value }).get();
  };

  self.refreshChannels = function(data)
  {
    jQuery(self.channelContainerId).empty();

    jQuery.each(data, function(idx, val) {
      var itemContainer = jQuery("<div/>");

      // Create the item
      itemContainer
        .append( // make checkbox
          jQuery("<input>", { type: "checkbox", id: "PointsSystemMapping_ChannelId_" + idx,
            value: idx, name: "PointsSystemMapping[ChannelId][]" })
        )
        .append("&nbsp;")
        .append( // make label
          jQuery("<label/>", { "for": "PointsSystemMapping_ChannelId_" + idx, text: val })
            .css("display", "inline-block")
        )
      // 
      jQuery(self.channelContainerId).append(itemContainer);
    });
  };

  self.render = function()
  {
    jQuery.each(self.listData, function(idx, val) {
      var itemContainer = jQuery("<div/>");

      // Create the item
      itemContainer
        .append( // make checkbox
          jQuery("<input>", { type: "checkbox", id: "PointsSystemMapping_CampaignId_" + idx,
            value: idx, name: "PointsSystemMapping[CampaignId][]" })
        )
        .append("&nbsp;")
        .append( // make label
          jQuery("<label/>", { "for": "PointsSystemMapping_CampaignId_" + idx, text: val })
            .css("display", "inline-block")
        )
      // 
      jQuery(self.campaignContainerId).append(itemContainer);
    });
  };
};

var campaignsList = new CampaignList();
campaignsList.init();




</script>
