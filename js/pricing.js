var pricing_loader_view,
	pricing_items_view,
	pricing_items_suppliers_table_view;


var PRICING_ITEMS = [],
	SELECTED_ITEMS = { '101': {}, '102': {}, '103': {}, '104': {}, '105': {} };


var PricingLoaderView = Backbone.View.extend({
	el: '#contents',
	render: function () {
		$("#home-tabs a").removeClass('home-tab-active');
		$("#pricing-tab").addClass('home-tab-active');

		var template = _.template($("#pricing-items-loader-view").html());
		this.$el.html(template); 
		$("#pricing-items-loader").css('width', ($("#pricing-tab").position().left) + 100);
		
		Backbone.ajax({
			type: 'GET',
			url: 'controller.php',
			data: { command: 'GetItemsforPricing' },
			dataType: 'json',
			success: function (response) {
				$("#pricing-loader").hide();
				
				if(response.error == 0) {
					SELECTED_ITEMS = { '101': {}, '102': {}, '103': {}, '104': {}, '105': {} };
					
					PRICING_ITEMS = response.data; 
					pricing_items_view = new PricingItemsView({ 
																	items: response.data                
																});
					pricing_items_view.render();
				}
			}
		});
	}
});



var PricingItemsView = Backbone.View.extend({
	el: '#contents',
	render: function () {
		var template = _.template(
									$("#pricing-items-view").html(), {
										categories_items: this.options['items']
								});
		this.$el.html(template); 

		$("#send-selected-items").css({'left': ($("#pricing-items").offset().left + $("#pricing-items").outerWidth() + 30) + 'px', 'top': $("#pricing-items").offset().top + 'px' });
	},
	events: {
		'click .pricing-item-category-check-all': 'wholeCategorySelection',
		'click .pricing-item-category-item': 'itemSelectedUnselected', 
		'click #send-selected-items': 'sendSelectedItems'
	},
	wholeCategorySelection: function (e) {
		var category_id = $(e.currentTarget).val(),
			checked = $(e.currentTarget).is(":checked");

		if(checked == true) {
			$(".pricing-item-category-" + category_id).find(".pricing-item-category-item").attr('data-chosen', 1).addClass('pricing-item-category-item-chosen');
			
			if($('.pricing-item-category-item-chosen').length > 0) {
				$("#send-selected-items").show();
			}
		}
		else if(checked == false) {
			$(".pricing-item-category-" + category_id).find(".pricing-item-category-item").attr('data-chosen', 0).removeClass('pricing-item-category-item-chosen');
			if($('.pricing-item-category-item-chosen').length == 0) {
				$("#send-selected-items").hide();
			}
		}
	},
	itemSelectedUnselected: function (e) {
		if($(e.currentTarget).hasClass('pricing-item-category-item-chosen')) {
			$(e.currentTarget).attr('data-chosen', 0);
			$(e.currentTarget).removeClass('pricing-item-category-item-chosen');

			if($('.pricing-item-category-item-chosen').length == 0) {
				$("#send-selected-items").hide();
			}
		}
		else {
			$(e.currentTarget).attr('data-chosen', 1);
			$(e.currentTarget).addClass('pricing-item-category-item-chosen');

			$("#send-selected-items").show();
		}
	},
	sendSelectedItems: function () {
		if($("#send-selected-items").attr('data-in-progress') == '1') {
			return;
		}

		var selected_items = [];
		$(".pricing-item-category-item-chosen").each(function () {
			selected_items.push($(this).attr('data-item-id'));
			SELECTED_ITEMS[$(this).attr('data-category-id')][$(this).attr('data-item-id')] = Object.create(PRICING_ITEMS[$(this).attr('data-category-id')][$(this).attr('data-item-id')]);
		});
		PRICING_ITEMS = []; 

		$("#send-selected-items").text('Processing ..').css('opacity', '0.5').attr('data-in-progress', '1');
		Backbone.ajax({
			type: 'GET',
			url: 'controller.php',
			data: { command: 'GetSuppliersforPricing', selected_items: selected_items },
			dataType: 'json',
			success: function (response) {
				$("#send-selected-items").text('Edit Prices').css('opacity', '1').attr('data-in-progress', '0');

				if(response.error == 0) {
					$("#pricing-items-suppliers-container").remove();
					if(typeof pricing_items_suppliers_table_view != 'undefined') {
						pricing_items_suppliers_table_view.undelegateEvents();
					}

					pricing_items_suppliers_table_view = new PricingItemsSuppliersTableView({ 
																	suppliers: response.data.suppliers,
																	prices: (typeof response.data.prices != 'object' ? {} : response.data.prices)                 
																});
					pricing_items_suppliers_table_view.render();
				}
			}
		});
	}
});



