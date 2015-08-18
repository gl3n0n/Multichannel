<?php

/**
 * This is the model class for table "points".
 *
 * The followings are the available columns in table 'points':
 * @property string $ScheduledPostId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 * @property string $From
 * @property string $To
 * @property string $Value
 * @property string $PointAction
 * @property string $PointCapping
 * @property string $ScheduledPostLimit
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class ScheduledPost extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'scheduled_post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('BrandId, CampaignId, ChannelId, ClientId, Title, Description,EventDate', 'required'),
			array('ClientId, BrandId, CampaignId, ChannelId', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('EventDate', 'match', 'pattern'=>'/^\d{4}-\d{2}-\d{2}$/'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('SchedId,ClientId,BrandId,CampaignId,ChannelId,Title,
			Description,EventDate,Status, DateCreated, 
			CreatedBy, DateUpdated, UpdatedBy,EventType,RepeatType,AwardType,PointsId,CouponId,RewardId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'schedClients'    =>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'schedBrands'     =>array(self::BELONGS_TO, 'Brands', 'BrandId'),
			'schedCampaigns'  =>array(self::BELONGS_TO, 'Campaigns', 'CampaignId'),
			'schedChannels'   =>array(self::BELONGS_TO, 'Channels', 'ChannelId'),
			'schedCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'schedUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
		);
	}
	
	public function scopes()
	{
		return array(
			'thisClient'=>array(
				'condition'=>'ClientId = :modelClientId',
				'params' => array(':modelClientId'=>Yii::app()->utils->getUserInfo('ClientId')),
			),
			'active'=>array(
				'condition'=>"Status='ACTIVE'",
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'SchedId'     => 'Scheduled Post',
			'ClientId'    => 'Client Name',
			'BrandId'     => 'Brand Name',
			'CampaignId'  => 'Campaign Name',
			'ChannelId'   => 'Channel Name',
			'Description' => 'Description',
			'EventDate'   => 'Event Date',
			
			'EventType'   => 'Event Type',
			'RepeatType'   => 'Repeat Type',
			'AwardType'   => 'Award Type',
			'PointsId'   => 'Points',
			'CouponId'   => 'Coupon',
			'RewardId'   => 'Reward',
			'Status'      => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy'   => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy'   => 'Updated By',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('SchedId',    $this->SchedId,true);
		$criteria->compare('ClientId',   $this->ClientId,true);
		$criteria->compare('BrandId',    $this->BrandId,true);
		$criteria->compare('CampaignId', $this->CampaignId,true);
		$criteria->compare('ChannelId',  $this->ChannelId,true);
		$criteria->compare('Title',      $this->Title,true);
		$criteria->compare('Description',$this->Description,true);
		
		$criteria->compare('Status',     $this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',  $this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',  $this->UpdatedBy);
		$criteria->compare('EventDate',  $this->EventDate);
		
		
		$criteria->compare('EventType',  $this->EventType);
		$criteria->compare('RepeatType', $this->RepeatType);
		$criteria->compare('AwardType',  $this->AwardType);
		$criteria->compare('PointsId',   $this->PointsId);
		$criteria->compare('CouponId',   $this->CouponId);
		$criteria->compare('RewardId',   $this->RewardId);

		

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ScheduledPost the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getDropDownList()
	{
		return array(
		'RepeatType'     => array(
			'DAILY'  => 'DAILY'   ,  
			'WEEKLY' => 'WEEKLY'  ,
			'MONTHLY'=> 'MONTHLY' ,
			'NONE'   => 'NONE'    ,
			),
		'AwardType'      => array(
			'POINT'  => 'POINT' ,
			'COUPON' => 'COUPON',
			'REWARD' => 'REWARD',
			'NONE'   => 'NONE'  ,
			),
		);

	}
	
}
