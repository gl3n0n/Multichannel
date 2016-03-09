


SELECT  DISTINCT
gen.GeneratedCouponId,
gen.Code,
sub.PointsId,
map.CouponType,
IFNULL(map.PointsValue,0) as PointsValue,
gen.CouponId,
sub.SubscriptionId,
sub.CustomerId,
sub.ClientId,
(
select clnt.Status
from clients clnt
where
clnt.ClientId = sub.ClientId
limit 1
) as Client_Status,
gen.Status as Generation_status,
(
select cust.Status
from
customers  cust
where
cust.CustomerId = sub.CustomerId
limit 1
) as Customer_Status,
map.Status as Coupon_Status,
sub.BrandId,
sub.CampaignId,
(
select chan.ChannelId
from
channels chan
where
chan.ClientId     = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as ChannelId,
(
select chan.Status
from
channels chan
where
chan.ClientId     = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as Channel_Status,
(
select brnd.Status
from
brands brnd
where
brnd.BrandId   = sub.BrandId
limit 1
) as Brand_Status,
(
select camp.Status
from
campaigns camp
where
camp.CampaignId   = sub.CampaignId
limit 1
) as Campaign_Status,
(
select typ.ActiontypeId
from
action_type typ
where
typ.PointsId = map.PointsId
and typ.ClientId = sub.ClientId
limit 1
) as ActionTypeId,

(
select date_format(chan.DurationFrom,'%Y%m%d')
from
channels chan
where
chan.ClientId   = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as Channel_DurationFrom,
(
select date_format(chan.DurationTo,'%Y%m%d')
from
channels chan
where
chan.ClientId   = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as Channel_DurationTo,
(
select date_format(camp.DurationFrom,'%Y%m%d')
from
campaigns camp
where
camp.CampaignId   = sub.CampaignId
limit 1
) as Campaign_DurationFrom,
(
select date_format(camp.DurationTo,'%Y%m%d')
from
campaigns camp
where
camp.CampaignId   = sub.CampaignId
limit 1
) as Campaign_DurationTo,
(
select date_format(brnd.DurationFrom,'%Y%m%d')
from
brands brnd
where
brnd.BrandId   = sub.BrandId
limit 1
) as Brand_DurationFrom,
(
select date_format(brnd.DurationTo,'%Y%m%d')
from
brands brnd
where
brnd.BrandId   = sub.BrandId
limit 1
) as Brand_DurationTo,
IFNULL((
select sum(IFNULL(c.Balance,0))
from
customer_points c
where
1=1
and c.PointsId       = sub.PointsId
),0) as Customer_Points_Balance,
IFNULL((
select ifnull(d.Value,0) from
coupon_to_points d
where
d.CouponId  = map.CouponId
and d.status    = 'ACTIVE'
and d.ClientId  = sub.ClientId
),0) as Coupon_To_Points_Value,
(curdate() <= map.ExpiryDate ) as coupon_not_expired,
( (
select count(1)
from
generated_coupons g
where 1=1
and g.CouponId   = gen.CouponId
and g.PointsId   = gen.PointsId
and g.CustomerId = sub.CustomerId
) < map.LimitPerUser ) as check_history_total
FROM
customer_subscriptions sub,
coupon map,
generated_coupons gen
WHERE   1=1
AND sub.ClientId   = '36'
AND sub.CustomerId = '38'
AND map.CouponId   = '1'
AND sub.PointsId   = map.PointsId
AND sub.ClientId   = map.ClientId
AND sub.Status     = 'ACTIVE'
AND gen.Status     = 'PENDING'
AND sub.PointsId   = gen.PointsId
AND map.CouponId   = gen.CouponId
AND gen.Code       = 'Y8kje'
AND map.LimitPerUser > 0 ;




SELECT  DISTINCT
gen.GeneratedCouponId,
gen.Code,
sub.PointsId,
map.CouponType,
IFNULL(map.PointsValue,0) as PointsValue,
gen.CouponId,
sub.SubscriptionId,
sub.CustomerId,
sub.ClientId,
(
select clnt.Status
from clients clnt
where
clnt.ClientId = sub.ClientId
limit 1
) as Client_Status,
gen.Status as Generation_status,
(
select cust.Status
from
customers  cust
where
cust.CustomerId = sub.CustomerId
limit 1
) as Customer_Status,
map.Status as Coupon_Status,
sub.BrandId,
sub.CampaignId,
(
select chan.ChannelId
from
channels chan
where
chan.ClientId     = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as ChannelId,
(
select chan.Status
from
channels chan
where
chan.ClientId     = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as Channel_Status,
(
select brnd.Status
from
brands brnd
where
brnd.BrandId   = sub.BrandId
limit 1
) as Brand_Status,
(
select camp.Status
from
campaigns camp
where
camp.CampaignId   = sub.CampaignId
limit 1
) as Campaign_Status,
(
select typ.ActiontypeId
from
action_type typ
where
typ.PointsId = map.PointsId
and typ.ClientId = sub.ClientId
limit 1
) as ActionTypeId,

(
select date_format(chan.DurationFrom,'%Y%m%d')
from
channels chan
where
chan.ClientId   = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as Channel_DurationFrom,
(
select date_format(chan.DurationTo,'%Y%m%d')
from
channels chan
where
chan.ClientId   = sub.ClientId
and chan.BrandId    = sub.BrandId
and chan.CampaignId = sub.CampaignId
limit 1
) as Channel_DurationTo,
(
select date_format(camp.DurationFrom,'%Y%m%d')
from
campaigns camp
where
camp.CampaignId   = sub.CampaignId
limit 1
) as Campaign_DurationFrom,
(
select date_format(camp.DurationTo,'%Y%m%d')
from
campaigns camp
where
camp.CampaignId   = sub.CampaignId
limit 1
) as Campaign_DurationTo,
(
select date_format(brnd.DurationFrom,'%Y%m%d')
from
brands brnd
where
brnd.BrandId   = sub.BrandId
limit 1
) as Brand_DurationFrom,
(
select date_format(brnd.DurationTo,'%Y%m%d')
from
brands brnd
where
brnd.BrandId   = sub.BrandId
limit 1
) as Brand_DurationTo,
IFNULL((
select sum(IFNULL(c.Balance,0))
from
customer_points c
where
1=1
and c.PointsId       = sub.PointsId
),0) as Customer_Points_Balance,
IFNULL((
select ifnull(d.Value,0) from
coupon_to_points d
where
d.CouponId  = map.CouponId
and d.status    = 'ACTIVE'
and d.ClientId  = sub.ClientId
),0) as Coupon_To_Points_Value,
(curdate() <= map.ExpiryDate ) as coupon_not_expired,
( (
select count(1)
from
generated_coupons g
where 1=1
and g.CouponId   = gen.CouponId
and g.PointsId   = gen.PointsId
and g.CustomerId = sub.CustomerId
) < map.LimitPerUser ) as check_history_total
FROM
customer_subscriptions sub,
coupon map,
generated_coupons gen
WHERE   1=1
AND sub.ClientId   = '36'
AND sub.CustomerId = '38'
AND map.CouponId   = '1'
AND sub.PointsId   = map.PointsId
AND sub.ClientId   = map.ClientId
AND sub.Status     = 'ACTIVE'
AND gen.Status     = 'PENDING'
AND sub.PointsId   = gen.PointsId
AND map.CouponId   = gen.CouponId
AND map.LimitPerUser > 0 \G;



