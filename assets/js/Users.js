/**
 * Front script for users controller.
 *
 */
var AddUser = function() {
	var self = this;

	self._request = null;
	self.targetUrl = BaseUrl + 'users/docreate';
	self.data = {};

	self.submitButton = 'new-user-submit';
	self.newUserFormId = 'new-user-form';
	self.newUserButtonId = 'new-user-button';

	self.errorNotifierContainerId = 'error-notifier-container';
	self.errorNotifierId = 'error-notifier';

	self.userCompanyInfoId = 'create-user-company-info';
	self.userAccessTypeDropdownId = 'Users_AccessType';
	self.userBirthdateId = 'Users_Birthdate';

	self.init = function()
	{
		// do stuff
		$(self.userCompanyInfoId).setStyle('display', 'none');
		self.addFormEvents();
	};

	self.addFormEvents = function()
	{
		$(self.submitButton).removeEvents();
		$(self.submitButton).addEvent('click', function(e) {
			e.preventDefault();
			self.data = $(self.newUserFormId).toQueryString().parseQueryString();

			// This format conversion is done only when a different date format is used in the form input.
			// For formats compatible with Date.parse(), see
			// 
			if(self.data['Users[Birthdate]']) {
				var t = new Date();
				self.data['Users[Birthdate]'] = t.parse(self.data['Users[Birthdate]']).format('%Y-%m-%d');
			}

			self.doCreate();			
		});


		$(self.userAccessTypeDropdownId).addEvent('change', function(e) {
			// e.preventDefault();
			if($(this).value==='SUPERADMIN')
			{
				$(self.userCompanyInfoId).setStyle('display', 'none');
			}
			else
			{
				$(self.userCompanyInfoId).setStyle('display', 'block');
			}
		});

		// Attach a datepicker to the input.
		self.datepicker(self.userBirthdateId);

	};

	// Pass any text input element ID to attach a datepicker.
	self.datepicker = function(elemId) {
		new Picker.Date($(elemId), {
			timePicker: false,
			useFadeInOut: !Browser.ie,
			// format: '%b/%e/%Y' // Output: Mar/1/1970
			format: '%Y-%m-%d'
		});
	};

	self.doCreate = function() {
		var self = this;

		self.data['ajax'] = 'create-user-form';

		if(!self._request || !self._request.isRunning())
		{
			self._request = new Request.JSON(
			{
				url: self.targetUrl,
				method: 'POST',
				data: self.data,
				onRequest: function() {
					new Element('p', {'id': self.errorNotifierId}).set('html','Processing...').inject(self.errorNotifierContainerId,'top');
				},
				onSuccess: function(response) {

					self.clearErrors();

					if(response.error)
					{
						new Element('p', {'id': self.errorNotifierId}).set('html',response.message).inject(self.errorNotifierContainerId,'top');

						if(response.fieldErrors) {
							$(self.errorNotifierContainerId).addClass('errorSummary');

							new Element('p', {'html': 'Please fix the following errors:'}).inject(self.errorNotifierContainerId,'top');
							var errorList = new Element('ul');

							if(response.fieldErrors.Users)
							{
								var modPrefix = 'Users';

								Object.each(response.fieldErrors.Users, function(errorMessage,col) {

								new Element('li').set('html',errorMessage).inject(errorList,'bottom');
								errorList.inject(self.errorNotifierContainerId,'bottom')

								self.showError(modPrefix + '_' + col, errorMessage);
								});
							}

							if(response.fieldErrors.Clients)
							{
								var modPrefix = 'Clients';

								Object.each(response.fieldErrors.Clients, function(errorMessage,col) {

								new Element('li').set('html',errorMessage).inject(errorList,'bottom');
								errorList.inject(self.errorNotifierContainerId,'bottom')

								self.showError(modPrefix + '_' + col, errorMessage);
								});
							}

						}
					}
					else
					{
						self.clearErrors();

						new Element('p', {'id': self.errorNotifierId}).set('html',response.message).inject(self.errorNotifierContainerId,'top');

						self.formClose();
						Users.initList();
					}
				},
				onError: function(response) {
					$(self.errorNotifierId).set('html',response);
				}
			}).send();
		}


	};

	self.showError = function(elemId, message) {
		new Element('div', {'class': 'errorMessage', 'html': message}).inject(elemId,'after');
		$(elemId).addClass('error');
		$$('label[for='+elemId+']').addClass('error');
	};

	self.clearErrors = function() {
		$$('form div.row .errorMessage').dispose();
		$$('div.form .error').removeClass('error');

		$('error-notifier-container').removeClass('errorSummary').set('html', null);
	};

	self.resetForm = function() {
		$(self.newUserFormId).reset();
	};

	self.formClose = function() {
		$(self.newUserFormId).setStyle('display','none');
		$(self.newUserButtonId).setStyle('display','block');
	}
};

