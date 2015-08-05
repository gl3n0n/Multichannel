var Points = function()
{
  var self = this;

  self.data = null;
  self.campaignsListURL = BaseUrl + 'channels/getcampaigns';
  self.channelsListURL  = BaseUrl + 'channels/getchannels';

  self.brandId        = '#Points_BrandId';
  self.campaignId     = '#Points_CampaignId';
  self.channelId      = '#Points_ChannelId';
  self.fromId         = '#Points_From';
  self.toId           = '#Points_To';
  self.valueId        = '#Points_Value';
  self.pointActionId  = '#Points_PointAction';
  self.pointCappingId = '#Points_PointCapping';
  self.pointsLimitId  = '#Points_PointsLimit';
  self.statusId       = '#Points_Status';

  self.formId = 'Points_Create_Form';

  self.init = function()
  {
    self.data = {};
    self.addEvents();
  };

  self.formValues = function()
  {
    self.data = {};

    jQuery(self.formId).find("[id^=Points]").each(function()
    {
      self.data[this.id] = this.value;
    });
  };

  self.updateCampaigns = function(brandId)
  {
    var data = { 'BrandId': brandId };
    jQuery.ajax({
      url: self.campaignsListURL, 
      'data': data, 
      success: function(response) {
        // $(self.campaignId).html("");
        document.getElementById("Points_CampaignId").innerHTML("");
        jQuery(self.channelId).html( jquery("<option/>",{"value":"", text: "--Select a brand--"})).attr("size", 1);

        var curval = jQuery(self.brandId).val();

        if(response.length === 0)
        {
            var promptTextCampaign = ( ! curval) ? "--Select a brand--" : "--Nothing--";
            jQuery(self.campaignId).html( jQuery("<option/>", {value: "", text: promptTextCampaign })).attr("size",1);
            jQuery(self.channelId).html( jQuery("<option/>", {value: "", text: promptTextCampaign })).attr("size",1);
        }
        else
        { 
            jQuery(self.campaignId).attr("size", 5);
            jQuery(self.channelId).html( jQuery("<option/>", {value: "", text: "--Select a campaign--" })).attr("size",1);
        }

        jQuery.each(response, function(idx, val) {
            jQuery(self.campaignId).append( jQuery("<option/>", {
                value: idx,
                text: val
            }));
        });

      }, 
      dataType: 'json',
      type: 'get'
    });
  };

  self.updateChannels = function(brandId, campaignId)
  {
    var data = { 
      'brand_id': brandId,
      'campaign_id': campaignId
    };
  };

  self.addEvents = function()
  {
    jQuery(self.brandId).on("change", function()
    {
      self.updateCampaigns( jQuery(self.brandId).val() );
    });
  };
};

jQuery.noConflict();
jQuery(document).ready( function()
{
  points = new Points;
  points.init();
});
