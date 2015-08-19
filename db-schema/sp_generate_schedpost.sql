DROP PROCEDURE if exists sp_generate_schedpost;
delimiter //
CREATE PROCEDURE sp_generate_schedpost ()
begin
   BEGIN
      declare done_p int default 0;
      declare vDescription, vMsg text;
      declare vClientID, vBrandId, vCampaignId, vChannelId, vCustomerId, vCustomerPointId  int default 0;                       
      declare vAwardType, vEventType, vTitle, vEmail, vRepeatType, vCreatedBy, vEventDate, vEventTime varchar(255);
      declare vSchedId, vPointsId,  vCouponId, vRewardId, vSubscriptionId, vPoints int default 0;
      declare c_pat cursor 
      for select a.SchedId, a.CreatedBy, a.EventDate, a.EventTime, a.RepeatType, 
                 a.ClientID, a.BrandId, a.CampaignId, a.ChannelId, a.CustomerId, b.Email,
                 a.AwardType, a.PointsId, a.CouponId, a.RewardId, a.EventType, a.Title, a.Description
          from  scheduled_post a, customers b
          where a.status = 'ACTIVE'
          and a.CustomerId = b.CustomerId
          and a.schedDate = curdate()
          and a.EventTime between curtime() and addtime(curtime(), '00:05:00');
      declare continue handler for sqlstate '02000' set done_p = 1;
   
      OPEN c_pat;
      REPEAT
         FETCH c_pat INTO vSchedId, vCreatedBy, vEventDate, vEventTime, vRepeatType, 
                          vClientID, vBrandId, vCampaignId, vChannelId, vCustomerId, vEmail,                           
                          vAwardType, vPointsId, vCouponId, vRewardId, vEventType, vTitle, vDescription;
         if not done_p then
            if vAwardType = 'POINTS' and vPointsId is not null then
               select value into vPoints
               from   points
               where  PointsId = vPointsId;

               select SubsriptionId into vSubscriptionId
               from   customer_subscriptions
               where  ClientID = vClientID
               and    CustomerId = vCustomerId
               and    BrandId = vBrandId
               and    CampaignId = vCampaignId
               and    ChannelId = vChannelId
               limit 1;

               if ifnull(vSubscriptionId,0) = 0 then
                  insert into customer_subscriptions (CustomerId, ClientID, BrandId, CampaignId, ChannelId, Status, DateCreated, CreatedBy)
                  values (CustomerId, ClientID, BrandId, CampaignId, ChannelId, 'ACTIVE', now(), vCreatedBy);
                  select SubscriptionId into vSubscriptionId
                  from   SubscriptionId
                  where  ClientID = vClientID
                  and    CustomerId = vCustomerId
                  and    BrandId = vBrandId
                  and    CampaignId = vCampaignId
                  and    ChannelId = vChannelId
                  limit 1;
               end if;

               select CustomerPointId into vCustomerPointId
               from   customer_points
               where  SubscriptionId = vSubscriptionId
               limit 1;

               if ifnull(CustomerPointId,0) = 0 then 
                  insert into customer_points (SubscriptionId, Balance, Used, Total, DateCreated, CreatedBy)
                  values (vSubscriptionId, 0, 0, vPoints, now(), vCreatedBy);
               else 
                  update customer_points
                  set    Total = Total + vPoints
                  where  CustomerPointId = vCustomerPointId;
               end if;

               -- insert into points_log
               insert into points_log (CustomerId, SubscriptionId, ClientId, BrandId, CampaignId, ChannelId, PointsId, DateCreated, CreatedBy)
               values (vCustomerId, vSubscriptionId, vClientId, vBrandId, vCampaignId, vChannelId, vPointsId, now(), vCreatedBy);
               
            elseif vAwardType = 'COUPON' and vCouponId is not null then

               select ifnull(PointsValue,0) into vPoints
               from   coupon
               where  CouponId = vCouponId;

               -- update generated_coupons
               update generated_coupons
               set    CustomerId = vCustomerId,
                      Status = 'REDEEMED',
                      DateRedeemed = now()
               where  CouponId = vCouponId
               and    Status = 'PENDING'
               limit  1;

            end if;
            select curdate(), curtime(), vSchedId, vClientID, vCustomerId, 0, vEmail, vMsg;
            Select concat(vTitle, '\n\n', vDescription) into vMsg;
            select vTitle, vDescription;
            insert into push_log (tx_date, tx_time, SchedId, ClientID, CustomerId, status, email_address, msg, dt_created)
            values (curdate(), curtime(), vSchedId, vClientID, vCustomerId, 0, vEmail, vMsg, now());

            if vRepeatType = 'DAILY' then
               update scheduled_post 
               set    schedDate = date_add(curdate(), interval 1 day)
               where  SchedId = vSchedId;
            elseif vRepeatType = 'WEEKLY' then
               update scheduled_post 
               set    schedDate = date_add(curdate(), interval 7 day)
               where  SchedId = vSchedId;
            elseif vRepeatType = 'MONTHLY' then
               update scheduled_post 
               set    schedDate = date_add(curdate(), interval 1 month)
               where  SchedId = vSchedId;
            end if;             
         end if;
      UNTIL done_p
      END REPEAT;
   END;
end;
//
delimiter ;