var PricingItemsSuppliersTableView = Backbone.View.extend({
	el: '#contents',
	render: function () {
		var template = _.template(
								$("#pricing-items-suppliers-table-view").html(), {
									suppliers: this.options['suppliers'],
									prices: this.options['prices'],
								});
		this.$el.html(template); 

		$("#pricing-table-body-container").jScrollPane();
		$(".timeago").timeago();

		if($("#suppliers-body-table-container").get(0).scrollWidth > $("#suppliers-body-table-container").get(0).clientWidth) {
			var offset = $("#suppliers-header-table-container").offset();
			offset.top = offset.top + $("#pricing-table-header-container").height() + $("#pricing-table-body-container").height() + 30;
			$("#suppliers-scroll-container").offset(offset).width($("#suppliers-body-table-container").width());
			
			var scroll_width = $("#suppliers-scroll-container").width()-($("#suppliers-body-table-container").get(0).scrollWidth-$("#suppliers-body-table-container").get(0).clientWidth);
			if(scroll_width >= 0) {
				$("#suppliers-scroll").width(scroll_width);
			}
			else {
				$("#suppliers-scroll").width(50);
				this.scroll_multiplier = ($("#suppliers-body-table-container").get(0).scrollWidth-$("#suppliers-body-table-container").get(0).clientWidth)/100;
			}

			$("#suppliers-body-table-container, #suppliers-header-table-container").scrollLeft(0);
		}
	},
	events: {
		'click #suppliers-scroll-container': 'suppliersScrollContainerClick', 
		'click #suppliers-scroll': 'suppliersScrollClick', 
		'mousedown #suppliers-scroll': 'suppliersScrollMousedown', 
		'mousemove #suppliers-scroll': 'suppliersScrollMousemove',
		'mouseout #suppliers-scroll': 'suppliersScrollMouseout',
		'mouseup #suppliers-scroll': 'suppliersScrollMouseup',
		'keyup #prices-body-table td input[type="text"]': 'textboxKeyup',
		'click #pricing-table-check-all': 'checkAllItems', 
		'click #prices-body-table td input[type="checkbox"]': 'checkItems', 
		'click #pricing-table-save-prices': 'saveEditedPrices', 
		'click #prepare-excel': 'prepareExcel',
		'click #create-excel': 'createExcel', 
	},
	scroll_started: 0,
	last_position: null,
	scroll_multiplier: 1,
	suppliersScrollContainerClick: function (e) {
		if(e.pageX > $("#suppliers-scroll").offset().left)
			var diff = e.pageX - ($("#suppliers-scroll").offset().left + $("#suppliers-scroll").width());
		else 
			var diff = e.pageX - $("#suppliers-scroll").offset().left;

		$("#suppliers-scroll").offset({left: $("#suppliers-scroll").offset().left + diff});
		$("#suppliers-body-table-container, #suppliers-header-table-container").scrollLeft($("#suppliers-body-table-container").scrollLeft() + (diff*this.scroll_multiplier));
	},
	suppliersScrollClick: function (e) {
		e.stopPropagation();
	},
	suppliersScrollMousedown: function (e) {
		this.scroll_started = 1;
		this.last_position = e.pageX;

		e.stopPropagation();
	},
	suppliersScrollMousemove: function (e) {
		if(this.scroll_started == 1) {
			var diff = e.pageX - this.last_position; console.log(diff);
			
			if(diff > 0) {
				if(($("#suppliers-scroll").offset().left + $("#suppliers-scroll").width()) < ($("#suppliers-scroll-container").offset().left + $("#suppliers-scroll-container").width())) {
					$("#suppliers-scroll").offset({left: $("#suppliers-scroll").offset().left + diff});
					$("#suppliers-body-table-container, #suppliers-header-table-container").scrollLeft($("#suppliers-body-table-container").scrollLeft() + (diff*this.scroll_multiplier));
				}
			}
			else if(diff < 0) {
				if(($("#suppliers-scroll").offset().left) > ($("#suppliers-scroll-container").offset().left)) {
					$("#suppliers-scroll").offset({left: $("#suppliers-scroll").offset().left + diff});
					$("#suppliers-body-table-container, #suppliers-header-table-container").scrollLeft($("#suppliers-body-table-container").scrollLeft() + (diff*this.scroll_multiplier));
				}
			}

			this.last_position = e.pageX;
		}

		e.stopPropagation();
	},
	suppliersScrollMouseout: function (e) {
		this.scroll_started = 0;

		e.stopPropagation();
	},
	suppliersScrollMouseup: function (e) {
		this.scroll_started = 0;

		e.stopPropagation();
	},
	textboxKeyup: function (e) { 
		if($(e.currentTarget).val() != $(e.currentTarget).attr('defaultValue')) {
			$(e.currentTarget).addClass('edited-price');
		}
		else {
			$(e.currentTarget).removeClass('edited-price');	
		}

		if($(".edited-price").length == 0) {
			$("#pricing-table-save-prices").hide();
		}
		else {
			$("#pricing-table-save-prices").show();
		}
	},
	checkAllItems: function (e) {
		var checked = $(e.currentTarget).is(":checked"); 

		$("#pricing-download-link").remove();

		if(checked == true) {
			$("#prices-body-table td input[type='checkbox']").prop('checked', true);
			
			if(!$("#excel-prices-container").is(":visible")) {
				$("#prepare-excel").show();
			}
		}
		else if(checked == false) {
			$("#prices-body-table td input[type='checkbox']").prop('checked', false);
			
			$("#pricing-table-excel-container").children().hide();
		}
	},
	checkItems: function () {
		$("#pricing-download-link").remove();
		
		if($("#prices-body-table td input[type='checkbox']:checked").length == 0) {
			$("#pricing-table-excel-container").children().hide();
		}
		else if($("#prices-body-table td input[type='checkbox']:checked").length == 1) {
			if(!$("#excel-prices-container").is(":visible")) {
				$("#prepare-excel").show();
			}
		}
	},
	saveEditedPrices: function () {
		if($("#pricing-table-save-prices").attr('data-in-progress') == '1') {
			return;
		}

		var edited_prices = [],
			edited_price = {},
			parent_row;

		$("#prices-body-table tr").has(".edited-price").each(function () {
			edited_price = {};
			parent_row = $(this);
			edited_price.item_id = parent_row.attr('data-item-id');
			edited_price.item_variety_id = parent_row.attr('data-item-variety-id');
			edited_price.unit_id = parent_row.attr('data-unit-id');

			if(parent_row.find(".pricing-table-price-1 input[type='text']")) {
				edited_price.price_1 = parent_row.find(".pricing-table-price-1 input[type='text']").val();
			}

			if(parent_row.find(".pricing-table-price-2 input[type='text']")) {
				edited_price.price_2 = parent_row.find(".pricing-table-price-2 input[type='text']").val();
			}

			if(parent_row.find(".pricing-table-price-3 input[type='text']")) {
				edited_price.price_3 = parent_row.find(".pricing-table-price-3 input[type='text']").val();
			}

			edited_prices.push(edited_price);
		});

		$("#pricing-table-save-prices").text('Saving ..').css('opacity', '0.5').attr('data-in-progress', '1');
		Backbone.ajax({
			type: 'POST',
			url: 'controller.php',
			data: { command: 'SaveAalgroPrices', edited_prices: edited_prices },
			dataType: 'json',
			success: function (response) {
				$("#pricing-table-save-prices").text('Save').css('opacity', '1').attr('data-in-progress', '0');

				if(response.error == 0) {
					$("#pricing-table-save-prices").hide();

					$(".edited-price").removeClass('edited-price');
					
					for(var i in response.data.prices) {
						for(var j in response.data.prices[i]) {
							for(var k in response.data.prices[i][j]) {
								if('price_1' in response.data.prices[i][j][k]) {
									$("#prices-body-table-row-" + i + '-' + j + '-' + k).find(".pricing-table-price-1 input[type='text']").val(response.data.prices[i][j][k]['price_1']);
									$("#prices-body-table-row-" + i + '-' + j + '-' + k).find(".pricing-table-price-1 input[type='text']").attr('defaultValue', response.data.prices[i][j][k]['price_1']);
								}

								if('price_2' in response.data.prices[i][j][k]) {
									$("#prices-body-table-row-" + i + '-' + j + '-' + k).find(".pricing-table-price-2 input[type='text']").val(response.data.prices[i][j][k]['price_2']);
									$("#prices-body-table-row-" + i + '-' + j + '-' + k).find(".pricing-table-price-2 input[type='text']").attr('defaultValue', response.data.prices[i][j][k]['price_2']);
								}

								if('price_3' in response.data.prices[i][j][k]) {
									$("#prices-body-table-row-" + i + '-' + j + '-' + k).find(".pricing-table-price-3 input[type='text']").val(response.data.prices[i][j][k]['price_3']);
									$("#prices-body-table-row-" + i + '-' + j + '-' + k).find(".pricing-table-price-3 input[type='text']").attr('defaultValue', response.data.prices[i][j][k]['price_3']);
								}
							}
						}
					}
				}
			}
		});
	},
	prepareExcel: function () {
		$("#prepare-excel").hide();

		$("#excel-prices-container").show();
		$("#create-excel").show();
	},
	createExcel: function(e) {
		var data = { 'Leafy': [], 'Fruits': [], 'Exotic': [], 'OPG': [], 'Vegetables': [] },
			price_chosen = $("input[name='excel-main-price']:checked").val(),
			item_categories = { 101: 'Leafy', 102: 'Fruits', 103: 'Exotic', 104: 'OPG', 105: 'Vegetables' };

		$("#prices-body-table td input[type='checkbox']:checked").closest('tr').each(function () {
			var category_id = $(this).attr('data-category-id'),
				item_id = $(this).attr('data-item-id'),
				item_variety_id = $(this).attr('data-item-variety-id'),
				unit_id = $(this).attr('data-unit-id');

			data[item_categories[category_id]].push([
													SELECTED_ITEMS[category_id][item_id]['item_name'] + (SELECTED_ITEMS[category_id][item_id]['item_varieties'][item_variety_id] == '' ? '' : ' - ' + SELECTED_ITEMS[category_id][item_id]['item_varieties'][item_variety_id]),
													SELECTED_ITEMS[category_id][item_id]['item_units'][unit_id],
													$(this).find(".pricing-table-price-" + price_chosen + " input[type='text']").attr('defaultValue')
													]);
		});

		$("#create-excel").text('Creating..').css('opacity', '0.5').attr('data-in-progress', '1');
		Backbone.ajax({
			type: 'POST',
			url: 'controller.php',
			data: { command: 'CreatePricingExcel', data: data },
			dataType: 'json',
			success: function (response) {
				$("#create-excel").text('Create').css('opacity', '1').attr('data-in-progress', '0');

				if(response.error == 0) {
					$("#pricing-table-excel-container").children().hide();

					$("<a id='pricing-download-link' target='_blank' href='excels/pricing/" + response.data.name + "'>DOWNLOAD</a>").insertAfter("#pricing-table-excel-container");
				}
			}
		});
	}
});