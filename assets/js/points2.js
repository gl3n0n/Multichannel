var CampaignList = function()
{
    var self = this;

    self.listData = {};
    self.requestUrl = "http://104.156.53.150/multichannel/channels/getchannels";

    self.brandId  = "#Points_BrandId";

    self.chkContainerId = "#Points_CampaignId_Container";
    self.chkItemId      = "[id^=Points_CampaignId]";

    self.init = function()
    {};

    self.getData = function()
    {
        var objRequest = {
            url: self.requestUrl,
            type: "GET",
            dataType: "json"
        };

        objRequest.data = { "BrandId": jQuery(self.brandId).val() };

        objRequest.beforeSend = function() {
            jQuery(self.chkContainerId).html("Loading...");
        };

        objRequest.success = function(response) {
            if(response) self.listData = response;
            else self.listData = {};
        };

        jQuery.ajax(objRequest);
    };

    self.render = function()
    {
        jQuery(self.chkContainerId).empty();

        jQuery.each(self.listData, function(idx, val)
        {
            var itemContainer = jQuery("<div/>");

            // Create the item
            itemContainer
            .append( // make checkbox
              jQuery("<input>", { 
                type: "checkbox", 
                id: "Points_CampaignId_" + idx,
                value: idx, 
                name: "Points[ChannelId][]" 
                })
            )
            .append("&nbsp;")
            .append( // make label
              jQuery("<label/>", { "for": "Points_CampaignId_" + idx, text: val })
              .css("display", "inline-block")
            )
            // 
            jQuery(self.chkContainerId).append(itemContainer);

        });
    };

    self.addEvents = function()
    {
        jQuery(self.brandId).off().click(function(e) {
            e.preventDefault();
            self.getData();
        });
    };
};

var ChannelList = function()
{
    var self = this;

    self.init = function()
    {};
};

var Points = {

};