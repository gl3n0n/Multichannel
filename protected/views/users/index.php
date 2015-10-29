<?php
/* @var $this UsersController */

$this->breadcrumbs=array(
	'Users',
);
?>
<div>
	<h1>Users</h1>
</div>

<div id="error-notifier-container">
	<p id="error-notifier"></p>
</div>

<div id="users-list-container">
	<div>
		<button type="button" id="new-user-button">New User...</button>
		<?php echo $createForm; // we render the create form here. ?>
	</div>
	<div>
		<form action="" method="get">
			<fieldset>
				<legend>Search User</legend>
				<input type="text" name="search" id="list-search" placeholder="Username" title="Search users">
				<button type="submit">Search</button>
			</fieldset>
		</form>
	</div>
	<br>
<?php echo $this->renderPartial('_list-pagination'); ?>
	<div>
		<table>
			<thead>
				<tr>
					<th>UserID</th>
					<th>Username</th>
					<th>First Name</th>
					<th>Last Name</th>
					<th>Email</th>
					<th>Access Type</th>
					<th>Client Name</th>
					<th>Status</th>
					<th>Date Created</th>
					<th>Created By</th>
					<th>Date Updated</th>
					<th>Updated By</th>
				</tr>
			</thead>
			<tbody id="users-list-body">
				<tr><td colspan="2">No Record.</td></tr>
			</tbody>
		</table>
	</div>
<?php echo $this->renderPartial('_list-pagination'); ?>

</div>
