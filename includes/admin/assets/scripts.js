jQuery(document).ready(function($) {
	$('.codereadr-event-custom-integration').on('click', function() {
		$('.codereadr-modal').addClass('show');
	});

	$(".codereadr-modal__close").on("click", function() {
		$('.codereadr-modal').removeClass('show');
	});

	$(".codereadr-button-add-new-user").on("click", function() {
		$(".codereadr-users-form-screen").addClass('show');
		$(".codereadr-users-listing-screen").removeClass("show");
	});

	$('.codereadr-admin-settings-page__buttons .codereadr-button-secondary').on(
	 	'click',
		function () {
			$('.codereadr-admin-settings-page__api-key-input').val('');
			$('.codereadr-admin-settings-page__api-key-input').removeAttr(
				'disabled'
			);
		}
	);

	function getActionType() {
		var actionName =  $(
			'.codereadr-service-action-select option:selected'
		).data('action-name');
		return registeredActions[actionName];
	}
	// Add service.
	$('.codereadr-add-new-service').on('click', function () {
		$(".codereadr-service-modal .codereadr-modal__header h3").text("Add a service");
		$('.codereadr-service-modal .codereadr-modal__content').scrollTop(0);
		$('.codereadr-service-modal').addClass('show');
		$('.codereadr-service-database-unique-id').val('');
		$('.codereadr__codereadr-service-id').val('');
		$('.codereadr-service-integration-select').val($(".codereadr-service-integration-select option:first").val());
		$('.codereadr-service-action-select').val($(".codereadr-service-action-select option:first").val());
		$('.codereadr-service-name').val('');
		$('.codereadr-service-response-text').val('');
		
		var actionTypeData = getActionType();
		if(actionTypeData) {
			fillActionTypeFields(actionTypeData);
			fillInvalidConditions(
				actionTypeData.default_invalid_conditions,
				actionTypeData.optional_invalid_conditions
			);
		}
	});

	// Edit service.
	$('.codereadr-admin-services-action__edit').on('click', function () {
		$(".codereadr-service-modal .codereadr-modal__header h3").text("Edit service");
		$('.codereadr-service-modal .codereadr-modal__content').scrollTop(0);
		var serviceId = $(this).data('service-id');
		var service = codereadrInDBservices.find(service => parseInt(service.codereadr_service_id) === parseInt(serviceId) );
		$('.codereadr-service-modal').addClass('show');
		// In WordPress database service id.
		$('.codereadr-service-database-unique-id').val(service['ID']);
		$('.codereadr-service-integration-select').val(service.integration_slug);
		$('.codereadr-service-integration-select').trigger('change');
		$('.codereadr-service-action-select').val(service.action_name);
		$('.codereadr-service-action-select').trigger('change');

		// Codereader remote saved service id at Codereadr server.
		$('.codereadr__codereadr-service-id').val(service.codereadr_service_id);
		$('.codereadr-service-name').val(service.title);
		$('.codereadr-service-response-text').val(
			service.meta.success_response_txt
		);
		fillInvalidConditions(
			service.meta.default_invalid_conditions,
			service.meta.optional_invalid_conditions
		);
	});

	// Delete service.
	$('.codereadr-delete-service:not(.is-deleting)').on('click', function () {

		var serviceId = $(this).data('service-id');
		var service = codereadrInDBservices.find(service => parseInt(service.codereadr_service_id) === parseInt(serviceId) );

		var codereadr_service_id = service.codereadr_service_id;
        if(confirm("Are you sure you want to delete this service?")) {
			$(this).addClass("is-deleting");
			$(this).text('Deleting');	
            $.ajax({
                url: codeReadr.ajaxUrl,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'codereadr_delete_service',
                    'codereadr-service-remote-id': codereadr_service_id,
                    'service-database-unique-id': serviceId,
					nonce: codeReadr.nonce
                },
                success: function (data) {
                    location.reload();
                },
            });
        }
	});

	// Click on cancel button.
	$('.codereadr-service-modal .codereadr-button-secondary').on(
		'click',
		function () {
			$('.codereadr-service-modal').removeClass('show');
			$(
				'.codereadr-service-modal .codereadr-button-primary'
			).removeClass('is-saving');
		}
	);

	// Filling merge tags and invalid conditions at initial start.
	var actionTypeData = getActionType();
	if(actionTypeData) {
		fillActionTypeFields(actionTypeData);
		fillInvalidConditions(
			actionTypeData.default_invalid_conditions,
			actionTypeData.optional_invalid_conditions
		);
	}

	// On Action select change.
	$('.codereadr-service-action-select').on('change', function () {
		var optionSelected = $(this).find(':selected');
		if(optionSelected) {
			var actionTypeData = getActionType();
			if(actionTypeData) {
				fillActionTypeFields(actionTypeData);
				fillInvalidConditions(
					actionTypeData.default_invalid_conditions,
					actionTypeData.optional_invalid_conditions
				);
			}
		}
	});

	// Filling the appropriate merge tags depending on the current selected action.
	function fillActionTypeFields(actionTypeData) {
		if(!actionTypeData) return;
		console.log(actionTypeData);
		$(".codereadr-service-action-description").text(actionTypeData.description);
		var allowableMergeTags = actionTypeData.allowable_merge_tags;
		$('.codereadr-service-action__custom-merge-tags').empty();
		if (Object.keys(allowableMergeTags).length) {
			$('.codereadr-service-action__custom-merge-tags').append(
				'<strong> Custom merge tags related with the current action: </strong> <br />'
			);
			Object.values(allowableMergeTags).forEach(function (mergeTag) {
				$('.codereadr-service-action__custom-merge-tags').append(
					mergeTag.tag + ' ' + mergeTag.description + '.<br>'
				);
			});
		}
	}

	// Filling the invalid conditions.
	function fillInvalidConditions(
		defaultInvalidConditions,
		optionalInvalidConditions
	) {
		var actionTypeData = getActionType();
		$('.codereadr-service-action__optional-invalid-conditions').empty();
		if (Object.keys(optionalInvalidConditions).length) {
			$('.codereadr-service-action__optional-invalid-conditions').append(
				'<h3 style="color: #fff">Optional Invalid Conditions </h3>'
			);
			Object.keys(optionalInvalidConditions).forEach(function (
				invalidConditionkey
			) {
				var invalidCondition =
					optionalInvalidConditions[invalidConditionkey];
				var checked =
					invalidCondition.checkbox ||
					invalidCondition.default === true
						? 'checked'
						: '';
				var responseClass = checked ? 'show' : '';
				var title =
					actionTypeData['optional_invalid_conditions'][
						invalidConditionkey
					].title;
				$(
					'.codereadr-service-action__optional-invalid-conditions'
				).append(
					"<div class='codereadr-service-action__optional-invalid-condition'><h4><input " +
						checked +
						" name='optional_invalid_conditions[" +
						invalidConditionkey +
						"][checkbox]' type='checkbox' value='" +
						(invalidCondition.default !== undefined
							? invalidCondition.default
							: invalidCondition.checkbox) +
						"' />" +
						title +
						"</h4><div class='codereadr-service-action__optional-invalid-condition-response " +
						responseClass +
						"'><p>Response Text</p> <textarea class='codereadr-service-action__optional-invalid-condition-response-textarea' name='optional_invalid_conditions[" +
						invalidConditionkey +
						"][response_text]'>" +
						(invalidCondition.response_text
							? invalidCondition.response_text
							: invalidCondition.default_response_text) +
						'</textarea> </div></div>'
				);
			});
		}

		$('.codereadr-service-action__default-invalid-conditions').empty();
		if (Object.keys(defaultInvalidConditions).length) {
			$('.codereadr-service-action__default-invalid-conditions').append(
				'<h3 style="color: #fff"> Default Invalid Conditions </h3>'
			);
			Object.keys(defaultInvalidConditions).forEach(function (
				invalidConditionkey
			) {
				var invalidCondition =
					defaultInvalidConditions[invalidConditionkey];
				var title =
					actionTypeData['default_invalid_conditions'][
						invalidConditionkey
					].title;
				$(
					'.codereadr-service-action__default-invalid-conditions'
				).append(
					"<div class='codereadr-service-action__default-invalid-condition'><h4>" +
						title +
						"</h4><div class='codereadr-service-action__default-condition-response'><p>Response Text</p> <textarea class='codereadr-service-action__default-invalid-condition-response-textarea' name='default_invalid_conditions[" +
						invalidConditionkey +
						"][response_text]'>" +
						(invalidCondition.response_text
							? invalidCondition.response_text
							: invalidCondition.default_response_text) +
						'</textarea> </div></div>'
				);
			});
		}
	}

	// On optional invalid condition checkbox change.
	$(document).on(
		'change',
		'.codereadr-service-action__optional-invalid-condition input',
		function () {
			if ($(this).is(':checked')) {
				$(this)
					.closest(
						'.codereadr-service-action__optional-invalid-condition'
					)
					.find(
						'.codereadr-service-action__optional-invalid-condition-response'
					)
					.addClass('show');
			} else {
				$(this)
					.closest(
						'.codereadr-service-action__optional-invalid-condition'
					)
					.find(
						'.codereadr-service-action__optional-invalid-condition-response'
					)
					.removeClass('show');
			}
		}
	);

	// A method to format the form data to be well structured.
	function serializePost(form) {
		var data = {};
		form = $(form).serializeArray();
		for (var i = form.length; i--; ) {
			var name = form[i].name;
			var value = form[i].value;
			var index = name.indexOf('invalid_conditions');
			if (index > -1) {
				if (!data['default_invalid_conditions']) {
					data['default_invalid_conditions'] = {};
				}
				if (!data['optional_invalid_conditions']) {
					data['optional_invalid_conditions'] = {};
				}
				var invalid_condition_index = name
					.match(/\[(.*?)\]/g)[0]
					.replace('[', '')
					.replace(']', '');
				var invalid_condition_type = name
					.match(/\[(.*?)\]/g)[1]
					.replace('[', '')
					.replace(']', '');
				if (
					!data['default_invalid_conditions'][invalid_condition_index]
				) {
					data['default_invalid_conditions'][
						invalid_condition_index
					] = {};
				}
				if (
					!data['optional_invalid_conditions'][
						invalid_condition_index
					]
				) {
					data['optional_invalid_conditions'][
						invalid_condition_index
					] = {};
				}
				if (name.indexOf('default_invalid_conditions') > -1) {
					data['default_invalid_conditions'][invalid_condition_index][
						invalid_condition_type
					] = value;
				} else {
					data['optional_invalid_conditions'][
						invalid_condition_index
					][invalid_condition_type] = value;
				}
			} else data[name] = value;
		}
		return data;
	}

	// On click on save button.
	$(
		'.codereadr-service-modal .codereadr-button-primary:not(.is-saving)'
	).on('click', function () {
		var thisEl = $(this);
		thisEl.addClass('is-saving');
		thisEl.text('Saving');
		var formData = serializePost('.codereadr-service-form');

		$.ajax({
			url: codeReadr.ajaxUrl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'codereadr_insert_or_update_service',
				action_name: $('.codereadr-service-action-select').val(),
				formData: formData,
				nonce: codeReadr.nonce
			},
			success: function (data) {
				thisEl.removeClass('is-saving');
				$('.codereadr-service-modal').removeClass('show');
				location.reload();
			},
			error: function () {
				thisEl.removeClass('is-saving');
			},
		});
	});


	// Filter action options based on selected integration
	function filterActionOptions() {
		var integrationSlug = $(".codereadr-service-integration-select").val();
		$(".codereadr-service-action-select option").each(function () {
			var actionName= $(this).data('action-name');
			console.log(actionName);
			var actionTypeData = registeredActions[actionName];
			if(actionTypeData['integration_slug'] !== integrationSlug) {
				$(this).hide()
			}
			else {
				$(this).show()
			}
		});
		$(".codereadr-service-action-select option").each(function () {
			if ($(this).css('display') != 'none') {
				$(this).prop("selected", true);
				$(".codereadr-service-action-select").trigger("change");
				return false;
			}
		});
	}

	filterActionOptions();

	$(".codereadr-service-integration-select").on('change', function() {
		filterActionOptions();
	});
	// On click on manage users button
	$(".codereadr-admin-services-users__manage").on('click', function() {
		var serviceId = $(this).data('service-id');
		$(".codereadr-back-to-users-listing-screen").attr('data-service-id', serviceId);
	});
	
	$(".codereadr-admin-services-users__manage, .codereadr-back-to-users-listing-screen").on('click', function() {
		var serviceId = $(this).data('service-id');
		$(".codereadr-manage-users-modal").addClass("show");
		$(".codereadr-users-listing-screen__users").removeClass("show");
		$(".codereadr-users-listing-screen").addClass("show")
		$(".codereadr-listing-screen__loader").addClass('show')
		$(".codereadr-users-form-screen").removeClass('show')
		$.ajax({
			url: codeReadr.ajaxUrl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'codereadr_retrieve_users',
				nonce: codeReadr.nonce
			},
			success: function (response) {
				$(".codereadr-users-listing-screen__users").addClass("show");
				$(".codereadr-listing-screen__loader").removeClass('show');
				$(".codereadr-users-listing-screen__users-list").empty();
				if(response.data.users.length > 0 ) {
					response.data.users.forEach(function(user) {
						var userServices = [];
						if(user.service) {
						  	user.service = Array.isArray(user.service) ? user.service : [user.service];
							if( user.service.length > 0 ) {
								userServices = user.service.map(function(userService) {
									return parseInt( userService['@attributes']['id']);
								}); 
							}  
						}
						var isTheSelectedServiceIncluded = userServices.includes(parseInt(serviceId));
						$(".codereadr-users-listing-screen__users-list").append("<p> <input name=" + user['@attributes']['id']  + " type='checkbox' " +  (isTheSelectedServiceIncluded ? 'checked' : '') + "/>" + user.username + "</p>");
					})
				}
				else {
					$(".codereadr-users-listing-screen__users-list").append("<p class='codereadr-info-message'>No users yet</p>");
				}
			},
			error: function () {
				thisEl.removeClass('is-saving');
			},
		});
	})





	// On click on creating a new user button
	$(".codereadr-create-new-user-button").on('click', function() {
		var serviceId = $('.codereadr-back-to-users-listing-screen').data('service-id');

		$.ajax({
			url: codeReadr.ajaxUrl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'codereadr_create_new_user',
				username: $(".codereadr-users-form-screen__user-name-input").val(),
				userPass: $(".codereadr-users-form-screen__user-password-input").val(),
				serviceId: serviceId,
				nonce: codeReadr.nonce
			},
			success: function (response) {
				$(".codereadr-back-to-users-listing-screen").trigger("click")
			},
			error: function () {
				
			},
		});
	})

	// On clicking on saving users list
	$(".codereadr-users-list-save-button").on('click', function() {
		var serviceId = $('.codereadr-back-to-users-listing-screen').data('service-id');
	
		$.ajax({
			url: codeReadr.ajaxUrl,
			method: 'POST',
			dataType: 'json',
			data: {
				action: 'codereadr_save_users_list',
				formData: $(".codereadr-users-listing-screen__users").serializeArray(),
				serviceId: serviceId,
				nonce: codeReadr.nonce
			},
			success: function (response) {
				$(".codereadr-back-to-users-listing-screen").trigger("click")
			},
			error: function () {
				
			},
		});
	})
});
