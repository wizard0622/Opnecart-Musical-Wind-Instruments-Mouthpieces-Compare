<?php
class ModelCatalogManufacturer extends Model {
    
	public function getSeries($manufacturer_id, $category_id) {
		$sql = "SELECT DISTINCT sku FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE p.manufacturer_id = '" . (int)$manufacturer_id . "'";
		if(!empty($category_id)) 
      $sql .= " AND p2c.category_id = '" . (int)$category_id . "'"; 
    
    $query = $this->db->query($sql);
		return $query->rows;
	}	

	public function getManufacturer($manufacturer_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_description md ON (m.manufacturer_id = md.manufacturer_id) LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m.manufacturer_id = '" . (int)$manufacturer_id . "' AND (md.language_id = '" . (int)$this->config->get('config_language_id') . "' OR md.language_id is null) AND m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");
	
		return $query->row;	
	}
	
	public function getManufacturers($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
			
			$sort_data = array(
				'name',
				'sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY name";	
			}
			
			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}

				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}	
			
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}				
					
			$query = $this->db->query($sql);
			
			return $query->rows;
		} else {
			$manufacturer_data = $this->cache->get('manufacturer.' . (int)$this->config->get('config_store_id'));
		
			if (!$manufacturer_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer m LEFT JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id) WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY name");
	
				$manufacturer_data = $query->rows;
			
				$this->cache->set('manufacturer.' . (int)$this->config->get('config_store_id'), $manufacturer_data);
			}

			return $manufacturer_data;
		}	
	}
	
	public function getManufacturersByCatID($category_id)
	{
        $sql = "SELECT DISTINCT m.manufacturer_id manufacturer_id, m.name
		FROM " . DB_PREFIX . "manufacturer m
		INNER JOIN " . DB_PREFIX . "product p ON (m.manufacturer_id = p.manufacturer_id)
		INNER JOIN " . DB_PREFIX . "manufacturer_to_store m2s ON (m.manufacturer_id = m2s.manufacturer_id)
		INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)
		WHERE m2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
		AND p2c.category_id = '" . (int)$category_id . "'
		ORDER BY m.name ASC";
	
		$query = $this->db->query($sql);
		
		return $query->rows;
	}

	
}
?>