var UpdateUser = function() {
	var self = this;

	self.init = function() {
		// do stuff
	};
};

var DeleteUser = function() {
	var self = this;

	self.init = function() {
		// do stuff
	};


};

var ListUsers = function() {
	var self = this;

	self._request = null;

	self.listUrl = BaseUrl + 'users/list';
	self.viewLinkUrl = BaseUrl + 'users/view/';

	self.page = null;
	self.data = [];
	self.totalData = null;
	self.totalRows = null;
	self.totalPages = 0;
	self.rowLower = '';
	self.rowUpper = '';
	self.pageTextInfo = '';
	self.search = null;

	self.listBodyContainerId = 'users-list-body';
	self.viewItemBtnId   = '';
	self.listSearchBtnId = '';

	self.pageInfoLowerClass = '.users-list-lower';
	self.pageInfoUpperClass = '.users-list-upper';
	self.pageInfoTotalClass = '.users-list-total';

	self.usersListRefreshLinkClass = '.users-list-refresh';
	self.usersListFirstLinkClass = '.users-list-first';
	self.usersListLastLinkClass = '.users-list-last';
	self.usersListNextLinkClass = '.users-list-next';
	self.usersListPrevLinkClass = '.users-list-prev';

	self.usersActionChangePw = 'view-user-action-changepw';
	self.usersActionDelete = 'view-user-action-delete';

	self.newUserContinerId = 'new-user-form';
	self.newUserButtonId = 'new-user-button';
	self.newUserCancelFormButtonId = 'new-user-cancel-button';

	self.init = function() {

		// This takes effect when user types the page directly into the 
		// address bar. The URI library gets the page and sets it to
		// self.page as the initial page.
		var requestUrl = new URI(window.location);
		if(!self.page)
			self.page = requestUrl.getData("page") || 1;

		if(!self.search && requestUrl.getData("search"))
			self.search = requestUrl.getData("search");

		// do stuff
		self.getData([
			self.render,
			self.addEvents
		]);
	};

	self.getData = function(callbacks) {
		var requestData = {
			page: self.page
		};

		if(self.search) requestData.search = self.search;

		if(!self._request || !self._request.isRunning()) {
			self._request = new Request.JSON({
				method: 'get',
				data: requestData,
				url: self.listUrl,
				onRequest: function() {
					// we need to set the correct page in the address bar
					// to reflect where we currently are in the list.
					var requestUrl = new URI(window.location);
					requestUrl.setData("page", self.page);

					// need to look into the contents of window.history
					// Need to know where "string" and "Navigate List" (Title) goes so
					// we get to set the values properly.
					window.history.pushState("string", "Navigate List", requestUrl.toString());

					// We change the content of the list upon the start of the request.
					$(self.listBodyContainerId).set('html', 'Getting the list...');
				},
				onSuccess: function(response) {
					if(response.error) {
						// do something
					}
					else {
						self.data         = response.data;
						self.page         = response.page;
						self.totalRows    = response.totalRows;
						self.totalData    = response.totalData;
						self.totalPages   = response.totalPages;
						self.pageTextInfo = response.pageTextInfo;
						self.rowLower     = response.rowLower;
						self.rowUpper     = response.rowUpper;

						if(callbacks) {
							Array.each(callbacks, function(callback) {
								callback();
							});
						}
					}
				},
				onError: function(response) {
					$(self.listBodyContainerId).set('text', 'An error was encountered while fetching data.');
				},
				onFailure: function() {
					$(self.listBodyContainerId).set('text', 'Failed to load data.');
				}
			}).send();
		}


	};

	self.render = function() {
		var content = '';

		// I don't know if this should be done here.
		// Just to initialize the display in case the
		// request returns nothing.
		$(self.listBodyContainerId).set('html', 'No record.');

		Array.each(self.data, function(row) {

			var linkAddress = self.viewLinkUrl + row.UserId;
			content += '<tr>\n';
			content += '<td><a href="'+ linkAddress +'">' + row.Username + '</a></td>\n';
			content += '<td>' + row.FirstName + '</td>\n';
			content += '<td>' + row.LastName + '</td>\n';
			content += '<td>' + row.AccessType + '</td>\n';
			content += '<td>' + row.Status + '</td>\n';
			content += '</tr>\n';
		});

		$$(self.pageInfoLowerClass).set('html', self.rowLower);
		$$(self.pageInfoUpperClass).set('html', self.rowUpper);
		$$(self.pageInfoTotalClass).set('html', self.totalData);

		$(self.listBodyContainerId).set('html', content);
	};

	self.addEvents = function() {
		$$(self.usersListRefreshLinkClass).removeEvents();
		$$(self.usersListRefreshLinkClass).addEvent('click', function(e) {
			e.preventDefault();
			self.init();
		});

		$$(self.usersListFirstLinkClass).removeEvents();
		$$(self.usersListFirstLinkClass).addEvent('click', function(e) {
			// do something
			if(self.page != 1)
			{
				self.page = 1;
				self.init();
			}
		});

		$$(self.usersListLastLinkClass).removeEvents();
		$$(self.usersListLastLinkClass).addEvent('click', function(e) {
			// do something
			if(self.page != self.totalPages)
			{
				if(self.totalPages) {
					self.page = self.totalPages;
				}
				else {
					self.page = 1;
				}
				self.init();
			}
		});

		$$(self.usersListNextLinkClass).removeEvents();
		$$(self.usersListNextLinkClass).addEvent('click', function(e) {
			// do something
			if(self.page < self.totalPages)
			{
				self.page = self.page + 1;
				self.init();
			}
		});

		$$(self.usersListPrevLinkClass).removeEvents();
		$$(self.usersListPrevLinkClass).addEvent('click', function(e) {
			// do something
			if(self.page > 1)
			{
				self.page = self.page - 1;
				self.init();
			}
		});

		$(self.newUserButtonId).removeEvents();
		$(self.newUserButtonId).addEvent('click', function(e) {
			e.preventDefault();
			$(self.newUserContinerId).setStyle('display', 'block');
			$(this).setStyle('display', 'none');
		});

		$(self.newUserCancelFormButtonId).removeEvents();
		$(self.newUserCancelFormButtonId).addEvent('click', function(e) {
			e.preventDefault();
			$(self.newUserContinerId).setStyle('display', 'none');
			$(self.newUserButtonId).setStyle('display', 'block');
		});

	};

	// self.toggleCreateButtonFunc = function( mode) {

	// 	if(mode == 'on') {
	// 		var id = self.newUserButtonId;
	// 		var displayMode = 'block';
	// 		var label = 'New User';
	// 	} else {
	// 		var id = self.newUserCancelFormButtonId;
	// 		var displayMode = 'none';
	// 		var label = 'Cancel';
	// 	}

	// 	$(id).removeEvents();
	// 	$(id).addEvent('click', function( e) {
	// 		e.preventDefault();
	// 		$(self.newUserContinerId).setStyle('display', displayMode);
	// 		this.set({'html': label, 'id': id});
	// 	});
	// };


};

var Users = {
	objList: null,
	objCreateForm: null,
	init: function() {
		var self = this;
		self.objList = new self.initList();
		self.objCreateForm = new self.initCreateForm();
	},
	initList: function() {
		var self = this;

		self.objList = new ListUsers();
		self.objList.init();
	},
	initCreateForm: function() {
		var self = this;

		self.objCreateForm = new AddUser();
		self.objCreateForm.init();
	}
};

window.addEvent('domready', function() {
	Users.init();
});