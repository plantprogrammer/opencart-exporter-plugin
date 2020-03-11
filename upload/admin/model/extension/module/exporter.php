<?php
class ModelExtensionModuleExporter extends Model {

	//runs a SQL command to get relevant data entries from the database 
	function getResults()
	{	
		$query = $this->db->query("select oc_order.email, oc_order.firstname, oc_order.lastname, oc_order_product.name, oc_category_description.name as type
		from oc_order
		inner JOIN oc_order_product
		on oc_order_product.order_id = oc_order.order_id
		inner JOIN oc_product_to_category
		on oc_product_to_category.product_id = oc_order_product.product_id
		inner JOIN oc_category_description
		on oc_category_description.category_id = oc_product_to_category.category_id");
		
		return $query;
	}
	
	//disables the modification introduced in the installation.xml document
	function disableMod()
	{
		$this->db->query("UPDATE oc_modification SET status = 0 WHERE status = 1");
	}
	
	//enables the modification introduced in the installation.xml document
	function enableMod()
	{
		$this->db->query("UPDATE oc_modification SET status = 1 WHERE status = 0");
	}
	
}

?>