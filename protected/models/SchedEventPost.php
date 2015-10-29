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
class SchedEventPost extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'sched_event_post';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ClientId, Title, Description,AwardName,AwardType,Value', 'required'),
			array('ClientId,Value', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('StartDate', 'match', 'pattern'=>'/^\d{4}-\d{2}-\d{2}$/'),
			array('EndDate',   'match', 'pattern'=>'/^\d{4}-\d{2}-\d{2}$/'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('SchedId,ClientId,Title,Description,AwardName,AwardType,StartDate,EndDate,Value,PointsId,CouponId,RewardId,Status,DateCreated,CreatedBy,DateUpdated,UpdatedBy', 'safe', 'on'=>'search'),
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
			'sClients'    =>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'sCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'sUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
			'sPoint'      =>array(self::BELONGS_TO, 'PointsSystem', 'PointsId'),
			'sCoupon'     =>array(self::BELONGS_TO, 'CouponSystem', 'CouponId'),
			'sReward'     =>array(self::BELONGS_TO, 'RewardsList',  'RewardId'),
			
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
			'SchedId'     => 'Scheduled Event',
			'ClientId'    => 'Client Name',
			'Title'       => 'Title',
			'Description' => 'Description',
			'AwardName'   => 'Award Name',
			'AwardType'   => 'Award Type',
			'StartDate'   => 'Start Date',
			'EndDate'     => 'End Date',
			'Value'       => 'Value',
			'PointsId'    => 'Points',
			'CouponId'    => 'Coupon',
			'RewardId'    => 'Reward',
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
		$criteria->compare('Title',      $this->Title,true);
		$criteria->compare('Description',$this->Description,true);
		
		$criteria->compare('Status',     $this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',  $this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',  $this->UpdatedBy);
		$criteria->compare('StartDate',  $this->StartDate);
		$criteria->compare('EndDate',  $this->EndDate);
		
		$criteria->compare('AwardName',  $this->AwardName);
		$criteria->compare('AwardType',  $this->AwardType);
		$criteria->compare('Value',  $this->Value);
		
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
		'AwardName'      => array(
			'BIRTHDATE'  => 'BIRTHDATE' ,
			'ANNIVERSARY' => 'ANNIVERSARY',
			'NO_OF_ACTIONS' => 'NO_OF_ACTIONS',
			'NO_OF_POINTS' => 'NO_OF_POINTS',
			'FIRST_ACTION' => 'FIRST_ACTION',
			'GENDER' => 'GENDER',
			'NONE'   => 'NONE'  ,
			),
		'AwardType'      => array(
			'POINT'  => 'POINT' ,
			'COUPON' => 'COUPON',
			'REWARD' => 'REWARD',
			'NONE'   => 'NONE'  ,
			),
		);

	}
	
	public function getTimeList()
	{
		$ts = array();
		for($i=0; $i<24; $i++)
		{
			$k1 = sprintf("%02d:00:00",$i);
			$k2 = sprintf("%02d:30:00",$i);
			$v1 = sprintf("%02d:00",$i);
			$v2 = sprintf("%02d:30",$i);
			
			$ts["$k1"] = "$v1";
			$ts["$k2"] = "$v2";
		}
		return $ts;
	}
	public function getCustomerList()
	{

		$xtra1  = ''; 
		$xtra2  = '';
		$xtra3  = '';
		$xtra4  = '';
		
		//clientid
		if(Yii::app()->user->AccessType !== "SUPERADMIN") 
		{
			$tid   = addslashes(Yii::app()->user->ClientId);
			$xtra1 = " AND t.ClientId = '$tid' ";
		}
		
        	$list  = array();

		if(1){
		
			$rawSql = "
				SELECT  t.*
				FROM customers t
				WHERE 1=1 
					$xtra1 
					$xtra2
					$xtra3
					$xtra4
			";

			$rawData  = Yii::app()->db->createCommand($rawSql); 
			$rawCount = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
			$dataProvider    = new CSqlDataProvider($rawData, array(
				    'keyField' => 'CustomerId',
				    'totalItemCount' => $rawCount,
				    )
				);

		}
		
		foreach($dataProvider->getData() as $row)
		{
			$list[$row["CustomerId"]] = sprintf("%s %s - %s",
							$row["FirstName"],
							$row["LastName"],
							$row["Email"]);
		}
		//give
		return $list;
	}
}
