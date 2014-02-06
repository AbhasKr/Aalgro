<?php
header('Content-type: application/json'); 

$command = $_REQUEST['command'];

try {
	switch($command) {		
		/* Items */
		case 'GetItemsByCategory':
			$category_id = $_REQUEST['category_id'];
			$units = array(101 => 'Kg', 102 => 'Bunch', 103 => 'Bag');

			$items = array();
			$items['_101'] = array('item_name' => 'Ginger', 'item_units' => array(101 => 'Kg', 103 => 'Bag'), 'item_varieties' => array(101 => 'Ginger A', 102 => 'Ginger B'));
			$items['_102'] = array('item_name' => 'Garlic', 'item_units' => array(101 => 'Kg', 102 => 'Bunch', 103 => 'Bag'), 'item_varieties' => array(103 => 'Garlic A'));
			$items['_103'] = array('item_name' => 'Onion', 'item_units' => array(101 => 'Kg'), 'item_varieties' => array());
			echo json_encode(array('error' => 0, 'data' => array('units' => $units, 'items' => $items, 'category_id' => $category_id)));
			
			break;

		case 'EditItem':
			$item = array('item_id' => 101, 'item_name' => 'Ginger', 'item_units' => array(101 => 'Kg', 102 => 'Bunch', 103 => 'Bag'), 'item_varieties' => array(101 => 'Ginger A', 102 => 'Ginger B', 104 => 'Ginger C'));
			echo json_encode(array('error' => 0, 'data' => array('item' => $item)));

			break;

		case 'AddItems':
			$items = array();
			for($i=0;$i<sizeof($_REQUEST['new_items']);$i++)
				$items[] = array('item_id' => 104, 'item_name' => 'Ginger', 'item_units' => array(101 => 'Kg', 102 => 'Bunch', 103 => 'Bag'), 'item_varieties' => array(101 => 'Ginger A', 102 => 'Ginger B', 104 => 'Ginger C'));
			echo json_encode(array('error' => 0, 'data' => array('items' => $items)));

			break;

		case 'DeleteItem':
			echo json_encode(array('error' => 0));

			break;

		
		/* Suppliers */
		case 'GetSuppliersByCategory':
			$category_id = $_REQUEST['category_id'];
			
			$suppliers = array();
			$suppliers[] = array('supplier_id' => 101, 'supplier_name' => 'Rama');
			$suppliers[] = array('supplier_id' => 102, 'supplier_name' => 'Krishna');
			$suppliers[] = array('supplier_id' => 103, 'supplier_name' => 'Muthuswamy');
			$suppliers[] = array('supplier_id' => 104, 'supplier_name' => 'Veera');
			echo json_encode(array('error' => 0, 'data' => array('suppliers' => $suppliers, 'category_id' => $category_id)));
			
			break;

		case 'DeleteSupplier':
			echo json_encode(array('error' => 0));
			
			break;

		case 'GetSupplierInfo':
			$supplier_id = 101;
			$supplier_name = 'Supp Name';
			$supplier_address = 'House 489/B';
			$supplier_phone = '9844460855';
			$supplier_remarks = array(101 => 'Remark 1', 102 => 'Remark 2');
			echo json_encode(array('error' => 0, 'data' => array('supplier_id' => $supplier_id, 'supplier_name' => $supplier_name, 'supplier_phone' => $supplier_phone, 'supplier_address' => $supplier_address, 'supplier_remarks' => $supplier_remarks)));
			
			break;

		case 'EditSupplierInfo':
			echo json_encode(array('error' => 0, 'data' => array('supplier_id' => 101, 'supplier_name' => 'New Supplier')));
			
			break;

		case 'AddSuppliers':
			$suppliers = array();
			$suppliers[] = array('supplier_id' => 105, 'supplier_name' => 'N Muthuswamy');
			$suppliers[] = array('supplier_id' => 106, 'supplier_name' => 'N Veera');
			echo json_encode(array('error' => 0, 'data' => array('suppliers' => $suppliers)));

			break;

		case 'GetSupplierItems':
			$response = array();
			
			if($_REQUEST['update_items'] == '1') {
				$items = array();
				$items['101'] = array('item_name' => 'Ginger', 'item_units' => array('101' => 'Kg', '103' => 'Bag'), 'item_varieties' => array('101' => 'Ginger A', '102' => 'Ginger B', '103' => ''));
				$items['102'] = array('item_name' => 'Garlic', 'item_units' => array('101' => 'Kg', '102' => 'Bunch', '103' => 'Bag'), 'item_varieties' => array('104' => 'Garlic A', '105' => ''));
				$items['103'] = array('item_name' => 'Onion', 'item_units' => array('101' => 'Kg'), 'item_varieties' => array('106' => ''));
				$response['items'] = $items;
			}

			$supplier_items = array();
			$supplier_items['101'] = array(
											'103' => array(
															'103' => array('item_price' => 105)
															),
											'101' => array(
															'101' => array('item_price' => 101), 
															'103' => array('item_price' => 102)
															),
											'102' => array(
															'103' => array('item_price' => 103),
															'101' => array('item_price' => 104)
															)
											);
			$supplier_items['103'] = array(
											'106' => array(
															'101' => array('item_price' => 106)
															)
											);
			$response['supplier_items'] = $supplier_items;

			echo json_encode(array('error' => 0, 'data' => $response));
			
			break;

		case 'DeleteSupplierItem':
			echo json_encode(array('error' => 0));

			break;

		case 'AddSupplierItems':			
			$supplier_items = array();
			$supplier_items[] = array('item_id' => 101, 'item_variety_id' => 101, 'unit_id' => 101, 'item_price' => 5555);
			$supplier_items[] = array('item_id' => 101, 'item_variety_id' => 103, 'unit_id' => 101, 'item_price' => 5555);
			$supplier_items[] = array('item_id' => 102, 'item_variety_id' => 104, 'unit_id' => 101, 'item_price' => 5555);
			
			echo json_encode(array('error' => 0, 'data' => $supplier_items));
			
			break;

		case 'EditSupplierItems':
			$edited_items = array();
			$edited_items[] = array('item_id' => 101, 'item_variety_id' => 101, 'unit_id' => 101, 'item_price' => 5555);
			$edited_items[] = array('item_id' => 101, 'item_variety_id' => 103, 'unit_id' => 101, 'item_price' => 5555);
			$edited_items[] = array('item_id' => 102, 'item_variety_id' => 104, 'unit_id' => 101, 'item_price' => 5555);
			
			echo json_encode(array('error' => 0, 'data' => $edited_items));

			break;

		
		/* Pricing */
		case 'GetItemsforPricing':
			$leafy = '';
			
			$fruits = '';

			$exotic = array();
			$exotic['104'] = array('item_name' => 'Brocolli', 'item_units' => array('101' => 'Kg'), 'item_varieties' => array('107' => '', '108' => 'Brocolli A'));

			$opg = array();
			$opg['102'] = array('item_name' => 'Garlic', 'item_units' => array('101' => 'Kg', '102' => 'Bunch', '103' => 'Bag'), 'item_varieties' => array('104' => '', '105' => 'Garlic A'));
			$opg['103'] = array('item_name' => 'Onion', 'item_units' => array('101' => 'Kg'), 'item_varieties' => array('106' => ''));

			$vegetables = array();
			$vegetables['101'] = array('item_name' => 'Ginger', 'item_units' => array('101' => 'Kg', '103' => 'Bag'), 'item_varieties' => array('101' => '', '102' => 'Ginger A', '103' => 'Ginger B'));

			$items = array('101' => $leafy, '102' => $fruits, '103' => $exotic, '104' => $opg, '105' => $vegetables);

			$price_categories = array('101' => 'FXD', '102' => 'VAR', '103' => 'WEB');

			echo json_encode(array('error' => 0, 'data' => array('items' => $items, 'price_categories' => $price_categories)));

			break;

		case 'GetSuppliersforPricing': 
			$suppliers = array();
			$suppliers['101'] = array(
									'supplier_name' => 'Rama', 
									'supplier_category' => 'Vendor',
									'supplier_category_id' => 101,
									'supplier_items' => array(
																'101' => array(
																			'103' => array(
																							'103' => array('item_price' => 111, 'ts' => '1390471716')
																							),
																			'101' => array(
																							'101' => array('item_price' => 112, 'ts' => '1390471716'), 
																							'103' => array('item_price' => 113, 'ts' => '1390471716')
																							),
																			'102' => array(
																							'103' => array('item_price' => 114, 'ts' => '1390471716'),
																							'101' => array('item_price' => 115, 'ts' => '1390471716')
																							)
																			),
																'103' => array(
																			'106' => array(
																							'101' => array('item_price' => 116, 'ts' => '1390471716')
																					)
																			)
														)
								);

			$suppliers['102'] = array(
									'supplier_name' => 'Krishna', 
									'supplier_category' => 'Vendor',
									'supplier_category_id' => 101,
									'supplier_items' => array(
																'101' => array(
																			'103' => array(
																							'103' => array('item_price' => 211, 'ts' => '1390471716')
																							),
																			'101' => array(
																							'101' => array('item_price' => 212, 'ts' => '1390471716'), 
																							'103' => array('item_price' => 213, 'ts' => '1390471716')
																							),
																			'102' => array(
																							'103' => array('item_price' => 214, 'ts' => '1390471716'),
																							'101' => array('item_price' => 215, 'ts' => '1390471716')
																							)
																			)
														)
								);

			$suppliers['103'] = array(
									'supplier_name' => 'Muthuswamy', 
									'supplier_category' => 'Retail',
									'supplier_category_id' => 102,
									'supplier_items' => array(
																'102' => array(
																			'104' => array(
																							'103' => array('item_price' => 311, 'ts' => '1390471716')
																							),
																			'105' => array(
																							'101' => array('item_price' => 312, 'ts' => '1390471716'), 
																							'103' => array('item_price' => 313, 'ts' => '1390471716')
																							)
																			),
																'103' => array(
																			'106' => array(
																							'101' => array('item_price' => 314, 'ts' => '1390471716')
																					)
																			)
														)
								);

			$suppliers['104'] = array(
									'supplier_name' => 'Veera', 
									'supplier_category' => 'Mandi',
									'supplier_category_id' => 103,
									'supplier_items' => ''
								);

			$prices = array();
			$prices['101'] = array('101' => array(
												'101' => array('101' => array('price' => 11, 'ts' => '1390471716'), '102' => array('price' => 12, 'ts' => '1390471716'), '103' => array('price' => 13, 'ts' => '1390471716')), 
												'103' => array('101' => array('price' => 14, 'ts' => '1390471716'), '102' => array('price' => 15, 'ts' => '1390471716'), '103' => array('price' => 16, 'ts' => '1390471716'))
												)
							);
			$prices['104'] = array('107' => array(
												'101' => array('101' => array('price' => 17, 'ts' => '1390471716'), '102' => array('price' => 18, 'ts' => '1390471716'), '103' => array('price' => 19, 'ts' => '1390471716'))
												),
									'108' => array(
												'101' => array('101' => array('price' => 20, 'ts' => '1390471716'), '102' => array('price' => 21, 'ts' => '1390471716'), '103' => array('price' => 22, 'ts' => '1390471716'))
												)
							);
		
			echo json_encode(array('error' => 0, 'data' => array('suppliers' => $suppliers, 'prices' => $prices)));
			
			break;

		case 'SaveAalgroPrices':
			$prices = array();
			$prices['101'] = array('101' => array(
												'101' => array('101' => array('price' => 911, 'ts' => '1390644516'), '102' => array('price' => 912, 'ts' => '1390644516'), '103' => array('price' => 913, 'ts' => '1390644516')), 
												'103' => array('101' => array('price' => 914, 'ts' => '1390644516'), '102' => array('price' => 915, 'ts' => '1390644516'), '103' => array('price' => 916, 'ts' => '1390644516'))
												)
							);
			$prices['104'] = array('107' => array(
												'101' => array('101' => array('price' => 917, 'ts' => '1390644516'), '102' => array('price' => 918, 'ts' => '1390644516'), '103' => array('price' => 919, 'ts' => '1390644516'))
												),
									'108' => array(
												'101' => array('101' => array('price' => 920, 'ts' => '1390644516'), '102' => array('price' => 921, 'ts' => '1390644516'), '103' => array('price' => 922, 'ts' => '1390644516'))
												)
							);
			echo json_encode(array('error' => 0, 'data' => array('prices' => $prices)));

			break;

		case 'CreatePricingExcel':
			echo json_encode(array('error' => 0, 'data' => array('name' => '123.xls')));

			break;


		/* Suppliers */
		case 'GetClientsByCategory':
			$category_id = $_REQUEST['category_id'];
			
			$clients = array();
			$clients[] = array('client_id' => 101, 'client_name' => 'Rama');
			$clients[] = array('client_id' => 102, 'client_name' => 'Krishna');
			$clients[] = array('client_id' => 103, 'client_name' => 'Muthuswamy');
			$clients[] = array('client_id' => 104, 'client_name' => 'Veera');
			echo json_encode(array('error' => 0, 'data' => array('clients' => $clients, 'category_id' => $category_id)));
			
			break;

		case 'DeleteClient':
			echo json_encode(array('error' => 0));
			
			break;	
	}
}
catch(Exception $e) {
	echo json_encode(array('error' => 1, 'code' => $e->getCode(), 'message' => ErrorMessages($e->getCode())));
	exit();
}

?>