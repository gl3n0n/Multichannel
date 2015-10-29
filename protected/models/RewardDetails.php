<?php

/**
 * This is the model class for table "reward_details".
 *
 * The followings are the available columns in table 'reward_details':
 * @property string $RewardConfigId
 * @property string $RewardId
 * @property string $PointsId
 * @property integer $ClientId
 * @property string $Name
 * @property integer $Inventory
 * @property string $Limitations
 * @property string $Value
 * @property string $StartDate
 * @property string $EndDate
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
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
			array('RewardId, PointsId, ClientId, Name, Inventory, Limitations, Value, StartDate, EndDate', 'required'),
			array('ClientId, Inventory, CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('RewardId, PointsId', 'length', 'max'=>11),
			array('Name, Limitations', 'length', 'max'=>255),
			array('Value', 'length', 'max'=>50),
			array('Status', 'length', 'max'=>8),
            		array('Status', 'default', 'value'=>'PENDING', 'setOnEmpty'=>true),
			array('StartDate, EndDate, DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('RewardConfigId, RewardId, PointsId, ClientId, Name, Inventory, Limitations, Value, StartDate, EndDate, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
			'byClients'      =>array(self::BELONGS_TO, 'Clients',      'ClientId'),
			'byBrands'       =>array(self::BELONGS_TO, 'Brands',       'BrandId'),
			'byCampaigns'    =>array(self::BELONGS_TO, 'Campaigns',    'CampaignId'),
			'byChannels'     =>array(self::BELONGS_TO, 'Channels',     'ChannelId'),
			'byCreateUsers'  =>array(self::BELONGS_TO, 'Users',        'CreatedBy'),
			'byUpdateUsers'  =>array(self::BELONGS_TO, 'Users',        'UpdatedBy'),
			'byPointsSystem' =>array(self::BELONGS_TO, 'PointsSystem', 'PointsId'),
			'byRewards'      =>array(self::BELONGS_TO, 'RewardsList',   'RewardId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'RewardConfigId' => 'Reward Config',
			'Name' => 'Reward Name',
			'RewardId' => 'Reward',
			'PointsId' => 'Point System',
			'ClientId' => 'Client',
			'Inventory' => 'Inventory',
			'Limitations' => 'Limitations',
			'Value' => 'Value',
			'StartDate' => 'Start Date',
			'EndDate' => 'End Date',
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

		$criteria->compare('RewardConfigId',$this->RewardConfigId,true);
		$criteria->compare('RewardId',$this->RewardId,true);
		$criteria->compare('PointsId',$this->PointsId,true);
		$criteria->compare('ClientId',$this->ClientId);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Inventory',$this->Inventory);
		$criteria->compare('Limitations',$this->Limitations,true);
		$criteria->compare('Value',$this->Value,true);
		$criteria->compare('StartDate',$this->StartDate,true);
		$criteria->compare('EndDate',$this->EndDate,true);
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
	 * @return RewardDetails the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
