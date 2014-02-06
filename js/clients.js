var clients_view,
	clients_loader_view,
	clients_category_view,
	client_info_view,
	client_items_view,
	new_client_view;


/** 
View Id : clients-view 
Show the 2 client categories - "Existing" & "Prospective"
*/
var ClientsView = Backbone.View.extend({
	el: '#contents',
	render: function () {
		var template = _.template($("#clients-view").html());
		this.$el.html(template);

		$("#home-tabs a").removeClass('home-tab-active');
		$("#clients-tab").addClass('home-tab-active');
	}
});


/** 
View Id : clients-loader-view 
Send an ajax to get clients of a category
*/
var ClientsLoaderView = Backbone.View.extend({
	el: '#client-category-contents',
	render: function () {
		var template = _.template($("#clients-loader-view").html());
		this.$el.html(template);
		
		$(".client-category-tab").removeClass('client-category-tab-active');
		$("#" + this.options['category_name'] + "-clients").addClass('client-category-tab-active');

		Backbone.ajax({
			type: 'GET',
			url: 'controller.php',
			data: { command: 'GetClientsByCategory', category_id: this.options['category_id'] },
			dataType: 'json',
			success: function (response) {
				$("#all-clients-loader").hide();
				
				/* On no error show the clients */
				if(response.error == 0) {
					clients_category_view = new ClientsCategoryView({ 
																	clients: response.data.clients,                  
																	category_id: response.data.category_id           
																});
					clients_category_view.render();
				}
			}
		});
	}
});


/** 
View Id : clients-category-view 
Show the clients of the category
*/
var ClientsCategoryView = Backbone.View.extend({
	el: '#client-category-contents',
	render: function () {
		var template = _.template(
								$("#clients-category-view").html(), { 
									clients: this.options['clients'],             
									category_id: this.options['category_id']           
								});
		this.$el.html(template);
	},
	events: {
		'click .client-delete-button': 'deleteClient',                   
		'click .client-confirm-delete-no': 'cancelDelete',                  
		'click .client-confirm-delete-yes': 'proceedDelete'
	},
	deleteClient: function (e) {
		var parent_td = $(e.currentTarget).closest('.client-header-edit');
		parent_td.find('.client-confirm-delete').show();
	}, 
	cancelDelete: function (e) {
		$(e.currentTarget).closest('.client-confirm-delete').hide();
	},
	proceedDelete: function (e) {
		var parent_td = $(e.currentTarget).closest('td'),
			parent_row = $(e.currentTarget).closest('tr'),
			client_id = parent_row.attr('data-client-id');

		parent_td.find('.client-header-edit-buttons').hide();
		parent_td.find('.client-confirm-delete').hide();
		parent_td.find('.client-local-loader').show();
		
		Backbone.ajax({
			type: 'get',
			url: 'controller.php',
			data: { command: 'DeleteClient', client_id: client_id },
			dataType: 'json',
			success: function (response) {
				parent_td.find('.client-header-edit-buttons').show();
				parent_td.find('.client-local-loader').hide();
				
				if(response.error == 0) {
					parent_row.remove();
					
					if($(".client-row").length == 0) {
						$("#all-clients-table").hide();
						$("#no-clients").show();
					}

					if($("#client-info-container").is(':visible')) {
						if($("#client-info-container").attr('data-client-id') == client_id) {
							$("#client-info-container").remove();
							if(typeof client_info_view != 'undefined') {
								client_info_view.undelegateEvents();
							}
						}
					}

					if($("#client-all-items").is(':visible')) {
						if($("#client-all-items").attr('data-client-id') == client_id) {
							$("#client-all-items").remove();
							if(typeof client_items_view != 'undefined') {
								client_items_view.undelegateEvents();
							}
						}
					}
				}
			}
		});
	},
	addClient: function () {
		new_client_view = new NewClientView();
		new_client_view.render();

		$("#save-new-clients").show();
	},
});



/** 
View Id : new-client-view 
Show the new client container
*/
var NewClientView = Backbone.View.extend({
	el: '#add-save-more-clients',
	render: function () {
		var template = _.template(
								$("#new-client-view").html(), {}
								);
		
		$(template).insertBefore(this.$el);
		
		$(".new-client-container").outerWidth($("#all-clients-table").width());
	}
});