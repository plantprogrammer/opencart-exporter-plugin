<?php
class ModelExtensionModuleExporter extends Model {

	//runs a SQL command to get relevant data entries from the database 
	function getResults()
	{
		$orderTable = "";
	
		if (DB_PREFIX == "")
		{
			$orderTable = "`order`";
		}
		else
		{
			$orderTable = DB_PREFIX . "order";
		}
		
		$query = $this->db->query("SELECT DISTINCT " . DB_PREFIX . "order.email, " . DB_PREFIX . "order.firstname, " . DB_PREFIX . "order.lastname, " . DB_PREFIX . "order_product.name, " . DB_PREFIX . "category_description.name as type
		DB_PREFIX . "order.date_added" 
		. " FROM " . $orderTable 
		. " INNER JOIN " . DB_PREFIX . "order_product" .  
		" ON " . DB_PREFIX . "order_product.order_id = " . DB_PREFIX . "order.order_id"
		. " INNER JOIN " . DB_PREFIX . "product_to_category" .  
		" ON " . DB_PREFIX . "product_to_category.product_id = " . DB_PREFIX . "order_product.product_id" 
		. " INNER JOIN " . DB_PREFIX . "category_description" .
		" ON " . DB_PREFIX . "category_description.category_id = " . DB_PREFIX . "product_to_category.category_id");
		
		return $query;
	}
	
}

?>
