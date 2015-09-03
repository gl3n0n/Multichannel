<?php

/**
 * This is the model class for table "reward_details".
 *
 * The followings are the available columns in table 'reward_details':
 * @property string $RewardConfigId
 * @property string $RewardId
 * @property integer $ChannelId
 * @property string $Inventory
 * @property string $Limitations
 * @property string $Value
 * @property string $Availability
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 * @property integer $ClientId
 * @property integer $BrandId
 * @property integer $CampaignId
 */
class RewardDetails extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'reward_details';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('RewardId, ChannelId, BrandId, CampaignId, Inventory, Limitations, Value, Availability', 'required'),
			array('ChannelId, CreatedBy, UpdatedBy, ClientId, BrandId, CampaignId, Inventory, Value',  'numerical', 'integerOnly'=>true),
			array('RewardId', 'length', 'max'=>11),
			array('Limitations', 'length', 'max'=>255),
			array('Status', 'length', 'max'=>8),
            		array('Status', 'default', 'value'=>'PENDING', 'setOnEmpty'=>true),
			array('Availability, DateCreated, DateUpdated', 'safe'),
			array('Inventory', 'match', 'pattern'=>'/^\d{1,11}$/'),
			
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('RewardConfigId, RewardId, ChannelId, Limitations, Value, Availability, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy, ClientId, BrandId, CampaignId', 'safe', 'on'=>'search'),
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
			'rdetailRewardslists'=>array(self::BELONGS_TO, 'RewardsList', 'RewardId'),
			'rdetailChannels'=>array(self::BELONGS_TO, 'Channels', 'ChannelId'),
			'rdetailCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'rdetailUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),

		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'RewardConfigId' => 'Reward Config',
			'RewardId' => 'Reward',
			'ChannelId' => 'Channel',
			'Inventory' => 'Inventory',
			'Limitations' => 'Limitations',
			'Value' => 'Value',
			'Availability' => 'Availability',
			'Status' => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
			'ClientId' => 'Client',
			'BrandId' => 'Brand',
			'CampaignId' => 'Campaign',
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

		$criteria->compare('RewardConfigId',$this->RewardConfigId,true);
		$criteria->compare('RewardId',$this->RewardId,true);
		$criteria->compare('ChannelId',$this->ChannelId);
		$criteria->compare('Inventory',$this->Inventory,true);
		$criteria->compare('Limitations',$this->Limitations,true);
		$criteria->compare('Value',$this->Value,true);
		$criteria->compare('Availability',$this->Availability,true);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy);
		$criteria->compare('ClientId',$this->ClientId);
		$criteria->compare('BrandId',$this->BrandId);
		$criteria->compare('CampaignId',$this->CampaignId);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RewardDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
