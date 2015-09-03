
<div>
	<div>
		<h3 id="view-user-header">User details - <?php echo $data['Username']; ?></h3>
		<p>
                    <a href="<?php echo Yii::app()->getBaseUrl(true); ?>/users/index">Back to list</a>
                    <a href="<?php echo Yii::app()->createAbsoluteUrl('users/edit', array('id'=>$data['UserId']?$data['UserId']:0)); ?>">Edit Details</a>
                    <?php
                    if(Yii::app()->user->AccessType=="SUPERADMIN")
                    {
                    ?>
                    <a href="<?php echo Yii::app()->createAbsoluteUrl('users/changepass', array('id'=>$data['UserId']?$data['UserId']:0)); ?>">Change Password</a>
                    <?php
                    }//change-pass
                    ?>
                </p>
	</div>
	<?php if(isset($data['error'])): ?>
	<br>
	<div style="color: red;"><?php echo $data['error']; ?></div>
	<br>
	<?php endif; ?>
	<div>
		<dl>
		<dt>Company Name</dt>
		<dd><i><?php echo $data['CompanyName'] ? $data['CompanyName'] : '&nbsp;'; ?></i></dd>
		
                        <dt>Username</dt>
                        <dd><i><?php echo $data['Username'] ? $data['Username'] : '&nbsp;'; ?></i></dd>

			<dt>First Name</dt>
			<dd><i><?php echo $data['FirstName'] ? $data['FirstName']: '&nbsp;'; ?></i></dd>

                        <dt>Middle Name</dt>
                        <dd><i><?php echo $data['MiddleName'] ? $data['MiddleName']: '&nbsp;'; ?></i></dd>

			<dt>Last Name</dt>
			<dd><i><?php echo $data['LastName'] ?$data['LastName']: '&nbsp;'; ?></i></dd>

                        <dt>Email</dt>
                        <dd><i><?php echo $data['Email'] ?$data['Email']: '&nbsp;'; ?></i></dd>

                        <dt>Contact Number</dt>
                        <dd><i><?php echo $data['ContactNumber'] ?$data['ContactNumber']: '&nbsp;'; ?></i></dd>

			<dt>AccessType</dt>
			<dd><i><?php echo $data['AccessType'] ?$data['AccessType']: '&nbsp;'; ?></i></dd>

			<dt>Status</dt>
			<dd><i><?php echo $data['Status'] ?$data['Status']: '&nbsp;'; ?></i></dd>

			<dt>Last Updated</dt>
			<dd><i><?php echo $data['DateUpdated'] ?$data['DateUpdated']: '&nbsp;'; ?></i></dd>
			
		</dl>
	</div>
	<?php if(!isset($data['error'])): ?>
	<!--
	<div>
		<div><h4>Options</h4></div>
		<div>
			<button onclick="javascript:alert('Feature unavailable');" id="view-user-action-changepw">Change Password</button>
			<button onclick="javascript:alert('Feature unavailable');" id="view-user-action-delete">Delete</button>
		</div>
	</div>
	-->
	<?php endif; ?>
</div>
