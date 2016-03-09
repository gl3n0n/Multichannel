<?php

class MgmtUsersController extends Controller
{
	public $extraJS;
	public $extraCSS;
	public $mainDivClass;
	public $modals;
	public $layout='//layouts/column2';
	
	public function filters()
	{
		// return the filter configuration for this controller, e.g.:
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',
				'users'=>array('@')
				),
			array('deny'),

		);
	}

	private function sendVerificationEmail($userid, $email)
	{
		$UserModel = Users::model()->findByPk($userid);
		$content = $this->renderPartial('email/verifyUser', array('username'=>$UserModel->username), true);

		$subject = 'Multichannel - Verify Your Account';

		$from_email = Yii::app()->params['adminEmail'];
		$sender = 'Accounts Admin';

		$name='=?UTF-8?B?'.base64_encode($sender).'?=';
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		$headers="From: $sender <{$from_email}>\r\n".
			"Reply-To: {$from_email}\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=UTF-8";

		mail($email,$subject,$content,$headers);
	}

	private function sendWelcomeEmail($userid)
	{
		$UserModel = Users::model()->findByPk($userid);
		$content = $this->renderPartial('email/welcomeUser', array('username'=>$UserModel->username), true);

		$subject = 'Multichannel - Account Verified';

		$from_email = Yii::app()->params['adminEmail'];
		$sender = 'Accounts Admin';

		$name='=?UTF-8?B?'.base64_encode($sender).'?=';
		$subject='=?UTF-8?B?'.base64_encode($subject).'?=';
		$headers="From: $sender <{$from_email}>\r\n".
			"Reply-To: {$from_email}\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=UTF-8";

		mail($email,$subject,$content,$headers);
	}

	private function createValidationLink($userid)
	{
		// The return should be like this:
		// http://multichannel/users/activate?id=#&vcode=hereherehere
		// 
		$UserModel = Users::model()->findByPk($userid);
		$code = sha1($UserModel->UserId . microtime() . $UserModel->username);
		return $code;
	}

	public function actionActivate()
	{
		// There are two routines:
		// 1. User clicks on the link sent with the email
		// 2. User manually enters his verification code.

		if($_GET['vcode'])
		{
			// This is routine 1.
		}
		else
		{
			// This is routine 2.
			// User will be asked his username, email and verification code.
			$FormModel = new FormActivate;
			$this->render('activate');
		}
	}

	// Validation rules? Look in the model classes.
	public function actionDoCreate()
	{
		if(isset($_POST['Users'], $_POST['Clients']))
		{
			$response = array();
			$transaction = Yii::app()->db->beginTransaction();
			$UserSaved = false;
			$ValidUser = false;
			$ClientSaved = true;

			try {
				$UsersModel = new Users;
				$UsersModel->attributes = $_POST['Users'];
				$UsersModel->DateCreated = new CDbExpression('NOW()');
				$UsersModel->DateUpdated = new CDbExpression('NOW()');
				$UsersModel->CreatedBy = Yii::app()->user->id;
				
				if($UsersModel->AccessType === 'SUPERADMIN') 
				{
					$ClientSaved = true; // Bypass the client creation
					$UsersModel->ClientId = 0;
				}
				else
				{
					$ClientSaved = true; // Bypass the client creation
					$ClientsModel = new Clients;
					$ClientsModel->attributes = $_POST['Clients'];
					$UsersModel->ClientId = $ClientsModel->ClientId;
				}

				$UserValidated = $UsersModel->validate();
				
				// chk-uniq-user
				$userNameMod = $UsersModel->userNameExistsNew($UsersModel->Username);
				if( $userNameMod )
				{
					$UsersModel->addError('Username', 'User already exists. ');
					$UserValidated = 0;
				}
				//chk uniq-email
				$emailModel = $UsersModel->emailExistsNew($UsersModel->Email);
				if( $emailModel )
				{
					$UsersModel->addError('Email', 'Email already exists. ');
					$UserValidated = 0;
				}
				//chk client-id
				if($UsersModel->AccessType === 'ADMIN') 
				{
					$clientId = trim($_POST['Clients']['ClientId']);
					$ClientsModel->ClientId = $clientId;
					$UsersModel->ClientId   = $clientId;
					
					if($clientId == '' or $clientId == 0)
					{
						$ClientsModel->addError('ClientId', 'Must select a valid Client. ');
						$UserValidated = 0;
					}
				}
				// echo "<hr> validate: $UserValidated<hr>" . @var_export($UsersModel,true);
				// exit;
				// Encryption is done in the model, beforeSave()
        		        //$UsersModel->Password = md5($_POST['Users']['Password']);

				if($UserValidated && $ClientSaved)
				{
					$UsersModel->save(false);
					$response = array(
						'message'=>'User successfully created.',
					);
					$utilLog = new Utils;
					$utilLog->saveAuditLogs();

				}
				else
				{
					if($UsersModel->AccessType === 'SUPERADMIN') 
					{
						$fieldErrors = array(
							'Users' => $UsersModel->errors
						);
					}
					else
					{
						$fieldErrors = array(
							'Clients' => $ClientsModel->errors,
							//'Users' => $UsersModel->errors
							'Users' => $UsersModel->errors
						);
					}
					

					$response = array(
						'error'=>true,
						'message'=>'User not saved.',
						'fieldErrors'=>$fieldErrors,
					);
				}
				
				$transaction->commit();
				Yii::app()->utils->sendJSONResponse($response);

			} catch (CDbException $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'We have encountered an error while saving your data.',
					'data'=>$ex->getMessage(),				));
			} catch (Exception $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'Oops! Something went wrong. Please try again in a few minutes.',
					'data'=>$ex->getMessage(),
				));
			}
		}
		else
		{
			Yii::app()->utils->sendJSONResponse(array(
				'hasError'=>true,
				'message'=>'Invalid action',
			));
		}
	}

	// Validation rules? Look in the model classes.
	public function ORIGactionDoCreate()
	{
		if(isset($_POST['Users'], $_POST['Clients']))
		{
			$response = array();
			$transaction = Yii::app()->db->beginTransaction();
			$UserSaved = false;
			$ValidUser = false;
			$ClientSaved = true;

			try {
				$UsersModel = new Users;
				$UsersModel->attributes = $_POST['Users'];
				$UsersModel->DateCreated = new CDbExpression('NOW()');
				$UsersModel->DateUpdated = new CDbExpression('NOW()');
				$UsersModel->CreatedBy = Yii::app()->user->id;
				
				if($UsersModel->AccessType === 'SUPERADMIN') 
				{
					$ClientSaved = true; // Bypass the client creation
				}
				else
				{
					$ClientsModel = new Clients;
					$ClientsModel->attributes = $_POST['Clients'];
					$ClientsModel->DateCreated = new CDbExpression('NOW()');
					$ClientsModel->DateUpdated = new CDbExpression('NOW()');
					$ClientsModel->CreatedBy = Yii::app()->user->id;

					$ClientSaved = $ClientsModel->save();

					if($ClientSaved) 
					{
						$UsersModel->ClientId = $ClientsModel->ClientId;
					}
				}

				$UserValidated = $UsersModel->validate();

				// Encryption is done in the model, beforeSave()
                //$UsersModel->Password = md5($_POST['Users']['Password']);

				if($UserValidated && $ClientSaved)
				{
					$UsersModel->save(false);
					$response = array(
						'message'=>'User successfully created.',
					);
				}
				else
				{
					if($UsersModel->AccessType === 'SUPERADMIN') 
					{
						$fieldErrors = array(
							'Users' => $UsersModel->errors
						);
					}
					else
					{
						$fieldErrors = array(
							'Clients' => $ClientsModel->errors,
							//'Users' => $UsersModel->errors
							'Users' => $UsersModel->errors
						);
					}
					

					$response = array(
						'error'=>true,
						'message'=>'User not saved.',
						'fieldErrors'=>$fieldErrors,
					);
				}
				
				$transaction->commit();
				Yii::app()->utils->sendJSONResponse($response);

			} catch (CDbException $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'We have encountered an error while saving your data.',
					'data'=>$ex->getMessage(),				));
			} catch (Exception $ex) {
				$transaction->rollback();
				Yii::app()->utils->sendJSONResponse(array(
					'hasError'=>true,
					'message'=>'Oops! Something went wrong. Please try again in a few minutes.',
					'data'=>$ex->getMessage(),
				));
			}
		}
		else
		{
			Yii::app()->utils->sendJSONResponse(array(
				'hasError'=>true,
				'message'=>'Invalid action',
			));
		}
	}

	
	public function actionCreate()
	{
		$UsersModel   = new Users;
		$ClientsModel = new Clients;

		$clientsID = Users::model()->findByPk(Yii::app()->user->id)->ClientId;
		
		if(Yii::app()->user->AccessType === "SUPERADMIN" ) 
		{
			$_clients = Clients::model()->active()->findAll();
		}
		else 
		{
			$_clients = Clients::model()->findAll(array(
					'select'=>'ClientId, CompanyName', 'condition' => 'ClientId='.$clientsID.''));
		}
		
		$clients = array();
		foreach($_clients as $row) {
			$clients[$row->ClientId] = $row->CompanyName;

		}
		
		
		if( isset($_POST['Users'],$_POST['Clients']) )
		{
			$response = array();
			$transaction = Yii::app()->db->beginTransaction();
			$UserSaved = false;
			$ValidUser = false;
			$ClientSaved = true;

			try {
				$UsersModel = new Users;
				$UsersModel->attributes  = $_POST['Users'];
				$UsersModel->DateCreated = new CDbExpression('NOW()');
				$UsersModel->DateUpdated = new CDbExpression('NOW()');
				$UsersModel->CreatedBy   = Yii::app()->user->id;
				
				if($UsersModel->AccessType === 'SUPERADMIN') 
				{
					$ClientSaved = true; // Bypass the client creation
					$UsersModel->ClientId = 0;
				}
				else
				{
					$ClientSaved  = true; // Bypass the client creation
					$ClientsModel = new Clients;
					$ClientsModel->attributes = $_POST['Clients'];
					$UsersModel->ClientId     = $ClientsModel->ClientId;
				}

				$UserValidated = $UsersModel->validate();
				
				// chk-uniq-user
				$userNameMod = $UsersModel->userNameExistsNew($UsersModel->Username);
				if( $userNameMod )
				{
					$UsersModel->addError('Username', 'User already exists. ');
					$UserValidated = 0;
				}
				//chk uniq-email
				$emailModel = $UsersModel->emailExistsNew($UsersModel->Email);
				if( $emailModel )
				{
					$UsersModel->addError('Email', 'Email already exists. ');
					$UserValidated = 0;
				}
				//chk client-id
				if($UsersModel->AccessType === 'ADMIN') 
				{
					$clientId = trim($_POST['Clients']['ClientId']);
					$ClientsModel->ClientId = $clientId;
					$UsersModel->ClientId   = $clientId;
					
					if($clientId == '' or $clientId == 0)
					{
						$ClientsModel->addError('ClientId', 'Must select a valid Client. ');
						$UserValidated = 0;
					}
				}
				
				if($UserValidated && $ClientSaved)
				{
					$UsersModel->save(false);
					$response = array(
						'message'=>'User successfully created.',
					);
					//good
					if(true)
					{
						$utilLog = new Utils;
						$utilLog->saveAuditLogs();
						$this->redirect(array('view','id'=>$UsersModel->UserId));
					}
				}
				else
				{
					if($UsersModel->AccessType === 'SUPERADMIN') 
					{
						$fieldErrors = array(
							'Users' => $UsersModel->errors
						);
					}
					else
					{
						$fieldErrors = array(
							'Clients' => $ClientsModel->errors,
							//'Users' => $UsersModel->errors
							'Users' => $UsersModel->errors
						);
					}
					

					$response = array(
						'error'       => true,
						'message'     => 'User not saved.',
						'fieldErrors' => $fieldErrors,
					);
				}
				
				$transaction->commit();
		
				
			} catch (CDbException $ex) {
				$transaction->rollback();
				$UsersModel->addError('Username', 'We have encountered an error while saving your data.');
			} catch (Exception $ex) {
				$transaction->rollback();
				$UsersModel->addError('Username', 'Oops! Something went wrong. Please try again in a few minutes.');
			}
		}

		if(0){
		echo 'x-POST#<hr><pre>'.@var_export($_POST,1).'</pre>';
		exit;
		}
		
		$this->render('create', array(
			'model'        => $UsersModel, 
			'client_list'  => $clients,
			'submodel'     => $ClientsModel,
			'clientsModel' => $ClientsModel));
	}

	public function actionDelete()
	{
		$id = Yii::app()->request->getQuery('id');

		Yii::app()->utils->sendJSONResponse(array('id' => $id));
		$utilLog = new Utils;
		$utilLog->saveAuditLogs();
		$rowCount = Users::model()->findByPk($id)->delete();
		// $this->render('delete');
	}

		/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
	
		$criteria = new CDbCriteria;
	
		//byName
		$afilter  = '';
		$byName   = trim(Yii::app()->request->getParam('byName'));
		if(strlen($byName))
		{
		    $t = addslashes($byName);
			$afilter = " AND ( t.FirstName  LIKE '%$t%' OR 
							   t.MiddleName LIKE '%$t%' OR
							   t.LastName   LIKE '%$t%' OR
							   t.Username   LIKE '%$t%'
							  ) ";
		}			
	
		//byStatusType
		$bfilter  = '';
		$byStatusType = trim(Yii::app()->request->getParam('byStatusType'));
		if(strlen($byStatusType))
		{
			$t = addslashes($byStatusType);
			$bfilter  = "  AND (  t.Status = '$t' )  ";
		}	
		
		//byAccessType
		$cfilter  = '';
		$byAccessType = trim(Yii::app()->request->getParam('byAccessType'));
		if(strlen($byAccessType))
		{
			$t = addslashes($byAccessType);
			$cfilter  = " AND (  t.AccessType = '$t' )  ";
		}			
		$dfilter  = '';
		if(1)
		{
				//by client
				if(Yii::app()->utils->getUserInfo('AccessType') === 'SUPERADMIN' and isset($_REQUEST['Clients'])) 
				{
					$byClient = $_REQUEST['Clients']['ClientId'];
					if($byClient>0)
					{
						$t        = addslashes($byClient);
						$dfilter  = " AND (  t.ClientId = '$t' )  ";
					}			
				}

				
				//normal
				if(Yii::app()->utils->getUserInfo('AccessType') !== 'SUPERADMIN') 
				{
					$t        = addslashes(Yii::app()->user->ClientId);
					$dfilter  = " AND (  t.ClientId = '$t' )  ";
				}
		}

		//run
		$sort    = new CSort;
		$sort->attributes = array('*');
		$rawSql   = "
						select
								t.UserId, 
								t.ClientId, 
								t.Username, 
								t.FirstName, 
								t.LastName, 
								t.Email, 
								t.AccessType, 
								t.Status, 
								t.DateCreated, 
								t.CreatedBy, 
								t.DateUpdated, 
								t.UpdatedBy,
								IFNULL((
								select a.CompanyName
								from clients a
								where
								1=1
								and
								a.ClientId = t.ClientId
								LIMIT 1
								),'') as CompanyName, 
								IFNULL((
								select a.Username
								from users a
								where
								1=1
								and
								a.UserId = t.UpdatedBy
								LIMIT 1
								),'') as UpdatedBy2,
								IFNULL((
								select a.Username
								from users a
								where
								1=1
								and
								a.UserId = t.CreatedBy
								LIMIT 1
								),'') as CreatedBy2
						from
						users t
					WHERE 1=1
						$afilter
						$bfilter
						$cfilter
						$dfilter
				ORDER BY 
						t.UserId DESC
				";

				$rawData      = Yii::app()->db->createCommand($rawSql); 
				$rawCount     = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
				$dataProvider = new CSqlDataProvider($rawData, array(
						'keyField'       => 'UserId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);
				
		
				$this->render('index',array(
					'dataProvider'=>$dataProvider,
				));
	}


        public function actionEdit($id)
        {
            if($_POST) {
                $UserModel = array();
                $form_fields = array('FirstName','MiddleName','LastName','Email','ContactNumber','Status','Password','ConfirmPassword');

                //@var_dump($_POST['Users']); exit;

                foreach($_POST['Users'] as $idx => $val) {
                    if( in_array($idx, $form_fields)) $UserModel[$idx] = $val;
                }

                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, FirstName, MiddleName, LastName, Gender, Birthdate, ContactNumber, Address, Email, Username, AccessType, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy'));

                $model->attributes = $UserModel;
                $model->UpdatedBy = Yii::app()->user->id;
                $model_validated = $model->validate();
		
		if($model_validated)
                {
                    if($model->save(false))
                    {
			$utilLog = new Utils;
			$utilLog->saveAuditLogs();

                    	Yii::app()->getUser()->setFlash('user-update-success', 'User updated.');
                    }
                    else
                    {
                    	Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes.');
                    }
                }

            	$model->Password = '';
            	$model->ConfirmPassword = '';
                    
                $this->render('_formUpdate', array(
                    'model' => $model,
                ));

            } else {
                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, Username, FirstName, MiddleName, LastName, ContactNumber, Email, AccessType, Status, DateUpdated'));

                if($model) {
                    $model->DateUpdated = date('F j, Y g:i A', strtotime($model->DateUpdated));
                } else {
                    echo 'Record not found.';
                    Yii::app()->end();
                }

                $this->render('_formUpdate', array(
                    'model' => $model,
                ));
            }
        }

 	public function actionChangepass($id)
        {
            $is_superadmin = Users::model()->findByPk(Yii::app()->user->id)->AccessType === 'SUPERADMIN';
            if(!$is_superadmin)
            {
            	Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes. (No privileges)');
            }
            
            
            if($_POST && $is_superadmin) {
                $UserModel   = array();
                $form_fields = array('FirstName','MiddleName','LastName','Email','ContactNumber','Status','Password','ConfirmPassword');
				$form_fields = array('Password','ConfirmPassword');
                //echo @var_export($_POST['Users'],true); exit;

                foreach($_POST['Users'] as $idx => $val) {
                    if( in_array($idx, $form_fields)) $UserModel[$idx] = trim($val);
                }

                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, FirstName, MiddleName, LastName, Gender, Birthdate, ContactNumber, Address, Email, Username, AccessType, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy'));

                $model->attributes = $UserModel;
                $model->UpdatedBy = Yii::app()->user->id;
                $model_validated = $model->validate();
		
		//try to check
		$p1 = trim($_POST['Users']['Password']);
		$p2 = trim($_POST['Users']['ConfirmPassword']);
		
		if(!strlen($p1) or !strlen($p1) )
		{
			Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes. (Please check the password/confirm-password)');
			$model_validated = false;
		}
		
		if($model_validated)
                {
                    if($model->save(false))
                    {
						$utilLog = new Utils;
						$utilLog->saveAuditLogs();
                    	Yii::app()->getUser()->setFlash('user-update-success', "User's password updated.");
						$this->redirect(array('view','id'=>$model->UserId));
                    }
                    else
                    {
                    	Yii::app()->getUser()->setFlash('user-update-error', 'Failed to save changes.');
                    }
                }
                

            	$model->Password = '';
            	$model->ConfirmPassword = '';
                    
                $this->render('_formUpdateChangePass', array(
                    'model' => $model,
                ));

            } else {
                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, Username, FirstName, MiddleName, LastName, ContactNumber, Email, AccessType, Status, DateUpdated'));

                if($model) {
                    $model->DateUpdated = date('F j, Y g:i A', strtotime($model->DateUpdated));
                } else {
                    echo 'Record not found.';
                    Yii::app()->end();
                }

                $this->render('_formUpdateChangePass', array(
                    'model' => $model,
                ));
            }
            
        }
	
	public function actionView($id)
	{
		$model = $this->loadModel($id);
		$this->render('view',array(
			'model'=> $model,
		));
	}
	
/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer $id the ID of the model to be loaded
	 * @return Customers the loaded model
	 * @throws CHttpException
	 */
	public function loadModel($id)
	{
		$model=Users::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function actionUpdate()
	{
		$id       = Yii::app()->request->getQuery('id');
		$user     = Users::model()->findByPk($id);
		$old_attrs= @var_export($user->attributes,1);
		
		if(isset($_POST['Users']))
		{
			try {
				
				$UserModel   = array();
                $form_fields = array('FirstName','MiddleName','LastName','Email','ContactNumber','Status','Password','ConfirmPassword');
				foreach($_POST['Users'] as $idx => $val) 
				{
                    if( in_array($idx, $form_fields)) 
						$UserModel[$idx] = $val;
                }

                $model = Users::model()->findByPk($id, array('select'=>'UserId, ClientId, FirstName, MiddleName, LastName, Gender, Birthdate, ContactNumber, Address, Email, Username, AccessType, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy'));

                $model->attributes = $UserModel;
				$model->setAttribute("DateUpdated", new CDbExpression('NOW()'));
				$model->setAttribute("UpdatedBy", Yii::app()->user->id);
				
				$new_attrs         = @var_export($model->attributes,1);
				$audit_logs        = sprintf("OLD:\n\n%s\n\nNEW:\n\n%s",$old_attrs,$new_attrs);
				$model_validated   = $model->validate();
				
				if($model_validated)
                {
                    if($model->save(false))
                    {
						$utilLog = new Utils;
						$utilLog->saveAuditLogs(null,$audit_logs);
						$this->redirect(array('view','id'=>$model->UserId));
					}
				}

				$user->addError('UserId', 'Failed to save data.');
				
			}
			catch(CDbException $ex) {
				$user->addError('UserId', 'Failed to save data. An error has occured.');
			}
			catch(Exception $ex) {
				$user->addError('UserId', 'Failed to save data. Something went wrong.');
			}
		}

		$this->render('_formUpdate', array(
						'model'    => $user,
                ));
	}

	public function actionList()
	{
		$limit  = Yii::app()->params['list']['perPage'];
		$page   = (int) Yii::app()->request->getParam('page', '1');
		$offset = ($page - 1) * $limit;
		$search = Yii::app()->request->getParam('search');
		$arr    = array('error'=>false, 'message'=>'Ack');

		try {
			$criteria = new CDbCriteria;
			/*
			$criteria->select= array(
							'UserId', 
							'ClientId', 
							'Username', 
							'FirstName', 
							'LastName', 
							'Email', 
							'AccessType', 
							'Status', 
							'DateCreated', 
							'CreatedBy', 
							'DateUpdated', 
							'UpdatedBy',
							'(
								select a.Username
								from users a
								where
								1=1
								and
								a.UserId = t.UpdatedBy
								LIMIT 1
							) as UpdatedBy2'
							);*/
			//$criteria->with=array('clientInfo'=>array('select'=>'CompanyName'));
			if($search) $criteria->compare('Username', $search, true);

			$arr['totalData'] = Users::model()->count($criteria);
			$arr['totalPages'] = ceil($arr['totalData']/$limit);
			$arr['page'] = $page;

			$criteria->offset=$offset;
			$criteria->limit=$limit;

			if(1){
				$sort    = new CSort;
				$sort->attributes = array('*');
				
				$sfilter = '';
				if(strlen($search))
				{
					$t       = addslashes($search);
					$sfilter = " AND t.UserName = '$t' ";
				}
				$rawSql   = "
						select
								t.UserId, 
								t.ClientId, 
								t.Username, 
								t.FirstName, 
								t.LastName, 
								t.Email, 
								t.AccessType, 
								t.Status, 
								t.DateCreated, 
								t.CreatedBy, 
								t.DateUpdated, 
								t.UpdatedBy,
								IFNULL((
								select a.CompanyName
								from clients a
								where
								1=1
								and
								a.ClientId = t.ClientId
								LIMIT 1
								),'') as CompanyName, 
								IFNULL((
								select a.Username
								from users a
								where
								1=1
								and
								a.UserId = t.UpdatedBy
								LIMIT 1
								),'') as UpdatedBy2,
								IFNULL((
								select a.Username
								from users a
								where
								1=1
								and
								a.UserId = t.CreatedBy
								LIMIT 1
								),'') as CreatedBy2
						from
						users t
					WHERE 1=1
						$sfilter 
				ORDER BY t.UserId DESC
				";

				$rawData      = Yii::app()->db->createCommand($rawSql); 
				$rawCount     = Yii::app()->db->createCommand('SELECT COUNT(1) FROM (' . $rawSql . ') as count_alias')->queryScalar(); //the count
				$dataProvider = new CSqlDataProvider($rawData, array(
						'keyField'       => 'UserId',
						'totalItemCount' => $rawCount,
						'sort'           => $sort,
						)
				);
				foreach($dataProvider->getData() as $rec)
				{
					$arr['data'][] = $rec;
				}

			}
			//$arr['data'] = Users::model()->inMyCompany()->findAll($criteria);
			//$arr['data'] = Users::model()->with('clientInfo')->findAll($criteria);
			//$arr['data'] = Users::model()->findAll($criteria);
			if(0){
			echo '<pre>';
			echo @var_export($criteria,1);
			exit();
			}
			$arr['totalRows'] = count($arr['data']);

			if($arr['totalRows']) {
				$arr['rowLower'] = $offset + 1;
				$arr['rowUpper'] = $offset + $arr['totalRows'];
			}
			else {
				$arr['totalPages'] = 0;
				$arr['rowLower'] = 0;
				$arr['rowUpper'] = 0;
			}
		}
		catch(CDbException $ex) {
			$arr['error'] = true;
			$arr['message'] = 'CDbException: ' . $ex->getMessage();
		}
		catch(Exception $ex) {
			$arr['error'] = true;
			$arr['message'] = 'Exception' . $ex->getMessage();
		}

		Yii::app()->utils->sendJSONResponse( $arr);
	}
	// Uncomment the following methods and override them if needed

}
