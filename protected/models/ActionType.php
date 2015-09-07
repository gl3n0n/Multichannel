<?php

/**
 * This is the model class for table "points".
 *
 * The followings are the available columns in table 'points':
 * @property string $ActionTypeId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 * @property string $From
 * @property string $To
 * @property string $Value
 * @property string $PointsAction
 * @property string $PointsCapping
 * @property string $ActionTypeLimit
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class ActionType extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'action_type';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('PointsId,ClientId,Value,PointsAction,PointsCapping,StartDate,EndDate', 
				'required'),
			array('PointsId,ClientId,Value,PointsCapping', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('Name',   'length', 'max'=>25),
			array('Value',        'match', 'pattern'=>'/^\d+$/'),
			array('PointsLimit',  'match', 'pattern'=>'/^\d+$/'),
			array('StartDate',    'match', 'pattern'=>'/^\d{4}-\d{2}-\d{2}$/'),
			array('EndDate',      'match', 'pattern'=>'/^\d{4}-\d{2}-\d{2}$/'),
			// The following rule is used by search().
			//ActiontypeId,PointsId,ClientId,Value,PointsAction,PointsCapping,PointsLimit,StartDate,EndDate,Status,DateCreated,CreatedBy,DateUpdated,UpdatedBy
			// @todo Please remove those attributes that should not be searched.
			array('ActiontypeId,Name,PointsId,ClientId,Value,PointsAction,PointsCapping,PointsLimit,StartDate,EndDate,Status,DateCreated,CreatedBy,DateUpdated,UpdatedBy', 
			'safe', 'on'=>'search'),
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
			'byClients'    =>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'byPoints'     =>array(self::BELONGS_TO, 'Points',  'PointsId'),
			'byCreateUsers'=>array(self::BELONGS_TO, 'Users',   'CreatedBy'),
			'byUpdateUsers'=>array(self::BELONGS_TO, 'Users',   'UpdatedBy'),
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
		//ActiontypeId,Name,PointsId,ClientId,Value,PointsAction,PointsCapping,PointsLimit,
		//StartDate,EndDate,Status,DateCreated,CreatedBy,DateUpdated,UpdatedBy

		return array(
			'ActiontypeId' => 'Id',
			'Name'         => 'Action Type Name',
			'ClientId'     => 'Client Name',
			'Value'        => 'Multiplier',
			'PointsId'     => 'Points System Name',
			'PointsAction' => 'Points Action',
			'PointsCapping'=> 'Points Capping',
			'PointsLimit'  => 'Points Limit',
			'StartDate'    => 'Start Date',
			'EndDate'      => 'End   Date',
			'Status'       => 'Status',
			'DateCreated'  => 'Date Created',
			'CreatedBy'    => 'Created By',
			'DateUpdated'  => 'Date Updated',
			'UpdatedBy'    => 'Updated By',
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

		$criteria->compare('ActiontypeId',    $this->ActiontypeId,true);
		$criteria->compare('ClientId',   $this->ClientId,true);
		$criteria->compare('Name',       $this->Name,true);
		$criteria->compare('Value',      $this->Value,true);
		$criteria->compare('PointsId',   $this->PointsId,true);
		$criteria->compare('PointsAction', $this->PointsAction,true);
		$criteria->compare('PointsCapping',$this->PointsCapping,true);
		$criteria->compare('PointsLimit',$this->PointsLimit,true);
		$criteria->compare('StartDate',  $this->StartDate);
		$criteria->compare('EndDate',    $this->EndDate);
		$criteria->compare('Status',     $this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',  $this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',  $this->UpdatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return ActionType the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
	
	public function getDropDownList()
	{
		return array(
		'PointsCapping'  => array(
				'DAILY'  => 'DAILY'   ,  
				'NONE'   => 'NONE'    ,
			),
		);

	}
}
