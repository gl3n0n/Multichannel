<?php

/**
 * This is the model class for table "channels".
 *
 * The followings are the available columns in table 'channels':
 * @property string $ChannelId
 * @property string $ClientID
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelName
 * @property string $Description
 * @property string $DurationFrom
 * @property string $DurationTo
 * @property string $Type
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class Channels extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'channels';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ChannelName, Description, DurationFrom, DurationTo, Type', 'required'),
			array('CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('ClientId, BrandId, CampaignId', 'length', 'max'=>11),
			array('ChannelName, Description', 'length', 'max'=>100),
			array('Type', 'length', 'max'=>50),
			array('Status', 'length', 'max'=>8),
			array('DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ChannelId, ClientId, BrandId, CampaignId, ChannelName, Description, DurationFrom, DurationTo, Type, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
			// 'brands'=>array(self::HAS_MANY, 'Brands'),
			'channelClients'=>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'channelBrands'=>array(self::BELONGS_TO, 'Brands', 'BrandId'),
			'channelCampaigns'=>array(self::BELONGS_TO, 'Campaigns', 'CampaignId', 'alias'=>'champaigns'),
			'channelCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'channelUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
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
			'ChannelId' => 'Channel ID',
			'ClientId' => 'Client Name',
			'BrandId' => 'Brand Name',
			'CampaignId' => 'Campaign Name',
			'ChannelName' => 'Channel Name',
			'Description' => 'Description',
			'DurationFrom' => 'Duration From',
			'DurationTo' => 'Duration To',
			'Type' => 'Type',
			'Status' => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
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

		$criteria->compare('ChannelId',$this->ChannelId,true);
		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('CampaignId',$this->CampaignId,true);
		$criteria->compare('ChannelName',$this->ChannelName,true);
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('DurationFrom',$this->DurationFrom,true);
		$criteria->compare('DurationTo',$this->DurationTo,true);
		$criteria->compare('Type',$this->Type,true);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Channels the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
