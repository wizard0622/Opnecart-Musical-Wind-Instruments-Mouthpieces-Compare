<?php
/*
  Project: CSV Product Import
  Author : karapuz <support@ka-station.com>

  Version: 3 ($Revision: 87 $)

*/

//
// this option adds a filter for processing \r character to \n in files because fgetcsv function 
// may fail randomly with detrmining the end of MacOS file line. It can be disabled to improve 
// the speed of file processing for the import scripts where MacOS csv files are not used.
//
define('KA_ENABLE_MACFIX', TRUE);

require_once(DIR_SYSTEM . 'library/ka_db.php');
require_once(DIR_SYSTEM . 'library/ka_urlify.php');
require_once(DIR_SYSTEM . 'engine/ka_model.php');

if (!function_exists('mb_strtolower')) {
	function mb_strtolower($str, $charset) {
		$ret = "";
		if ($charset == 'utf-8') {
			$ret = utf8_strtolower($str);
		} else {
			$ret = strtolower($str);
		}
		return $ret;
	}
}

if (!function_exists('utf8_strlen')) {
	function utf8_strlen($string) {
		return strlen(utf8_decode($string));
	}
}

if (constant('KA_ENABLE_MACFIX')) {
	class macfix_filter extends php_user_filter {
		function filter($in, $out, &$consumed, $closing) {
			while ($bucket = stream_bucket_make_writeable($in)) {

				if (mb_strpos($bucket->data, "\r\n") === false) {
					$bucket->data = mb_ereg_replace("\r", "\n", $bucket->data, 'mp');
				}
				
				$consumed += $bucket->datalen;
				stream_bucket_append($out, $bucket);
			}
			return PSFS_PASS_ON;
	  	}
	}

	stream_filter_register("macfix", "macfix_filter") or die("Failed to register filter");
}

class ModelToolKaImport extends Model {

	// constants

	protected $sec_per_cycle    = 10;
	protected $enclosure        = '"';	
	//	protected $escape           = '\\'; - not supported;
	protected $default_attribute_group_name = 'unassigned';
	protected $generate_urls    = true;
	protected $curl_resolve_attempts = 3;

	protected $extended_types          = array('select', 'radio', 'checkbox', 'image');
	protected $options_with_def_values = array('text', 'textarea', 'date', 'time', 'datetime');
	protected $option_types            = array('select', 'radio', 'checkbox', 'image', 'text', 'textarea',
	                                     'file', 'date', 'time', 'datetime');
	protected $options_with_images     = array('select', 'radio', 'checkbox', 'image');
	
	protected $filter = null;
	
	protected $delimiters = array(
		"\t"   => 'tab',
		";"  => 'semicolon ";"',
		','  => 'comma ","',
		'|'  => 'pipe "|"',
		' '  => 'space " "',
	);
	
	// session variables
	protected $params;
	protected $stat;

	//temporary vaiables
	protected $lastError;
	protected $columns;
	protected $messages;
	protected $kalog = null;
	protected $default_attribute_group_id;
	protected $product_mark = ''; // current product identfier in format like '(model: MK1233): '
	
	protected $key_fields = null;
	protected $org_error_handler = null;

	function __construct(&$registry) {
		parent::__construct($registry);

 		$this->kalog = new Log('ka_product_import.log');

 		$this->kadb = new KaDb($this->db);

		if (!isset($this->session->data['ka_pi_m_stat'])) 
			$this->session->data['ka_pi_m_stat'] = array();
		$this->stat = &$this->session->data['ka_pi_m_stat'];

		if (!isset($this->session->data['ka_pi_m_params'])) 
			$this->session->data['ka_pi_m_params'] = array();
		$this->params = &$this->session->data['ka_pi_m_params'];

 		$upd = $this->config->get('ka_pi_update_interval');
 		if ($upd >= 5 && $upd <= 25) {
 			$this->sec_per_cycle = $upd;
 		}
		$this->load->model('catalog/product');
		
		$key_fields = $this->config->get('ka_pi_key_fields');
		if (!is_array($key_fields) || empty($key_fields)) {
			$key_fields = array('model');
		}
		$this->key_fields = $key_fields;
		
		$this->org_error_handler = set_error_handler(array($this, 'import_error_handler'));
	}
	
	/*
		Never give up :)
	*/
	public function import_error_handler($errno, $errstr, $errfile, $errline) {

		if ($errno == E_WARNING) {
			if (preg_match("/iconv stream filter.*invalid multibyte/", $errstr)) {
				return true;
			}
		}
		
		return $this->org_error_handler;
	}

 	/*
 		This function is used for writing messages to log and displaying them instantly during development.
 	*/
 	public function report($msg) {

 		if (defined('KA_DEBUG')) {
 			echo $msg;
 		}

		$this->kalog->write($msg);
 	}


	public function isInstalled() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE code = 'ka_import'");
		if (empty($query->num_rows)) {
			return false;
		}
		return true;
	}

	
	public function isDBPrepared() {

		$prefix = DB_PREFIX;
		if (class_exists('MijoShop')) {
		  $prefix = MijoShop::get('db')->getDbo()->replacePrefix($prefix);
		}
		
		$tbl = $prefix . "ka_import_profiles";
		
		$res = $this->db->query("SHOW TABLES LIKE '$tbl'");
		if (empty($res->rows)) {
			return false;
		}
	
		$tbl = $prefix . "ka_product_import";		
		$res = $this->db->query("SHOW TABLES LIKE '$tbl'");
		if (empty($res->rows)) {
			return false;
		}

		return true;
	}


	public function getSecPerCycle() {
		return $this->sec_per_cycle;
	}


	/*
		PARAMETERS:
			$str   - string
			$chars - a character or array of characters
	*/
	public function strip($str, $chars) {
		$str = trim($str);

		if (empty($chars)) {
			return $str;
		}

		if (!is_array($chars)) {
			$chars = array($chars);
		}

		$pat = array();
		$rep = array();
		foreach($chars as $char) {
			$pat[] = "/(" . preg_quote($char, '/') . ")*$/";
			$rep[] = '';
			$pat[] = "/^(" . preg_quote($char, '/') . ")*/";
			$rep[] = '';
		}

		$res = preg_replace($pat, $rep, $str);
		
		return $res;
	}

	
	/*
		supports values like '.99', '0,99', '$1,200.00'
	*/
	public function formatPrice($price) {
		if (strpos($price, '.') === false) {
			$price = str_replace(",", ".", $price);
		} else {
			$price = str_replace(",", "", $price);
		}
	
		$price = floatval(preg_replace("/^([^\d\-\.]*)(.*)(\D*)$/", '$2', trim($price)));
		return $price;
	}

	/*
		this function should parse the date and try to return formated as YYYY-MM-DD.
	*/
	public function formatDate(&$date) {
	
		$date = trim($date);
		
		// yyyy-mm-dd
		if (preg_match("/^\d{4}-\d{1,2}-\d{1,2}$/", $date, $matches)) {
			return true;

		// mm/dd/yyyy
		} elseif (preg_match("/^(\d{1,2})\/(\d{1,2})\/(\d{2,4})$/", $date, $matches)) {
			if ($matches[3] < 100) {
				$matches[3] += 2000;
			}
			$date = sprintf("%04d-%02d-%02d", $matches[3], $matches[1], $matches[2]);			
			return true;
			
		// dd.mm.yyyy
		} elseif (preg_match("/^(\d{1,2})\.(\d{1,2})\.(\d{2,4})$/", $date, $matches)) {
			if ($matches[3] < 100) {
				$matches[3] += 2000;
			}
			$date = sprintf("%04d-%02d-%02d", $matches[3], $matches[2], $matches[1]);
			return true;
		}
		
		return false;
	}
	
	public function timeFormat($diff) {

 		$periods = array( //suffixes
	    	'd' => array(86400, $this->language->get('text_days')),
	   		'h' => array(3600, $this->language->get('text_hours')),
      		'm' => array(60, $this->language->get('text_minutes')),
			's' => array(1, $this->language->get('text_seconds'))
  		);

		$ret = '';
		foreach ($periods as $k => $v) {
			$num = floor($diff / $v[0]);
				if ($num || !empty($ret) || $k == 's') {
					$ret .= $num . ' ' . $v[1] . ' ';
				}
				$diff -= $v[0] * $num;
		}

	    return $ret;
	}

	
  	/*
  		function converts values like 10M to bytes
	*/
	public function convertToByte($file_size) {
		$val = trim($file_size);
		switch (strtolower(substr($val, -1))) {
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}
		return $val;
	}


	/*
		Function converts value to human readable format like 10.1 Mb 
	*/
	public function convertToMegabyte($val) {
	
		if (!is_numeric($val)) {
			$val = $this->convertToByte($val);
		}

		if ($val >= 1073741824) {
			$val = round($val/1073741824, 1) . " Gb";

		} elseif ($val >= 1048576) {
			$val = round($val/1048576, 1) . " Mb";

		} elseif ($val >= 1024) {
			$val = round($val/1024, 1) . " Kb";
		} else {
			$val = $val . " bytes";
		}

		return $val;
	}

	
	public function getCustomerGroupByName($customer_group) {
			
		static $customer_groups;

		if (isset($customer_groups[$customer_group])) {
			return $customer_groups[$customer_group];
		}
		
		if (version_compare(VERSION, '1.5.3', '>=')) {
		
			$qry = $this->db->query("SELECT cgd.customer_group_id FROM " . DB_PREFIX . "customer_group cg
				INNER JOIN " . DB_PREFIX . "customer_group_description cgd
					ON cg.customer_group_id = cgd.customer_group_id 
				WHERE 
					cgd.name = '$customer_group'"
			);

		} else {
		
			$qry = $this->db->query("SELECT customer_group_id FROM " . DB_PREFIX . "customer_group
			WHERE name = '$customer_group'");
		}

		
		if (empty($qry->row)) {
			$customer_groups[$customer_group] = 0;
			return 0;
		}
		
		$customer_groups[$customer_group] = $qry->row['customer_group_id'];
						
		return $qry->row['customer_group_id'];
	}

	/*
		fseek - works in bytes always (regardless of the stream encoding)
		ftell - measures length in bytes for UTF-8 charset
		fread - reads a number of utf characters (not bytes)

		PARAMETERS:
			offset - length in bytes for utf-8 stream
	*/
	function fseek_utf8($handle, $offset) {

		if (empty($this->filter)) {
			return fseek($handle, $offset);
		}

		rewind($handle);

		$remainder = $offset;	 // remainder in bytes for utf-8 stream

		while ($remainder && !feof($handle)) {

			// calculate max_length in bytes
			//
			// 4 bytes are reserved for possible BOL
			// we assume that the whole string may consists of 2-byte characters
			//
			$max_length = (int)($remainder / 2) - 4;

			if ($max_length > 1) {
				$block_size = min(1024*32, $max_length);
			} else {
				$block_size = 1;
			}

			$buf = fread($handle, $block_size);
			if ($buf === false) {
				return -1;
			} 
			
			$pos = ftell($handle);

			if ($pos > $offset) {
				die('ERROR: ftell() function failed, please contact author of this extension.');
			}

			$remainder = $offset - $pos;
			if ($remainder < 0) {				
				die('ERROR: fseek() function failed, please contact author of this extension.');
			}
		}

		return 0;
	}

	function filesize_utf8($filename) {

		if (($handle = $this->fopen_utf8($filename, $this->params['charset'])) == FALSE) {
			return false;
		}
		
    	if ($this->fseek_utf8($handle, 1024*1024*1024) == -1) {
    		return false;
    	}
		$size = ftell($handle);
		fclose($handle);

		return $size;
	}

	function fopen_utf8($filename, $charset = 'UTF-16') {
		
		if (!is_file($filename)) {
			$this->report("trying to open non-existing file $filename");			
			return false;
		}
	
		@ini_set("auto_detect_line_endings", "1");

		$handle   = fopen($filename, 'r');
		$bom      = fread($handle, 2);

		rewind($handle);

/*
			http://www.unicode.org/faq/utf_bom.html#UTF8
			00 00 FE FF UTF-32, big-endian
			FF FE 00 00 UTF-32, little-endian
			FE FF       UTF-16, big-endian
			FF FE       UTF-16, little-endian
			EF BB BF    UTF-8
*/
		
	    if ($charset == 'UTF-16') {
			if ($bom === chr(0xff).chr(0xfe)  || $bom === chr(0xfe).chr(0xff)) {
				// UTF16 Byte Order Mark present
				$charset = 'UTF-16';
			} else {
				$charset = '';
			}
	 	} elseif ($charset == 'UTF-8') {
	 		if ($bom === chr(0xef).chr(0xbb)) {
	 			fread($handle, 3);
	 		}
	 	}

		if ($charset) {
			$this->filter = stream_filter_append($handle, 'convert.iconv.'.$charset.'/UTF-8');
			setlocale(LC_ALL, 'en_US.UTF8', 'en_US.UTF-8');
		} else {
			fclose($handle);
			$handle = FALSE;
		}
		
		if (constant('KA_ENABLE_MACFIX')) {
			stream_filter_append($handle, 'macfix');
		}
		
		return $handle;
	}

	
	protected function readColumns($file, $sep) {

		if (($handle = $this->fopen_utf8($file, $this->params['charset'])) == FALSE) {
			return false;
		}
		$this->columns = fgetcsv($handle, 0, $sep, $this->enclosure);
		fclose($handle);

		if (empty($this->columns)) {
			return false;
		}
				
		foreach ($this->columns as &$cv) {
			$cv = trim($cv);
		}

    	return true;
	}


	protected function addImportMessage($msg, $type = 'W') {
		static $too_many = false;

		if ($too_many ) return false;

		$prefix = '';
		if ($type == 'W') {
			$prefix = 'WARNING';
		} else if ($type == 'E') {
	  		$prefix = 'ERROR';
	  	} elseif ($type == 'I') {
		  	$prefix = 'INFO';
		}


		if (!empty($this->messages) && count($this->messages) > 200) {
			$too_many = true;
	  		$msg = "too many messages...";
	  	} else {
		  	$msg = $prefix . ': ' . $msg;
		}

	  	$this->kalog->write("Import message: " . $msg);

		$this->messages[] = $msg;
	}


	protected function insertToStores($entity, $entity_id, $stores) {

		$table = $entity . "_to_store";
		$field = $entity . "_id";

		foreach($stores as $sv) {
		 	$rec = array(
		 		'store_id' => $sv,
		 		$field => $entity_id
		 	);
		 	$this->kadb->queryInsert($table, $rec, true);
		}

		return true;
	}


	protected function saveCategory($product_id, $category_chain, $clear_cache = false) {

		if (empty($category_chain)) {
			return false;
		}

		if (!empty($this->params['cat_separator'])) {
			$category_chain = $this->strip($category_chain, $this->params['cat_separator']);
			$category_names = explode($this->params['cat_separator'], $category_chain);
		} else {
			$category_names = array($category_chain);
		}

		$categories = array();

		$parent_id   = 0;
		$category_id = 0;
		foreach ($category_names as $ck => $cv) {

			$cv = trim($cv);

			$new_category = false;
			
			// we use convert function here to make comparison case-insensitive
			// http://dev.mysql.com/doc/refman/5.0/en/cast-functions.html#function_convert
			//
			// http://dev.mysql.com/doc/refman/5.0/en/string-comparison-functions.html#operator_like
			//
			$sel = $this->db->query("SELECT c.category_id FROM " . DB_PREFIX . "category_description cd
				INNER JOIN " . DB_PREFIX . "category c ON cd.category_id=c.category_id
				WHERE language_id = '" . $this->params['language_id'] . "' AND TRIM(CONVERT(name using utf8)) LIKE '". $this->db->escape($this->db->escape($cv)) . "' AND parent_id = '$parent_id'");

			if (empty($sel->row)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category SET 
					parent_id = '$parent_id',
					status ='1',
					image = '',
					date_modified = NOW(), date_added = NOW()
				");
				$category_id = $this->db->getLastId();
				$is_new      = true;

				$rec = array(
					'category_id' => $category_id,
					'language_id' => $this->params['language_id'],
					'name'        => $cv
				);
				$this->kadb->queryInsert('category_description', $rec);
				$this->stat['categories_created']++;

				if (version_compare(VERSION, '1.5.5', '>=')) {
					$level = 0;
					$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$parent_id . "' ORDER BY `level` ASC");
					
					foreach ($query->rows as $result) {
						$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");
						$level++;
					}
										
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");
				}
				
			} else {
				$category_id = $sel->row['category_id'];
			}

			// insert category to stores
			if (!$this->insertToStores('category', $category_id, $this->params['store_ids'])) {
				$this->addImportMessage("Saving the record to stores has failed");
			}

			$parent_id = $category_id;
		}

		if (empty($category_id)) {
			return false;
		}

		$rec = array(
			'product_id'  => $product_id,
			'category_id' => $category_id,
		);
		$this->kadb->queryInsert('product_to_category', $rec, true);
		
		if ($clear_cache) {
			$this->cache->delete('category');
		}
				
		return true;
	}


	protected function isUrl($path) {
    	return preg_match('/^(http|https|ftp):\/\//isS', $path);
	}


	/*
		RETURNS:
			false - on error
			array - on success. It looks like:
				array(
					'status'
						'http_version'  =>
						'status_code'   =>
						'reason_phrase' =>
					'headers'
						'<hdr1>' => value
						'<hdr2>' => value
				)
	*/
	protected function parseHttpHeader($header) {
	
		if (!preg_match("/^(.*)\s(.*)\s(.*)\x0D\x0A/U", $header, $matches)) {
			return false;
		}

		$status = array(
			'http_version'  => $matches[1],
			'status_code'   => $matches[2],
			'reason_phrase' => $matches[3]
		);
		
		$headers = array();		
		$header_lines = explode("\x0D\x0A", $header);
		
		foreach ($header_lines as $line) {
			$pair        = array();
			$value_start = strpos($line, ': ');
			$name        = substr($line, 0, $value_start);
			$value       = substr($line, $value_start + 2);
						
			$headers[$name] = $value;
		}
		
		$result = array(
			'status' => $status,
			'headers' => $headers
		);
		
		return $result;					
	}

	//
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html
	//
	public function getFileContentsByUrl($url) {

		$message = null;
		$this->lastError = '';
		
		if (function_exists('curl_init')) {
		
			$tmp_url        = $url;
			$redirect_count = 0;
						
			do {			
				$headers = '';
				$message = null;
				
				$curl = curl_init($tmp_url);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_HEADER, true);
				curl_setopt($curl, CURLOPT_TIMEOUT, 23);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
				
				// use more attempts to resolve host name. Sometimes curl fails at resolving 
				// a valid host name.
				//
				$resolve_attempt = 0;
				while (true) {
					$response = curl_exec($curl);
					
					if ($response === false) {
					
						// curl_error code 6 means 'could not resolve host name'
						//
						if (curl_errno($curl) == 6) {
							if ($resolve_attempt++ < $this->curl_resolve_attempts) {
								continue;
							}
						}
						$this->lastError = 'CURL error (' . curl_errno($curl) . '): ' . curl_error($curl);
					}
					
					break;					
				}				
				curl_close($curl);
				
				if ($response === false) {
					break;
				}
				
				$msg_start    = strpos($response, "\x0D\x0A\x0D\x0A");
				$header_block = substr($response, 0, $msg_start);
				$headers      = $this->parseHttpHeader($header_block);				
				if (empty($headers)) {
					if (strlen($response) > 1000) {
						$this->lastError = 'No headers received. Response size is ' . strlen($response);
					} else {
						$this->lastError = 'No headers received. Response is "' . $response . '"';
					}
					break;
				}

				if ($headers['status']['status_code'] >= 200 && $headers['status']['status_code'] < 300) {
					$message = substr($response, $msg_start+4);
					break;
					
				} elseif ($headers['status']['status_code'] >= 300 && $headers['status']['status_code'] < 400) {
					$tmp_url = $headers['headers']['Location'];
					continue;
				} else {
					$this->lastError = 'Invalid status code: ' . $headers['status']['status_code'];
					break;
				}
				
			} while (++$redirect_count < 5);
			
		} else {
			if (ini_get('allow_url_fopen')) {
				$message = file_get_contents($url);
			}
		}

		return $message;
	}

		
	protected function normalizeFilename($filename) {

		$chars = array('\\','/','=','.','+','*','?','[','^',']','(','$',']','&','<','>');
   		$filename = str_replace($chars, "_", $filename);

   		return $filename;
	}

	/*
		RETURNS:
			$file - path with file name within image directory or FALSE on error.

		File name can be theortically converted to the right charset but we do not support
		it at this moment.
		$file  = iconv('utf-8', 'Windows-1251', $image);
	*/
	protected function getImageFile($image) {
		$this->lastError = '';
	
		if (empty($image))
			return false;

		$image = trim($image);
		
		$file = '';
		if ($this->isUrl($image)) {

		    $url_info = @parse_url($image);

		    if (empty($url_info)) {
	    		$this->lastError = "Invalid URL data $url";
	    		return false;
			}

	    	// 1) get relative image directory to $images_dir
			//
		    $fullname  = '';
		    $images_dir = str_replace("\\", '/', $this->params['incoming_images_dir']);
      
	    	if (!empty($url_info['path'])) {
	    		$url_info['path'] = urldecode($url_info['path']);
	    		
			    $path_info = pathinfo($url_info['path']);
			    $path_info['dirname'] = $this->strip($path_info['dirname'], array("\\","/"));

			    if (!empty($path_info['dirname'])) {
		    		$images_dir = $images_dir . $path_info['dirname'] . '/';
		    		if (!file_exists(DIR_IMAGE . $images_dir)) {
			    		if (!mkdir(DIR_IMAGE . $images_dir, 0755, true)) {
			    			$this->lastError = "Script cannot create directory: $images_dir";
			    			return false;
		    			}
			    	}
			    }
			    
			    // skip downloading files if they exist on the server
			    // it works for direct URLs only.
			    //
			    if (!empty($this->params['skip_img_download'])) {
					if (empty($url_info['query']) && !empty($path_info['extension'])) {
						$_file = $images_dir . $path_info['basename'];
						if (is_file(DIR_IMAGE . $_file) && filesize(DIR_IMAGE . $_file) > 0) {
							return $_file;
						}
					}
				}
			}
		
			// 2) download file and parse the path
			//
			$image = htmlspecialchars_decode($image);
		    $tmp = str_replace(array(' '), array('%20'), $image);

		    $content = $this->getFileContentsByUrl($tmp);
	    	if (empty($content)) {
		    	$this->lastError = "File content is empty for $tmp (" . $this->lastError . ")";
		    	return false;
	    	}

	    	// save the image to a temporary file
	    	//
		  	$tmp_file = tempnam(DIR_IMAGE . $images_dir, "tmp");
		  	
		  	$size = file_put_contents($tmp_file, $content);
		  	if (empty($size)) {
		  		$this->lastError = "Cannot save new image file: $tmp_file";
			  	return false;
			}

    		$image_info = getimagesize($tmp_file);
    		if (empty($image_info)) {
				$this->lastError = "getimagesize returned empty info for the file: $image";
				return false;
			}
			
			// 3) get a complete image file path
			//
			if (!empty($url_info['query'])) {
				$filename = '';
				if (!empty($path_info['basename'])) {
					$filename = $path_info['basename'];
				}
				$query = $this->normalizeFilename($url_info['query']);
				$filename = $filename . $query . image_type_to_extension($image_info[2]);

			} else {
				$filename = $path_info['basename'];
				if (empty($path_info['extension'])) {
					$filename = $filename . image_type_to_extension($image_info[2]);
				}
			}

			// 4) move the image file to the incoming directory
			//
			if (is_file(DIR_IMAGE . $images_dir . $filename)) {
				@unlink(DIR_IMAGE . $images_dir . $filename);
			}
			
			if (!is_file(DIR_IMAGE . $images_dir . $filename)) {
				if (!rename($tmp_file, DIR_IMAGE . $images_dir . $filename)) {
					$this->lastError = "rename operation failed. from $tmp_file to " . DIR_IMAGE . $images_dir . $filename;
					return false;
				}

				if (!chmod(DIR_IMAGE . $images_dir . $filename, 0644)) {
					$this->lastError = "chmod failed for file: $filename";
					return false;
				}
			}

		   	$file = $images_dir . $filename;
		   	
		} else {
			
			//
			// if the image is a regular file
			//
			$file = $this->params['images_dir'].$image;
			if (!is_file(DIR_IMAGE . $file)) {
				$this->lastError = "File not found " . DIR_IMAGE . $file;
				return false;
			}
		}

		return $file;
	}


	/*
		PARAMETERS:
			weight - value like this 0.0234g

		RETURNS:
			array (
				value           -> 0.0234
				weight_class_id -> 4
			)

		NOTES:
			function does NOT create a new weight class
	*/	
	protected function parseWeight($weight) {

		$pair = array(
			'value'           => 0,
			'weight_class_id' => $this->config->get("config_weight_class_id"),
		);
	
		$matches = array();
		if (preg_match("/([\d\.\,]*)([\D]*)$/", $weight, $matches)) {
			$pair['value'] = $matches[1];
		
			$qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "weight_class_description
				WHERE unit = '" . $this->db->escape($matches[2]) . "'"
			);
	
			if (!empty($qry->row)) {
				$pair['weight_class_id'] = $qry->row['weight_class_id'];
			}
		}
		
		return $pair;
	}

	
	protected function parseLength($length) {
	
		$pair = array(
			'value'           => 0,
			'length_class_id' => $this->config->get("config_length_class_id"),
		);
	
		$matches = array();
		if (preg_match("/(.*)([\D]*)$/U", $length, $matches)) {
			$pair['value'] = $matches[1];
		
			$qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "length_class_description
				WHERE unit = '" . $this->db->escape($matches[2]) . "'"
			);

			if (!empty($qry->row)) {
				$pair['length_class_id'] = $qry->row['length_class_id'];
			}
		}

		return $pair;
	}
	

	protected function generateProductUrl($id, $name) {
	
		if (empty($name) || empty($id)) {
			$this->kalog->write("generateProductUrl:empty parameters");
			return false;
		}
	
		$url = KaUrlify::filter($name);
		if (empty($url)) {
			$this->kalog->write("generateProductUrl: filter returned empty string");
			return false;
		}
		
		$qry = $this->db->query("SELECT url_alias_id FROM " . DB_PREFIX . "url_alias WHERE 
			keyword='" . $this->db->escape($url) . "'");
			
		if (empty($qry->row)) {
			return $url;
		}
		
		$url = $url . "-p-" . $id;
		$qry = $this->db->query("SELECT url_alias_id FROM " . DB_PREFIX . "url_alias WHERE 
			keyword='" . $this->db->escape($url) . "'");

		if (empty($qry->row)) {
			return $url;
		}

		$this->kalog->write("generateProductUrl: cannot find suitable string");
				
		return false;
	}
	
	
	/*
		TRUE  - success
		FALSE - fail. See lastError for details.
	*/
	public function loadFile($params) {

		if (empty($params['file'])) {
			$this->lastError = "File not defined";
			return false;
		}			
		
		if (!is_file($params['file'])) {
			$this->lastError = "File '" . $params['file'] ."' does not exist.";
			return false;
		}

		$this->params = $params;

		if (!$this->readColumns($params['file'], $params['delimiter'])) {
			$this->lastError = 'Column names cannot be read. Please try another charset.';
			return false;
		}
		
		$this->params['columns'] = $this->columns;

		return true;
	}


	/*
		PARAMETERS:
			$matches - an array with field sets
				array(
					'fields' =>
									array(
										'field' => 'model',
										'required' => true,
										'copy'  => true,
										'name'  => 'Model',
										'descr' => 'A unique product code required by Opencart'
									),
									...
					'options'    =>
					'attributes' =>
					...
			 	
			$columns - an array with column names like
				arrray(
					0 => 'model'
					1 => 'name'
					...
				);

		RETURNS:
			true  - on success. In this case the matches array is extended with th 'column' value
							containing the column name associated with the field.

			false - error.
	*/
	public function findMatches(&$matches, $columns) {

		$tmp = array();

		foreach ($columns as $ck => $cv) {
			$cv = mb_strtolower($cv, 'utf-8');
			$tmp[$cv] = $ck;
		}
		$columns = $tmp;

		/*
			'set name' => (
				<field id>
				<readable name for users>
				<prefix>
			);
		*/
		$sets = array(
			'fields'        => array('field', 'name', ''), 
			'attributes'    => array('attribute_id', 'name', 'attribute:'),
			'filter_groups' => array('filter_group_id', 'name', 'filter group:'), 
			'options'       => array('option_id', 'name', 'simple option:'),
			'ext_options'   => array('field', 'name', 'option:'),
			'discounts'     => array('field', 'name', 'discount:'),
			'specials'      => array('field', 'name', 'special:'),
			'reward_points' => array('field', 'name', 'reward point:'),
			'product_profiles' => array('field', 'name', 'product profile:'),
		);

		foreach ($sets as $sk => $sv) {
		
			if (!isset($matches[$sk])) {
				continue;
			}
			
			foreach ($matches[$sk] as $mk => $mv) {

				if (isset($mv['column'])) {
					continue;
				}
			
				$field = mb_strtolower($mv[$sv[0]], 'utf-8');
				$name  = mb_strtolower($mv[$sv[1]], 'utf-8');

				if (isset($columns[$sv[2]. $field])) {
					$mv['column'] = $columns[$sv[2]. $field];
					
				} if (isset($columns[$sv[2]. $name])) {
					
					$mv['column'] = $columns[$sv[2]. $name];
				}
				$matches[$sk][$mk] = $mv;
			}
		}
		
		return true;
	}

	/*
		get product information from the row.

		RETURNS:
			product_id - if product exists
			false      - if product does NOT exist
			
		    lastError  - message. If it is set, the product will not be imported!
	*/
	protected function getProductId($data) {

		$this->lastError = '';
		// get product_id. It finds an existing product or creates a new one.

		$where = array();

		if (!empty($data['product_id'])) {
		
			$where[] = "product_id='" . $this->db->escape($data['product_id']) . "'";
			
		} else {
		
			if (isset($data['model']) && in_array('model', $this->key_fields)) {
				if (!empty($this->params['field_lengths']['model'])) {
					if (utf8_strlen($data['model']) > $this->params['field_lengths']['model']) {
						$this->lastError = 'Model field (' . $data['model'] . ') exceeds the maximum field size(' .
							$this->params['field_lengths']['model'] ."). Product is skipped.";
					};
				}				
				$where[] = "model='" . $this->db->escape($data['model']) . "'";
			}

			if (isset($data['sku']) && in_array('sku', $this->key_fields)) {
				if (!empty($this->params['field_lengths']['sku'])) {
					if (utf8_strlen($data['sku']) > $this->params['field_lengths']['sku']) {
						$this->lastError = 'SKU field (' . $data['sku'] . ') exceeds the maximum field size(' .
							$this->params['field_lengths']['sku'] ."). Product is skipped.";
					};
				}				
				$where[] = "sku='" . $this->db->escape($data['sku']) . "'";
			}
			
			if (isset($data['upc']) && in_array('upc', $this->key_fields)) {
				if (!empty($this->params['field_lengths']['upc'])) {
					if (utf8_strlen($data['upc']) > $this->params['field_lengths']['upc']) {
						$this->lastError = 'UPC field (' . $data['upc'] . ') exceeds the maximum field size(' .
							$this->params['field_lengths']['upc'] ."). Product is skipped.";
					};
				}				
				$where[] = "upc='" . $this->db->escape($data['upc']) . "'";
			}
		}
		
		if (empty($where)) {
			$this->lastError = 'key fields are empty';
			return false;
		}

		$sel = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product AS p 
			WHERE " . implode(" AND ", $where));

		$product_id = (isset($sel->row['product_id'])) ?$sel->row['product_id'] : 0;

		return $product_id;
	}

	/*
		Update existing product. The product record should be created before for new products.

		Returns:
			true  - success
			false - fail 
	*/
	protected function updateProduct($product_id, $data, $is_new) {

		if (empty($product_id)) {
			return false;
		}

		$product = array();

		// set the product status
		//
		if (isset($data['status'])) {
			$product['status'] = (in_array($data['status'], array('1','Y'))) ? 1 : 0;

		} elseif ($is_new) {

			// keep in mind: the option can have an empty value
			//
			if ($this->params['status_for_new_products'] == 'enabled') {
				$product['status'] = 1;
			} elseif ($this->params['status_for_new_products'] == 'disabled') {
				$product['status'] = 0;
			} else {
				if (!empty($data['quantity']) && $data['quantity'] > 0) {
					$product['status'] = 1;
				} else {
					$product['status'] = 0;
				}
			}

		} else {
			// keep in mind: the option can have an empty value
			//
			if ($this->params['status_for_existing_products'] == 'enabled') {
				$product['status'] = 1;
			} elseif ($this->params['status_for_existing_products'] == 'disabled') {
				$product['status'] = 0;
			} elseif ($this->params['status_for_existing_products'] == 'enabled_gt_0') {
				if (!empty($data['quantity']) && $data['quantity'] > 0) {
					$product['status'] = 1;
				} elseif (isset($data['quantity']) && strlen(trim($data['quantity']))) {
					$product['status'] = 0;
				}
			}
		}


		// get a manufacturer id
		//
		if (isset($data['manufacturer'])) {
			$sel = $this->db->query("SELECT manufacturer_id FROM " . DB_PREFIX . "manufacturer AS m
				WHERE name='" . $this->db->escape($data['manufacturer']) . "'");

			if (!empty($sel->row['manufacturer_id'])) {
				$manufacturer_id = $sel->row['manufacturer_id'];
			} elseif (!empty($data['manufacturer'])) {
				$rec = array(
					'name' => $data['manufacturer'],
				);
				$manufacturer_id = $this->kadb->queryInsert("manufacturer", $rec);
			} else {
				$manufacturer_id = 0;
			}
			$product['manufacturer_id'] = $manufacturer_id;

			// insert a new manufacturer to the stores
			//
			if (!empty($manufacturer_id)) {
				if (!$this->insertToStores('manufacturer', $manufacturer_id, $this->params['store_ids'])) {
					$this->addImportMessage("Saving the record to stores has failed");
				}
			}
		}

		// get a tax class id
		//
		if (isset($data['tax_class'])) {
			$sel = $this->db->query("SELECT tax_class_id FROM " . DB_PREFIX . "tax_class AS t
				WHERE title='" . $this->db->escape($data['tax_class']) . "'");

			if (empty($sel->row) && !empty($data['tax_class'])) {
				$this->addImportMessage("Tax class name '$data[tax_class]' not found");
			}
			$tax_class_id = (isset($sel->row['tax_class_id'])) ?$sel->row['tax_class_id'] : 0;
			$product['tax_class_id'] = $tax_class_id;
		}

		// Weight. Sample value: 10.000Kg
		//
		if (isset($data['weight'])) {
			$pair = $this->parseWeight($data['weight']);
			$product['weight']          = $pair['value'];
			$product['weight_class_id'] = $pair['weight_class_id'];
		} elseif ($is_new) {
			$product['weight_class_id'] = $this->config->get('config_weight_class_id');
		}

		// Dimensions. Sample value: 23.07Cm
		//
		$length_params = array('length', 'height', 'width');
		foreach ($length_params as $lv) {

			if (isset($data[$lv])) {
				$pair = $this->parseLength($data[$lv]);
				$product[$lv]               = $pair['value'];
				$product['length_class_id'] = $pair['length_class_id'];
			} elseif ($is_new) {
				$product['length_class_id'] = $this->config->get('config_length_class_id');
			}
		}
		
		// insert the product to the selected store
		//
	    if (!$this->insertToStores('product', $product_id, $this->params['store_ids'])) {
    		$this->addImportMessage("Saving the record to stores has failed");
	    }

		// insert language-specific options for the product
		//
		$lang = array(
			'product_id'  => $product_id,
			'language_id' => $this->params['language_id'],
		);
		
		if (isset($data['name'])) {
			$lang['name'] = trim($data['name']);
		}

		if (isset($data['description'])) 
			$lang['description'] = trim($data['description']);

		if (isset($data['meta_description']))
			$lang['meta_description'] = trim($data['meta_description']);

		if (isset($data['meta_keyword']))
			$lang['meta_keyword'] = trim($data['meta_keyword']);

		if (version_compare(VERSION, '1.5.4', '>=')) {
		
			if (isset($data['product_tags']))
				$lang['tag'] = trim($data['product_tags']);
				
		} else {

			// insert product tags
			//
			if (isset($data['product_tags'])) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE 
					product_id = '" . $product_id. "'
					AND language_id = '" . (int)$this->params['language_id'] . "'
				");
			
				$tags = explode(',', $data['product_tags']);
				foreach ($tags as $tag) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_tag SET product_id = '" . $product_id . "', 
						language_id = '" . (int)$this->params['language_id'] . "', 
						tag = '" . $this->db->escape(trim($tag)) . "'"
					);
				}
			}
		}
			
		// stock status
		//
		if (!empty($data['stock_status'])) {
			$qry = $this->db->query("SELECT stock_status_id FROM " . DB_PREFIX . "stock_status 
				WHERE '" . $this->db->escape(mb_strtolower($data['stock_status'], 'utf-8')) . "' LIKE LOWER(name)
					AND language_id = '" . $this->params['language_id'] . "'");
					
			if (empty($qry->row)) {
				$this->addImportMessage("stock status not found - $data[stock_status]");
				if ($is_new) {
					$product['stock_status_id'] = $this->config->get('config_stock_status_id');
				}
			} else {
				$product['stock_status_id'] = $qry->row['stock_status_id'];
			}
		} elseif ($is_new) {
			$product['stock_status_id'] = $this->config->get('config_stock_status_id');
		}

		// update product description or insert new one
		//
		$qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description
			WHERE 
				product_id = '" . $product_id . "'
				AND language_id = '" . $this->params['language_id'] . "'"
		);
		if (!empty($qry->row)) {
			$lang = array_merge($qry->row, $lang);
		}
		$this->kadb->queryInsert('product_description', $lang, true);

		if (isset($data['price']) && strlen($data['price'])) {
			$product['price'] = $this->formatPrice($data['price']);
			if (!empty($this->params['price_multiplier'])) {
				$product['price'] = $product['price'] * $this->params['price_multiplier'];
			}
		}

		// insert an image
		//
		if (isset($data['image'])) {
			if (!empty($data['image'])) {
				$file = $this->getImageFile($data['image']);
				if ($file === false) {
					if (!empty($this->lastError)) {
						$this->addImportMessage($this->product_mark . "image cannot be saved - " . $this->lastError);
					}
				} elseif (!empty($file)) {
					$product['image'] = $file;
				}
			} else {
				$product['image'] = '';
			}
		}

		// insert date available
		//
		if (!empty($data['date_available'])) {
			if (!$this->formatDate($data['date_available'])) {
				$this->addImportMessage($this->product_mark . "Wrong date format in 'date available' field. We recommend to use YYYY-MM-DD. Ex. 2012-11-28");
				if ($is_new) {
					$product['date_available'] = '2000-01-01';
				}
			} else {
				if ($data['date_available'] != '0000-00-00') {
					$product['date_available'] = $data['date_available'];
				} else {
					$product['date_available'] = '2000-01-01';
				}
			}
		} elseif ($is_new) {
			$product['date_available'] = '2000-01-01';
		}
		
		$this->kadb->queryUpdate('product', $product, "product_id='$product_id'");

		// insert seo keyword
		//		
		if (isset($data['seo_keyword']) || $this->generate_urls) {
	
			if (!empty($data['seo_keyword'])) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . $product_id. "'");
				
			} elseif ($this->generate_urls) {
				$qry = $this->db->query("SELECT url_alias_id FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . $product_id. "'");

				if (empty($qry->row) && !empty($lang['name'])) {
					$data['seo_keyword'] = $this->generateProductUrl($product_id, $lang['name']);
					if (empty($data['seo_keyword'])) {
						$this->addImportMessage($this->product_mark . "Unable to generate SEO friendly URL for product ($lang[name])");
					}
				}
			}
			
			if (!empty($data['seo_keyword'])) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET 
					query = 'product_id=" . (int)$product_id . "', 
					keyword = '" . $this->db->escape($data['seo_keyword']) . "'"
				);
			}
		}

		// produt layout
		//		
		if (isset($data['layout'])) {
			$qry = $this->db->query('SELECT * FROM ' . DB_PREFIX . "layout WHERE
				name = '" . $this->db->escape($data['layout']) . "'"
			);			
			
			if (!empty($qry->row['layout_id'])) {
				$layout_id = $qry->row['layout_id'];
			} else {
				$layout_id = 0;
			}
			
			foreach ($this->params['store_ids'] as $store_id) {
				$this->db->query("REPLACE INTO " . DB_PREFIX . "product_to_layout SET 
					product_id = '" . (int)$product_id . "', 
					store_id   = '" . (int)$store_id . "', 
					layout_id  = '" . (int)$layout_id . "'"
				);
			}
		}

		return true;
	}

	/*
		Assign categories to product by category_id or by category name.
		
	*/
	protected function saveCategories($product_id, $data, $delete_old, $is_new) {

		$category_assigned = false;
		
		if (!empty($data['category_id']) || !empty($data['category'])) {
			if ($delete_old) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category
					WHERE product_id = '$product_id'");
			}
		}
		
		$multicat_sep = $this->config->get('ka_pi_multicat_separator');
		$cats_list    = array();

		// assign categories by category_id
		//
		if (!empty($data['category_id'])) {

			if (!empty($multicat_sep)) {
				$cats_list = explode($multicat_sep, $data['category_id']);	
			} else {
				$cats_list = array($data['category_id']);
			}
			foreach ($cats_list as $cat) {
				$cat = (int)$cat;
				$qry = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category 
					WHERE category_id = '" . $cat . "'"
				);

				if (!empty($qry->row)) {
					$rec = array(
						'product_id'  => $product_id,
						'category_id' => $qry->row['category_id'],
					);
					$this->kadb->queryInsert('product_to_category', $rec, true);
					$category_assigned = true;
				} else {
					$this->addImportMessage($this->product_mark . " category ID not found '$cat'");
				}
			}
		}

		// assign categories by name
		//				
		if (!empty($data['category']) && empty($data['category_id'])) {

			// insert categories
			//
			if (!empty($data['category'])) {
				if (!empty($multicat_sep)) {
					$cats_list = explode($multicat_sep, $data['category']);	
				} else {
					$cats_list = array($data['category']);
				}
				foreach ($cats_list as $cat) {
					if ($this->saveCategory($product_id, $cat)) {
						$category_assigned = true;
					}
				}
			}
		}

		// assign the default category for new products if no categories were assigned
		//
		if (!$category_assigned && $is_new) {
			if (!empty($this->params['default_category_id'])) {
				$category_id = $this->params['default_category_id'];
				$rec = array(
					'product_id'  => $product_id,
					'category_id' => $category_id,
				);
			}
			$this->kadb->queryInsert('product_to_category', $rec, true);			
		}
		
		$this->cache->delete('category');
	}


	protected function saveAdditionalImages($product_id, $data, $delete_old, $is_new) {

		if (empty($data['additional_image'])) {
			return true;
		}

		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_image
				WHERE product_id = '$product_id'");
		}

		// insert an additional product image
		//
		
		if (!empty($this->params['ka_pi_image_separator'])) {
			$images = explode($this->params['ka_pi_image_separator'], $data['additional_image']);
		} else {
			$images = array($data['additional_image']);
		}

		$sort_order = 0;
		if (!$delete_old && !$is_new) {
			$qry = $this->db->query("SELECT sort_order FROM " . DB_PREFIX . "product_image 
				WHERE product_id = '" . (int)$product_id . "'"
			);
			if (!empty($qry->row['sort_order'])) {
				$sort_order = $qry->row['sort_order'];
			}
		}
		
		foreach ($images as $image) {
		
			if (empty($image))
				continue;
				
			$file = $this->getImageFile($image);
			if ($file === false) {
				$this->addImportMessage($this->product_mark . "image cannot be saved - " . $this->lastError);

			} elseif (!empty($file)) {

				$qry = $this->db->query("SELECT product_image_id FROM " . DB_PREFIX . "product_image
					WHERE image = '" . $this->db->escape($file) . "' AND product_id = '$product_id'");

				if (empty($qry->row)) {
				
					$sort_order += 5;
				
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET 
						product_id = '" . $product_id . "', 
						sort_order = " . $sort_order . ",
						image = '" . $this->db->escape($file) . "'"
					);
				}
			}
		}
	}


	protected function saveAttributes($row, $product, $delete_old) {

		if (empty($this->params['matches']['attributes'])) {
			return true;
		}

		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute
				WHERE
					product_id = '$product[product_id]' 
					AND	language_id = '" .$this->params['language_id'] . "'"
			);
		}

		$data = array();
		foreach ($this->params['matches']['attributes'] as $ak => $av) {
			if (empty($av['column']))
				continue;

			$val = $row[$av['column']];
			if (empty($val)) {
				continue;
			}
			
			if (strcasecmp($val, '[DELETE]') == 0)  {
			
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute
					WHERE
						product_id = '$product[product_id]' 
						AND	attribute_id = '" .$av['attribute_id'] . "'"
				);
				
			} else {
				$rec = array(
					'product_id'   => $product['product_id'],
					'attribute_id' => $av['attribute_id'],
					'language_id'  => $this->params['language_id'],
					'text'         => $val
				);

				$this->kadb->queryInsert('product_attribute', $rec, true);
			}
		}

		return true;
	}

	
	protected function saveFilters($row, $product, $delete_old) {

		if (empty($this->params['matches']['filter_groups'])) {
			return true;
		}

		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter
				WHERE
					product_id = '$product[product_id]'"
			);
		}

		$data = array();
		foreach ($this->params['matches']['filter_groups'] as $ak => $av) {
			if (empty($av['column']))
				continue;

			$val = $row[$av['column']];

			$sep = $this->config->get('ka_pi_general_separator');
			if (!empty($sep)) {
				$filter_values = explode($sep, $val);
			} else {
				$filter_values = array($val);
			}
			
			foreach ($filter_values as $fv) {
			
				if (empty($fv)) {
					continue;
				}
						
				// find the filter_id
				//
				$filter_id = false;
				$filter_group_id = $av['filter_group_id'];
				
				$qry = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "filter_description WHERE 
					language_id = '" . $this->params['language_id'] . "' 
					AND TRIM(CONVERT(name using utf8)) LIKE '". $this->db->escape($this->db->escape($fv)) . "' 
					AND filter_group_id = '$filter_group_id'"
				);
				
				// create a new filter if required
				//
				if (empty($qry->row)) {
				
					// add a new filter value
					//
					$this->db->query("INSERT INTO " . DB_PREFIX . "filter SET 
						filter_group_id = '" . (int)$filter_group_id . "', 
						sort_order = 0"
					);
					$filter_id = $this->db->getLastId();
					
					if (empty($filter_id)) {
						$this->report('filter was not created');
						continue;
					}
						
					$rec = array(
						'filter_id'       => $filter_id,
						'filter_group_id' => $filter_group_id,
						'language_id'     => $this->params['language_id'],
						'name'            => $fv
					);
					$this->kadb->queryInsert('filter_description', $rec);

				} else {
					$filter_id = $qry->row['filter_id'];
				}
				
				// assign the filter
				//
				$this->db->query("REPLACE INTO " . DB_PREFIX . "product_filter SET 
					product_id = '" . (int)$product['product_id'] . "', 
					filter_id = '" . (int)$filter_id . "'"
				);
			}
		}

		return true;
	}
	
	/*
		Function saves one product option.

		PARAMETERS:
			...
			option['value'] - it can be empty for text options (and maybe other option types)
			...
			
		RETURNS:
			true  - success
			false - error / fail
	*/	
	protected function saveOption($product, $option) {

		$is_new = false;
		$data = array();
	
		// validate parameters
		//
		$option['type'] = strtolower($option['type']);
		if (!in_array($option['type'], $this->option_types)) {
			$this->addImportMessage("Invalid option type - $option[type]");
			return false;
		}

		$option['value'] = trim($option['value']);

		// STAGE 1: find the option in the store
		//
		$qry = $this->db->query("SELECT o.option_id FROM `" . DB_PREFIX ."option` o
			INNER JOIN " . DB_PREFIX . "option_description od ON o.option_id = od.option_id
			WHERE
				language_id = '" . $this->params['language_id'] . "' 
				AND	o.type='$option[type]' 
				AND od.name='" . $this->db->escape($option['name']) . "'"
		);

		if (empty($qry->row)) {

			// if the option is NOT found
			//
			if (empty($this->params['create_options'])) {
				$this->addImportMessage("Option '$option[name]' does not exist in the store. If you want to create options from the file then 
					enable the appropriate setting on the extension settings page."
				);
				return false;
			}

			$rec = array(
				'type'   => $option['type'],
			);
			
			if (!empty($option['group_sort_order'])) {
				$rec['sort_order'] = $option['group_sort_order'];
			};

			$option_id = $this->kadb->queryInsert('option', $rec);
			if (empty($option_id)) {
				$this->addImportMessage("Cannot create a new option");
				return false;
			}
			
			$is_new = true;

			$rec = array(
				'option_id'   => $option_id,
				'language_id' => $this->params['language_id'],
				'name'        => $option['name']
			);
			$this->kadb->queryInsert('option_description', $rec);

			$this->addImportMessage("New option created - $option[name]", 'I');

			// repeat option request
			//
			$qry = $this->db->query("SELECT o.option_id FROM `" . DB_PREFIX ."option` o
				INNER JOIN " . DB_PREFIX . "option_description od ON o.option_id = od.option_id
				WHERE
					language_id = '" . $this->params['language_id'] . "' 
					AND	o.type='$option[type]' 
					AND od.name='" . $this->db->escape($option['name']) . "'"
			);
			
		} else {
		
			// update group sort order for existing option group
			//
			if (!empty($option['group_sort_order'])) {
				$rec = array(
					'sort_order' => $option['group_sort_order'],
				);
				$this->kadb->queryUpdate('option', $rec, "option_id = '" . $qry->row['option_id'] . "'");
			}		
		}

		//
		// STAGE 2: option found/created and we are going to assing it to a product
		//		
		$option_id = $option['option_id'] = $qry->row['option_id'];

		// find product option id or insert a new one
		//
	   	$qry = $this->db->query("SELECT product_option_id FROM " . DB_PREFIX . "product_option WHERE
	 		product_id='$product[product_id]'
   			AND option_id='$option[option_id]'");
   			
		$rec = array(
			'required' => $option['required'],
		);
		if ($this->options_with_def_values) {
			$rec['option_value'] = $option['value'];
		}
   		
	   	if (empty($qry->row['product_option_id'])) {

			$rec['product_id'] = $product['product_id'];
			$rec['option_id']  = $option['option_id'];
			
			$product_option_id = $this->kadb->queryInsert('product_option', $rec);
		} else {
			$product_option_id = $qry->row['product_option_id'];
			$this->kadb->queryUpdate('product_option', $rec, "product_option_id = '$product_option_id'");
		}

		/*
			There are two option types in Opencart:
				simple   - user enters a custom value manually
				extended - options with predefined values
		*/
		if (in_array($option['type'], $this->extended_types)) {

			// find option value or insert a new one
			//
			$qry = $this->db->query("SELECT option_value_id FROM " . DB_PREFIX . "option_value_description WHERE 
				option_id = '" . $option_id . "'
				AND language_id = '" . $this->params['language_id'] . "'
				AND name='" . $this->db->escape($option['value']) . "'");
			
			if (empty($qry->row['option_value_id'])) {
				$rec = array(
					'option_id' => $option['option_id'],					
				);
				
				$option_value_id = $this->kadb->queryInsert('option_value', $rec);

				$rec = array(
					'option_id'       => $option['option_id'],
					'option_value_id' => $option_value_id,
					'language_id'     => $this->params['language_id'],
					'name'            => $option['value']
				);

				$this->kadb->queryInsert('option_value_description', $rec);

			} else {
				$option_value_id = $qry->row['option_value_id'];
			}

			//
			// collect in $rec array extra option parameters and update the option if required
			//
			$rec = array();
			
			if (in_array($option['type'], $this->options_with_images) && !empty($option['image'])) {
				$file = $this->getImageFile($option['image']);
				if ($file === false) {
					$this->addImportMessage("image cannot be saved - " . $this->lastError);
				} else {
					$rec['image'] = $file;
				}
			}
			
			if (isset($option['sort_order'])) {
				$rec['sort_order'] = $option['sort_order'];
			}
			
			if (!empty($rec)) {
				$this->kadb->queryUpdate('option_value', $rec, "option_value_id = '$option_value_id'");
			}
			
	    	// assign option value for product
    		//
     		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE
     		product_option_id='$product_option_id'
     		AND option_value_id='$option_value_id'");

			$rec = array(
				'product_option_id' => $product_option_id,
				'product_id'        => $product['product_id'],
				'option_id'         => $option_id,
				'option_value_id'   => $option_value_id
			);
			
			if (isset($option['quantity'])) {
				$rec['quantity'] = $option['quantity'];
			}
			
			if (isset($option['subtract'])) {
				$rec['subtract'] = $option['subtract'];
			}
			
			if (isset($option['price'])) {
				$rec['price']        = abs($this->formatPrice($option['price']));
				$rec['price_prefix'] = ($option['price'] < 0 ? '-':'+');
			}
			
			if (isset($option['points'])) {
				$rec['points']        = abs($option['points']);
				$rec['points_prefix'] = ($option['points'] < 0 ? '-':'+');				
			}

			if (isset($option['weight'])) {
				$rec['weight']        = abs($option['weight']);
				$rec['weight_prefix'] = ($option['weight'] < 0 ? '-':'+');
			}
			
			$product_option_value_id = $this->kadb->queryInsert('product_option_value', $rec);		
		}

		return true;
		
	}
	

	protected function saveOptions($row, $product, $delete_old) {

		if (!empty($this->params['matches']['options']) ||
			!empty($this->params['matches']['ext_options'])
			) 
		{
			if ($delete_old) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . $product['product_id']. "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . $product['product_id']. "'");
			}
		}
		
		// STAGE 1: process simple options from the selected columns
		//
		if (!empty($this->params['matches']['options'])) {
			foreach ($this->params['matches']['options'] as $ok => $ov) {
				if (empty($ov['column']))
					continue;

				$val = $row[$ov['column']];
				
				if (empty($val)) {
					continue;
				}
				
				if (!empty($this->params['ka_pi_options_separator'])) {
					$option_values = explode($this->params['ka_pi_options_separator'], $val);
				} else {
					$option_values = array($val);
				}
				
				foreach ($option_values as $ovalue) {
					
					$option = array(
						'name'     => $ov['name'],
						'type'     => $ov['type'],
						'value'    => $ovalue,
						'required' => $ov['required']
					);
					
					$this->saveOption($product, $option);
				}
			}
		}
		
		// STAGE 2: process extended options
		//
		if (!empty($this->params['matches']['ext_options'])) {
			$option = array();
			foreach ($this->params['matches']['ext_options'] as $ck => $cv) {
				if (empty($cv['column']))
					continue;

				$val = $row[$cv['column']];
				$option[$cv['field']] = trim($val);
			}
			
			if (!empty($option['name'])) {
			
				if (!empty($this->params['ka_pi_options_separator'])) {
				
					$multi_options = array();
					$option_keys = array('value', 'quantity', 'subtract', 'image', 'price', 'points', 'weight', 'sort_order');

					$max_option_length = 0;
					foreach ($option_keys as $key) {
						if (isset($option[$key])) {
							$multi_options[$key] = explode($this->params['ka_pi_options_separator'], $option[$key]);
							$max_option_length = max($max_option_length, count($multi_options[$key]));
						}
					}
					
					for($i = 0; $i < $max_option_length; $i++) {
						$tmp_option = $option;
						
						foreach($multi_options as $key => $val) {
							if (isset($option[$key][$i])) {
								$tmp_option[$key] = $multi_options[$key][$i];
							} else {
								$tmp_option[$key] = $multi_options[$key][count($option[$key]) - 1];
							}
						}
						
						$this->saveOption($product, $tmp_option);
					}
					
				} else {
					$this->saveOption($product, $option);
				}
			}
		}
	}


	protected function saveDiscounts($row, $product, $delete_old) {

		if (empty($this->params['matches']['discounts'])) {
			return true;
		}

		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '$product[product_id]'");
		}

		$data = array();

		$record_valid = false;		
		foreach ($this->params['matches']['discounts'] as $ak => $av) {
		
			if (empty($av['column']))
				continue;

			$val = trim($row[$av['column']]);

			if ($av['field'] == 'price') {
				if (strlen($val) > 0) {
					$record_valid = true;
				}
				$val = $this->formatPrice($val);

			} elseif (in_array($av['field'], array('date_start', 'date_end'))) {

				if (!$this->formatDate($val)) {
					if (!empty($val)) {
						$this->addImportMessage("Wrong date format in 'discount' record. product_id = $product[product_id]");
					}
					$val = '0000-00-00';
				}

			} elseif ($av['field'] == 'customer_group') {
				if (empty($val)) {
					$data['customer_group_id'] = $this->config->get('config_customer_group_id');
				} else {
					$data['customer_group_id'] = $this->getCustomerGroupByName($val);
				}
				continue;
			}

			$data[$av['field']] = $val;
		}
		
		if (!$record_valid) {
			return false;
		}

		if (empty($data['customer_group_id'])) {
			return false;
		}
				
		$data['product_id'] = $product['product_id'];

		$this->kadb->queryInsert('product_discount', $data);

		return true;
	}


	protected function saveSpecials($row, $product, $delete_old) {

		if (empty($this->params['matches']['specials'])) {
			return true;
		}
		
		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '$product[product_id]'");
		}

		$data = array();
		
		foreach ($this->params['matches']['specials'] as $ak => $av) {
			if (empty($av['column']))
				continue;

			$val = trim($row[$av['column']]);

			if ($av['field'] == 'price') {
				$val = $this->formatPrice($val);

			} elseif (in_array($av['field'], array('date_start', 'date_end'))) {

				if (!$this->formatDate($val)) {
					if (!empty($val)) {
						$this->addImportMessage("Wrong date format in 'special' record. product_id = $product[product_id]");
					}
					$val = '0000-00-00';
				}

			} elseif ($av['field'] == 'customer_group') {
				$data['customer_group_id'] = $this->getCustomerGroupByName($val);
				continue;
			}

			$data[$av['field']] = $val;
		}
		
		if (empty($data['customer_group_id']) && empty($data['price'])) {
			return true;
		}

		$data['product_id'] = $product['product_id'];

		if (empty($data['customer_group_id'])) {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		if (!isset($data['priority'])) {
			$data['priority'] = 1;
		}

		// find duplicate
		//		
		$key_fields = array('product_id', 'customer_group_id', 'price', 'priority', 
			'date_start', 'date_end');
		$data_keys = array_keys($data);
		$diff      = array_diff($key_fields, $data_keys);
		
		if (empty($diff)) {
		
			$qry = $this->db->query("SELECT product_special_id FROM " . DB_PREFIX . "product_special
				WHERE product_id = '$data[product_id]' 
					AND customer_group_id = '$data[customer_group_id]'
					AND price = '$data[price]'
					AND priority = '$data[priority]'
					AND date_start = '$data[date_start]'
					AND date_end = '$data[date_end]'
			");			
			if (!empty($qry->row)) {
				return true;
			}
		}

		$this->kadb->queryInsert('product_special', $data);

		return true;
	}


	protected function saveRewardPoints($row, $product, $delete_old) {

		if (empty($this->params['matches']['reward_points'])) {
			return true;
		}
		
		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '$product[product_id]'");
		}

		$data = array();
		foreach ($this->params['matches']['reward_points'] as $ak => $av) {
			if (empty($av['column']))
				continue;

			$val = $row[$av['column']];

			if ($av['field'] == 'customer_group') {				
				$data['customer_group_id'] = $this->getCustomerGroupByName($val);
				continue;
			}

			$data[$av['field']] = $val;
		}

		if (empty($data['points'])) {
			return false;
		}

		$data['product_id'] = $product['product_id'];

		if (empty($data['customer_group_id'])) {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE 
			product_id = '$product[product_id]'
			AND customer_group_id = '$data[customer_group_id]'
		");
		$this->kadb->queryInsert('product_reward', $data);

		return true;
	}

	
	/* 
		$data     - array of file data split by columns
		$product  - product data array
	*/
	protected function saveRelatedProducts($data, $product, $delete_old) {

		if (empty($data['related_product'])) {
			return true;
		}
	
		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '$product[product_id]'");
		}
	
		// get the array of related models
		//
		$related = array();
		$sep     = $this->config->get('ka_pi_related_products_separator');		
		if (!empty($sep)) {
			$related = explode($sep, $data['related_product']);
		} else {
			$related = array($data['related_product']);
		}
		
		foreach ($related as $rv) {
			if (empty($rv)) {
				continue;
			}

			$qry = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product
				WHERE model = '" . $this->db->escape($rv) . "'");
			
			if (empty($qry->row)) {
				continue;
			}

			// link to all products with found model regardless of their number
			//
			foreach ($qry->rows as $row) {
			
				$rec = array(
					'product_id' => $product['product_id'],
					'related_id' => $row['product_id']
				);
				$this->kadb->queryInsert('product_related', $rec, true);

				$rec = array(
					'product_id' => $row['product_id'],
					'related_id' => $product['product_id']
				);
				$this->kadb->queryInsert('product_related', $rec, true);
			}
		}

		return true;
	}


	protected function saveProductProfiles($row, $product, $delete_old) {

		if (empty($this->params['matches']['product_profiles'])) {
			return true;
		}
		
		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_profile WHERE product_id = '$product[product_id]'");
		}

		$data = array();
		foreach ($this->params['matches']['product_profiles'] as $ak => $av) {
			if (empty($av['column']))
				continue;

			$val = $row[$av['column']];

			if ($av['field'] == 'customer_group') {
				$data['customer_group_id'] = $this->getCustomerGroupByName($val);
				if (empty($data['customer_group_id'])) {
					return false;
				}
				continue;
				
			} elseif ($av['field'] == 'name') {
				$qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "profile_description");
				if (empty($qry->row)) {
					return false;
				}
				$data['profile_id'] = $qry->row['profile_id'];
				continue;
			}

			$data[$av['field']] = $val;
		}

		$data['product_id'] = $product['product_id'];

		if (empty($data['customer_group_id'])) {
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_profile WHERE 
			product_id = '$product[product_id]'
			AND customer_group_id = '$data[customer_group_id]'
		");
		$this->kadb->queryInsert('product_profile', $data);

		return true;
	}

		
	protected function saveDownloads($data, $product, $delete_old) {

		if (empty($data['downloads'])) {
			return true;
		}
	
		if ($delete_old) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '$product[product_id]'");
		}
	
		$downloads = array();
		$sep = $this->config->get('ka_pi_general_separator');
		if (!empty($sep)) {
			$downloads = explode($sep, $data['downloads']);
		} else {
			$downloads = array($data['downloads']);
		}
		
		foreach ($downloads as $dv) {
			if (empty($dv)) {
				continue;
			}

			// 1) detect file parts from the file name
			//
			$ext = $dest_filename = $mask = '';
			$info = pathinfo($dv);

			if ($this->params['file_name_postfix'] == 'generate') {				
				$mask = $dv;
				$ext  = md5(mt_rand());
				
			} elseif ($this->params['file_name_postfix'] == 'detect') {
				$mask = $info['filename'];
				$ext  = $info['extension'];
				
			} else {
				$mask = $dv;
			}
			
			$filename = $mask . (!empty($ext) ? '.'.$ext : '');
			
			// 2) find this file in downloads
			//			
			$qry = $this->db->query('SELECT * FROM ' . DB_PREFIX . "download WHERE
				mask = '" . $this->db->escape($mask) . "'"
			);
			
			if (!empty($qry->row)) {
				$download_id = $qry->row['download_id'];
			} else {
			
				$data = array(
					'src_file'  => $dv,
					'filename'  => $filename,
					'mask'      => $mask,
					'remaining' => 50,
				);
				$download_id = $this->addDownload($data);
			}
			
			// 3) connect product and download record
			//
			if (!empty($download_id)) {
				$rec = array(
					'product_id'  => $product['product_id'],
					'download_id' => $download_id
				);
				$this->kadb->queryInsert('product_to_download', $rec, true);
			}
		}

		return true;
	}
	
	
	protected function addDownload($data) {
	
		$src_file = $this->params['download_source_dir'] . $this->strip($data['src_file'], array("\\", "/"));
		
		// 1) copy the file to downloads directory
		//
		if (!file_exists($src_file)) {
			$this->addImportMessage("File does not exist: $src_file");
			return false;			
		}
		
		$dest_file = DIR_DOWNLOAD . $data['filename'];
		if (!copy($src_file, $dest_file)) {
			$this->addImportMessage("Cannot copy file from $src_file to $dest_file.");
			return false;
		}
		
		// 2) add a new record to the database
		//
      	$this->db->query("INSERT INTO " . DB_PREFIX . "download SET 
      		filename   = '" . $this->db->escape($data['filename']) . "', 
      		mask       = '" . $this->db->escape($data['mask']) . "', 
      		remaining  = '" . (int)$data['remaining'] . "', 
      		date_added = NOW()"
      	);

      	$download_id = $this->db->getLastId(); 
      	
       	$this->db->query("INSERT INTO " . DB_PREFIX . "download_description SET 
       		download_id = '" . (int)$download_id . "', 
       		language_id = '" . (int)$this->params['language_id'] . "', 
       		name = '" . $this->db->escape($data['mask']) . "'"
       	);
       	
       	return $download_id;
	}
	

	/*
		it works with varchar(...) type only right now
		
		RETURNS:
			array where keys are fields, values are field lengths
	*/
	protected function getFieldLengths($table, $fields) {
	
		$qry = $this->db->query("DESCRIBE `$table`");
		if (empty($qry->rows) || empty($fields)) {
			return false;
		}
		
		$ret = array_combine($fields, array_fill(0, count($fields), 0));
		foreach ($qry->rows as $f) {

			if (!in_array($f['Field'], $fields)) {
				continue;
			}
		
			if (!preg_match("/varchar\((\d*)\)/", $f['Type'], $matches)) {
				continue;
			}
			
			$ret[$f['Field']] = intval($matches[1]);
		}
		
		return $ret;	
	}
	
	
	public function initImport($params) {

		if (!$this->loadFile($params)) {
			$this->report("initImport: file was not loaded. Last Error:" . $this->lastError);
			return false;
		}

		// clean up the temporary table		
		//
		$this->db->query("DELETE FROM " . DB_PREFIX . "ka_product_import
				WHERE 
					token = '" . $this->session->data['token'] . "'
					OR UNIX_TIMESTAMP(NOW() - added_at)"
		);


		$this->params['images_dir']   = $this->strip($this->params['images_dir'], array("\\", "/"));
		if (!empty($this->params['images_dir'])) {
			$this->params['images_dir'] = $this->params['images_dir'] . '/';
		}

		// store relative path in incoming images directory 
		// important: if incoming_images_dir exists then it should ends with slash
		//
		$incoming_images_dir = '';
		if (!empty($this->params['incoming_images_dir'])) {
			$this->params['incoming_images_dir'] = $this->strip($this->params['incoming_images_dir'], array("\\", "/"));
			if (!empty($this->params['incoming_images_dir'])) {
				$incoming_images_dir = $this->params['incoming_images_dir'] . '/';
			}
		}
		$this->params['incoming_images_dir'] = $incoming_images_dir;

		// remove sets if they are not required in the current import
		//
		$sets = $this->getFieldSets();
		$this->copyMatches($sets, $params['matches'], $this->columns);
		foreach ($sets as $sk => $sv) {
			$has_column = false;
			foreach ($sv as $msk => $msv) {
				if (isset($msv['column'])) {
					$has_column = true;
				}
			}
			
			if (!$has_column) {
				unset($sets[$sk]);
			}
		}
		$this->params['matches'] = $sets;

		$this->params['status_for_new_products']      = $this->config->get('ka_pi_status_for_new_products');
		$this->params['status_for_existing_products'] = $this->config->get('ka_pi_status_for_existing_products');
		$this->params['ka_pi_options_separator']      = $this->config->get('ka_pi_options_separator');
		$this->params['skip_img_download']            = $this->config->get('ka_pi_skip_img_download');
		$this->params['ka_pi_image_separator']        = str_replace(array('\r','\n'), array("\r", "\n"), $this->config->get('ka_pi_image_separator'));

		$download_source_dir = '';
		if (!empty($this->params['download_source_dir'])) {
			$this->params['download_source_dir'] = $this->strip($this->params['download_source_dir'], array("\\", "/"));
			if (!empty($this->params['download_source_dir'])) {
				$download_source_dir = dirname(DIR_APPLICATION) . DIRECTORY_SEPARATOR . $this->params['download_source_dir'] . '/';
			}
		}
		$this->params['download_source_dir'] = $download_source_dir;
		
		$this->params['field_lengths'] = $this->getFieldLengths(DB_PREFIX . 'product', array('model', 'sku', 'upc'));
		
		$this->stat = array(
			'filesize'         => $this->filesize_utf8($params['file']),
			'offset'           => 0,

			'started_at'       => time(),

			'lines_processed'  => 0,
			'products_created' => 0,			
			'products_updated' => 0,
			'products_deleted' => 0,

			'categories_created' => 0,

			'errors'           => array(),
			'status'           => 'not_started',
			'col_count'        => count($this->columns),
		);

		$this->kalog->write("Import started. Parameters: " . var_export($this->stat, true));

		return true;
	}


	/*
	*/
	protected function disableNotImportedProducts() {
		
		$this->db->query("UPDATE " . DB_PREFIX . "product p INNER JOIN 
			" . DB_PREFIX. "product_to_store pts ON p.product_id = pts.product_id
			SET p.status='0' 
			WHERE 
				p.product_id NOT IN (
					SELECT product_id FROM " . DB_PREFIX . "ka_product_import 
						WHERE token = '" . $this->session->data['token'] . "'
				)
		");
	}
	
	
	/*
		PARAMETERS:
			$ctrl - a controller object for output.

		This function is supposed to be called from an external object multiple times. But first you
		will need to call initImport() to define import parameters.

		Import status can be checked by requesting getImportStat() function and verifying $status
		parameter.
	*/
	public function processImport($ctrl = NULL) {

		// switch error output to our stream
		//
		$old_config_error_display = $this->config->get('config_error_display');
		$this->config->set('config_error_display', false);
		$this->process($ctrl);
		$this->config->set('config_error_display', $old_config_error_display);

		return;
	}

	/*
		function updates $this->stat array.
		
		Import status can be determined by 
			$this->stat['status']  - completed, in_progress, error, not_started
			$this->stat['message'] - last import fatal error
	*/
	protected function process($ctrl = NULL) {

		if ($this->stat['status'] == 'completed') {
			return;
		}
		
		$max_execution_time = @ini_get('max_execution_time');
		if ($max_execution_time > 5 && $max_execution_time < $this->sec_per_cycle) {
			$this->sec_per_cycle = $max_execution_time - 3;
		}

		$started_at = time();
		if (($handle = $this->fopen_utf8($this->params['file'], $this->params['charset'])) == FALSE) {
			$this->addImportMessage("Cannot open file: $this->params[file]", 'E');
			$this->stat['status']  = 'error';
			return;
		}

		$col_count = $this->stat['col_count'];
		$this->load->model('tool/image');

		if ($this->stat['offset']) {
			if ($this->fseek_utf8($handle, $this->stat['offset']) == -1) {
				$this->addImportMessage("Cannot offset at $this->stat[offset] in file: $file.", 'E');
				$this->stat['status']  = 'error';
				return;
			}
		} else {
			$tmp = fgetcsv($handle, 0, $this->params['delimiter'], $this->enclosure);
			$this->stat['lines_processed'] = 1;
			if (is_null($tmp) || count($tmp) != $col_count) {
				$this->addImportMessage("File header does not match the initial file header.", 'E');
				$this->stat['status']  = 'error';
				return;
			}
		}

		$status = 'error';
		while ($row = fgetcsv($handle, 0, $this->params['delimiter'], $this->enclosure)) {
		
			$this->stat['lines_processed']++;
			$row = $this->request->clean($row);

			if (!is_array($row)) {
				$this->addImportMessage('File reading error. File ended unexpectedly.');
				continue;
			}
			
			// compare number of read values against the number of columns in the header
			//
			$row_count = count($row);
			if ($row_count < $col_count) {
				if ($row_count == 1) {
					continue;
				}

				// extend the line with empty values. MS Excel may 'optimize' a CSV file and remove
				// trailing empty cells
				//
				$tail = array_fill($row_count, $col_count - $row_count, '');
				$row = array_merge($row, $tail);
				
			} elseif ($row_count > $col_count) {
				$row = array_slice($row, 0, $col_count);
			}
				

			// delete previous product records like 'specials', 'discounts' etc.
			//
			$delete_old_param = ($this->params['update_mode'] == 'replace');

			$product          = array();
			$data             = array();
			$is_new           = false;

			$is_first_product = true;		// first occurence of the product in the file
			$delete_old       = false;  	// 

			// fill in associated product fields
			//
			if (empty($this->params['matches']['fields'])) {
				$this->addImportMessage("Import script lost parameters. Aborting...");
				$status = 'error';
				break;
			}

			foreach ($this->params['matches']['fields'] as $fk => $fv) {
				if (!isset($fv['column']))
					continue;

				$data[$fv['field']] = trim($row[$fv['column']]);
				
				if (!empty($fv['copy'])) {
					$product[$fv['field']] = $data[$fv['field']];
				}
			}

			if (!empty($data['model'])) {
				$this->product_mark = '(model:' . $data['model'] . '): ';
			} else {
				$this->product_mark = '';
			}
			
			// get product id
			//
			$product_id = $this->getProductId($data);
			if (!empty($this->lastError)) {
				$this->addImportMessage($this->lastError . " Line " . $this->stat['lines_processed']);
				continue;
			}
			
			// here we have two separate ways
			// 1) delete product
			// 2) go through insert/update procedure
			//
			if (!empty($data['delete_product_flag'])) {
				if (!empty($product_id)) {
					$this->model_catalog_product->deleteProduct($product_id);
					$this->stat['products_deleted']++;
				}
				
			} else {

				if (empty($product_id)) {

					if (!empty($this->params['skip_new_products'])) {
						continue;
					}
										
					// insert a new product if required
					//
					if (empty($data['name'])) {
						$this->addImportMessage("Product name is not specified for a new product. Line is skipped: " . $this->stat['lines_processed']);
						continue;
					}

					if (!empty($this->params['tpl_product_id'])) {
						$last_product_id = $this->model_catalog_product->getLastProductId();
						$this->model_catalog_product->copyProduct($this->params['tpl_product_id']);
						$product_id	= $this->model_catalog_product->getLastProductId();
						if ($last_product_id == $product_id) {
							$product_id = 0;
						}
						
						if (!empty($product_id)) {
							if (!empty($data['product_id'])) {
								$this->db->query("UPDATE INTO " . DB_PREFIX . "product SET product_id = '" . $this->db->escape($data['product_id']) . "' WHERE product_id = '$product_id'");
							}
						}
					}
					
					if (empty($product_id)) {
						if (empty($data['product_id'])) {
							$this->db->query("INSERT INTO " . DB_PREFIX . 'product SET date_modified = NOW(), date_added = NOW()');
							$product_id = $this->db->getLastId();
						} else {
							$this->db->query("REPLACE INTO " . DB_PREFIX . "product SET date_modified = NOW(), date_added = NOW(), product_id = '" . $this->db->escape($data['product_id']) . "'");
							$product_id = $data['product_id'];
						}
					}

					if (empty($product_id)) {
						$this->addImportMessage("Insert operation failed.");
						continue;
					}
					$is_new = true;
					$this->stat['products_created']++;
				}

				// check if we already updated the product
				//
				$qry = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "ka_product_import
					WHERE product_id = '$product_id'
					AND token='" . $this->session->data['token'] . "';"
				);

				if (empty($qry->row)) {
					$rec = array(
						'product_id' => $product_id,
						'token'      => $this->session->data['token']
					);
					$this->kadb->queryInsert('ka_product_import', $rec);
				} else {
					$is_first_product = false;
				}
				
				// update product fields once
				//
				if ($is_first_product) {
					$this->kadb->queryUpdate('product', $product, "product_id='$product_id'");
					if (!$this->updateProduct($product_id, $data, $is_new)) {
						continue;
					}
					if (!$is_new) {
						$this->stat['products_updated']++;
					}
				}

				if ($delete_old_param && $is_first_product) {
					$delete_old = true;
				}
			
				$product['product_id'] = $product_id;

				$this->saveCategories($product_id, $data, $delete_old, $is_new);

				$this->saveAdditionalImages($product_id, $data, $delete_old, $is_new);

				$this->saveAttributes($row, $product, $delete_old);
				
				if (version_compare(VERSION, '1.5.5', '>=')) {
					$this->saveFilters($row, $product, $delete_old);
				}

				$this->saveOptions($row, $product, $delete_old);

				$this->saveDiscounts($row, $product, $delete_old);

				$this->saveSpecials($row, $product, $delete_old);

				$this->saveRewardPoints($row, $product, $delete_old);

				if (version_compare(VERSION, '1.5.6', '>=')) {
					$this->saveProductProfiles($row, $product, $delete_old);
				}
								
				$this->saveRelatedProducts($data, $product, $delete_old);
				
				$this->saveDownloads($data, $product, $delete_old);
			}
			
			if (time() - $started_at > $this->sec_per_cycle) {
				$status = 'in_progress';
				break;
			}
		}

		$this->cache->delete('product');

	    if (feof($handle)) {
	    
	    	fclose($handle);

    		$this->stat['status'] = 'completed';
    		$this->stat['offset'] = $this->stat['filesize'];
	    	$this->kalog->write("Import completed. Parameters: " . var_export($this->stat, true));
	    	
	    	// rename the import file if required
	    	//
	    	if ($this->params['location'] == 'server' && !empty($this->params['rename_file'])) {
		    	$path_parts = pathinfo($this->params['file']);
	    	
		    	$dest_file  = $path_parts['dirname'] . DIRECTORY_SEPARATOR . $path_parts['filename'] 
					. '.' . 'processed_at_' . date("Ymd-His") 
					. '.' . $path_parts['extension'];
				if (!rename($this->params['file'], $dest_file)) {
					$this->addImportMessage("rename operation failed. from " .$this->params['file'] . " to " . $dest_file);
				}
			}
			
			if (!empty($this->params['disable_not_imported_products'])) {
				$this->disableNotImportedProducts();
			}

	    } else if ($status == 'error') {
	    	fclose($handle);
			$this->stat['status'] = $status;
			
		} else {
			$this->stat['offset'] = ftell($handle);
			$this->stat['status'] = 'in_progress';
			fclose($handle);
		}

	    return;
	}

	
	public function getProfiles() {
		$qry = $this->db->query("SELECT import_profile_id, name FROM " . DB_PREFIX . "ka_import_profiles");

		$profiles = array();				
		if (!empty($qry->rows)) {
			foreach ($qry->rows as $row) {
				$profiles[$row['import_profile_id']] = $row['name'];
			}
		}
				
		return $profiles;
	}


	public function deleteProfile($profile_id) {

		$this->db->query("DELETE FROM " . DB_PREFIX. "ka_import_profiles WHERE import_profile_id = '" . $this->db->escape($profile_id) . "'");
			
		return true;
	}
	
		
	/*
		returns:
			array - on success
			false - on error
	*/
	public function getProfileParams($profile_id) {
	
		$qry = $this->db->query("SELECT * FROM " . DB_PREFIX . "ka_import_profiles WHERE import_profile_id = '" . $this->db->escape($profile_id) . "'");
		if (empty($qry->rows)) {
			return false;
		}

		if (!empty($qry->row['params'])) {
			$params = unserialize($qry->row['params']);
		} else {
			$params = array();
		}

		return $params;
	}
	
	
	/*
		returns:
			true  - on success
			false - on error
	*/
	public function setProfileParams($profile_id, $name, $params) {
	
		if (empty($profile_id)) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "ka_import_profiles
				SET 
					name = '" . $this->db->escape($name) . "'
			");
			$profile_id = $this->db->getLastId();
		}
		
		$this->db->query("UPDATE " . DB_PREFIX . "ka_import_profiles
			SET 
				name = '" . $this->db->escape($name) . "',
				params = '" . $this->db->escape(serialize($params)) . "'
			WHERE
				import_profile_id = '" . $this->db->escape($profile_id) . "'
		");

		return true;
	}
	

	public function getLastError() {
		return $this->lastError;
	}


	public function getColumns() {
		return $this->columns;
	}


	public function getImportMessages() {
		return $this->messages;
	}

	public function getImportStat() {		
	 	return $this->stat;
	}


	/*
		Extends array with custom fields from the product table only.
		
	*/	
	protected function addCustomProductFields(&$fields) {
		$default_fields = array('product_id', 'model', 'sku', 'upc', 'ean', 'jan', 'isbn', 'mpn',
			'location', 'quantity', 'stock_status_id', 'image', 'manufacturer_id', 'shipping',
			'price', 'points', 'tax_class_id', 'date_available', 'weight', 'weight_class_id',
			'length', 'width', 'height', 'length_class_id', 'subtract', 'minimum', 'sort_order',
			'status', 'date_added', 'date_modified', 'viewed'
		);
		
		if (!empty($fields)) {
			foreach ($fields as $field) {
				$default_fields[] = $field['field'];
			}
		}
	
		$qry = $this->db->query('SHOW FIELDS FROM ' . DB_PREFIX . 'product');
		if (empty($qry->rows)) {
			return false;
		}
		
		foreach ($qry->rows as $row) {
			if (in_array($row['Field'], $default_fields)) {
				continue;
			}
			
			$field = array(
				'field' => $row['Field'],
				'copy'  => true,
				'name'  => $row['Field'],
				'descr' => 'Custom field. Type: ' . $row['Type']
			);
			
			$fields[] = $field;
		}
		
		return true;
	}
	
	
	public function getFieldSets() {

		$fields = array(
			'model' => array(
				'field' => 'model',
				'required' => false,
				'copy'  => true,
				'name'  => 'Model',
				'descr' => 'A unique product code required by Opencart'
			),
			'sku' => array(
				'field' => 'sku',
				'copy'  => true,
				'name'  => 'SKU',
				'descr' => ''
			),
			'upc' => array(
				'field' => 'upc',
				'copy'  => true,
				'name'  => 'UPC',
				'descr' => 'Universal Product Code',
			),
		);
		
		$fields2 = array(			
			array(
				'field' => 'name',
				'name'  => 'Name',
				'descr' => 'Product name',
			),
			array(
				'field'    => 'description',
				'name'     => 'Description',
				'descr'    => 'Product description',
			),
			array(
				'field'    => 'category_id',
				'name'     => 'Category ID',
				'descr'    => "If this field is specified then the 'category name' field will be ignored",
			),
			array(
				'field'    => 'category',
				'name'     => 'Category Name',
				'descr'    => 'Full category path. Example: category' . $this->params['cat_separator'] . 'subcategory1' . $this->params['cat_separator'] . 'subcategory2',
			),
			array(
				'field' => 'location',
				'copy'  => true,
				'name'  => 'Location',
				'descr' => 'This field is not used in front-end but it can be defined for products'
			),
			array(
				'field' => 'quantity',
				'copy'  => true,
				'name'  => 'Quantity',
				'descr' => ''
			),			
			array(
				'field' => 'minimum',
				'copy'  => true,
				'name'  => 'Minimum Quantity',
				'descr' => ''
			),
			'subtract' => array(
				'field' => 'subtract',
				'copy'  => true,
				'name'  => 'Subtract Stock',
				'descr' => "1 - Yes, 0 - No."
			),
			'stock_status' => array(
				'field' => 'stock_status',
				'name'  => 'Out of Stock Status',
				'descr' => 'Name of the stock status. Only stock statuses registered in the store are processed.'
			),			 
			'shipping' => array(
				'field' => 'shipping',
				'copy'  => true,
				'name'  => 'Requires Shipping',
				'descr' => '1 - Yes, 0 - No.'
			),
			array(
				'field' => 'status',
				'name'  => 'Status',
				'descr' => "Status 'Enabled' can be defined by '1' or 'Y'. If the status column is not used then behavior depends on the extension settings."
			),			
			array(
				'field' => 'image',
				'name'  => 'Main Product Image',
				'descr' => "A relative path to the image file within 'image' directory or URL."
			),
			array(
				'field' => 'additional_image',
				'name'  => 'Additional Product Image',
				'descr' => "A relative path to the image file within 'image' directory or URL."
			),
			array(				
				'field' => 'manufacturer',
				'name'  => 'Manufacturer',
				'descr' => 'Manufacturer name'
			),
			array(
				'field' => 'price',
				'name'  => 'Price',
				'descr' => 'Regular product price in primary currency (' . $this->config->get('config_currency') . ')',
			),
			array(
				'field' => 'tax_class',
				'name'  => 'Tax class',
				'descr' => 'Tax class'
			),
			array(
				'field' => 'weight',
				'name'  => 'Weight',
				'descr' => 'Weight class units (declared in the store) can be used with the value. Example: 15.98lbs (no spaces).'
			),
			array(
				'field' => 'length',
				'name'  => 'Length',
				'descr' => 'Length class units (declared in the store) can be used with the value. Example: 1.70m (no spaces)'
			),
			array(
				'field' => 'width',
				'name'  => 'Width',
				'descr' => 'Length class units (declared in the store) can be used with the value. Example: 1.70m (no spaces)'
			),
			array(
				'field' => 'height',
				'name'  => 'Height',
				'descr' => 'Length class units (declared in the store) can be used with the value. Example: 1.70m (no spaces)'
			),
			array(
				'field' => 'meta_keyword',
				'name'  => 'Meta tag keywords',
				'descr' => ''
			),
			array(
				'field' => 'meta_description',
				'name'  => 'Meta tag description',
				'descr' => ''
			),
			'points' => array(
				'field' => 'points',
				'copy'  => true,
				'name'  => 'Points Required',
				'descr' => 'Number of reward points required to make purchase'
			),
			'sort_order' => array(
				'field' => 'sort_order',
				'copy'  => true,
				'name'  => 'Sort Order',
				'descr' => ''
			),
			array(
				'field' => 'seo_keyword',
				'name'  => 'SEO Keyword',
				'descr' => 'SEO friendly URL for the product. Make sure that it is unique in the store.'
			),
			array(
				'field' => 'product_tags',
				'name'  => 'Product Tags',
				'descr' => 'List of product tags separated by comma'
			),
			array(
				'field' => 'date_available',
				'name'  => 'Date Available',
				'descr' => 'Format: YYYY-MM-DD, Example: 2012-03-25'
			),
			array(
				'field' => 'related_product',
				'name'  => 'Related Product',
				'descr' => 'model identifier of the related product',
			),
			'downloads' => array(
				'field' => 'downloads',
				'name'  => 'Downloads',
				'descr' => 'Downloadable file(s)',
			),
			'layout'    => array(
				'field' => 'layout',
				'name'  => 'Laytout',
				'descr' => 'Product layout'
			),
		);
		
		$enable_delete_flag = true;
		if ($enable_delete_flag) {
			$fields2[] = array(
				'field' => 'delete_product_flag',
				'name'  => '"Delete Product" Flag',
				'descr' => 'Any non-empty value will be treated as positive confirmation, be careful',
			);
		}
		
		if (version_compare(VERSION, '1.5.4', '>=')) {
			$fields_ver154 = array(
				'ean' => array(
					'field' => 'ean',
					'copy'  => true,
					'name'  => 'EAN',
					'descr' => 'European Article Number',
				),
				'jan' => array(
					'field' => 'jan',
					'copy'  => true,
					'name'  => 'JAN',
					'descr' => 'Japanese Article Number',
				),
				'isbn' => array(
					'field' => 'isbn',
					'copy'  => true,
					'name'  => 'ISBN',
					'descr' => 'International Standard Book Number',
				),
				'mpn' => array(
					'field' => 'mpn',
					'copy'  => true,
					'name'  => 'MPN',
					'descr' => 'Manufacturer Part Number',
				),
			);
			
			$fields = array_merge($fields, $fields_ver154);
		}

		$fields = array_merge($fields, $fields2);

		if ($this->config->get('ka_pi_enable_product_id')) {
			$product_id_field = array(
				'field' => 'product_id',
				'name'  => 'product_id',
				'descr' => 'You import this value at your own risk.'
			);
			array_unshift($fields, $product_id_field);
		}

		$this->addCustomProductFields($fields);
		
		foreach ($this->key_fields as $kfk => $kfv) {
			$fields[$kfv]['required'] = true;
		}
		
		$specials = array(
			array(
				'field' => 'customer_group',
				'name'  => 'Customer Group',
				'descr' => ''
			),
			array(
				'field' => 'priority',
				'name'  => 'Prioirity',
				'descr' => ''
			),
			array(
				'field'    => 'price',
				'name'     => 'Price',
				'descr'    => ''
			),
			array(
				'field' => 'date_start',
				'name'  => 'Date Start',
				'descr' => 'Format: YYYY-MM-DD, Example: 2012-03-25'
			),
			array(
				'field' => 'date_end',
				'name'  => 'Date End',
				'descr' => 'Format: YYYY-MM-DD, Example: 2012-03-25'
			),
		);			

		$discounts = array(
			array(
				'field' => 'customer_group',
				'name'  => 'Customer Group',
				'descr' => ''
			),
			'quantity' => array(
				'field' => 'quantity',
				'name'  => 'Quantity',
				'descr' => ''
			),
			'priority' => array(
				'field' => 'priority',
				'name'  => 'Prioirity',
				'descr' => ''
			),
			'price' => array(
				'field' => 'price',
				'name'  => 'Price',
				'descr' => ''
			),
			'date_start' => array(
				'field' => 'date_start',
				'name'  => 'Date Start',
				'descr' => 'Format: YYYY-MM-DD, Example: 2012-03-25'
			),
			'date_end' => array(
				'field' => 'date_end',
				'name'  => 'Date End',
				'descr' => 'Format: YYYY-MM-DD, Example: 2012-03-25'
			),
		);			

		$reward_points = array(
			'customer_group' => array(
				'field' => 'customer_group',
				'name'  => 'Customer Group',
				'descr' => '',
			),
			'points' => array(
				'field'    => 'points',
				'name'     => 'Reward Points',
				'descr'    => '',
			),
		);

		$ext_options = array(
			'name' => array(
				'field' => 'name',
				'name'  => 'Option Name',
				'descr' => 'required'
			),
			'type' => array(
				'field' => 'type',
				'name'  => 'Option Type',
				'descr' => 'required'
			),
			'value' => array(
				'field' => 'value',
				'name'  => 'Option Value',
				'descr' => 'required'
			),
			'required' => array(
				'field' => 'required',
				'name'  => 'Option Required',
				'descr' => ''
			),
			'image' => array(
				'field' => 'image',
				'name'  => 'Option Image',
				'descr' => ''
			),
			'sort_order' => array(
				'field' => 'sort_order',
				'name'  => 'Value Sort Order',
				'descr' => 'Sort order for option values'
			),
			'group_sort_order' => array(
				'field' => 'group_sort_order',
				'name'  => 'Group Sort Order',
				'descr' => 'Sort order for option groups. Empty cells are skipped.'
			),
			'quantity' => array(
				'field' => 'quantity',
				'name'  => 'Option Quantity',
				'descr' => ''
			),
			'subtract' => array(
				'field' => 'subtract',
				'name'  => 'Option Subtract',
				'descr' => ''
			),
			'price' => array(
				'field' => 'price',
				'name'  => 'Option Price',
				'descr' => ''
			),
			'points' => array(
				'field' => 'points',
				'name'  => 'Option Points',
				'descr' => ''
			),
			'weight' => array(
				'field' => 'weight',
				'name'  => 'Option Weight',
				'descr' => ''
			),
		);

		$sets = array(
			'fields'        => $fields,
			'discounts'     => $discounts,
			'specials'      => $specials,
			'reward_points' => $reward_points,
			'ext_options'   => $ext_options,
		);

		$this->load->model('catalog/attribute');
		$sets['attributes'] = $this->model_catalog_attribute->getAttributes();

		$this->load->model('catalog/option');
		$sets['options'] = $this->model_catalog_option->getOptions();
				
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$this->load->model('catalog/filter');
			$sets['filter_groups']   = $this->model_catalog_filter->getFilterGroups();
		}
		
		if (version_compare(VERSION, '1.5.6', '>=')) {
			$sets['product_profiles'] = array(
				'name' => array(
					'field'    => 'name',
					'name'     => 'Profile name',
					'descr'    => '',
				),
				'customer_group' => array(
					'field' => 'customer_group',
					'name'  => 'Customer Group',
					'descr' => '',
				),
			);
		}		
				
		return $sets;
	}

	public function getCharsets() {
		$arr = array(
			'ISO-8859-1'   => 'ISO-8859-1 (Western Europe)',
			'ISO-8859-5'   => 'ISO-8859-5 (Cyrillc, DOS)',
			'UTF-16'       => 'UNICODE (MS Excel text format)',
			'KOI-8R'       => 'KOI-8R (Cyrillic, Unix)',
			'UTF-7'        => 'UTF-7',
			'UTF-8'        => 'UTF-8',
			'windows-1250' => 'windows-1250 (Central European languages)',
			'windows-1251' => 'windows-1251 (Cyrillc)',
			'windows-1252' => 'windows-1252 (Western languages)',
			'windows-1253' => 'windows-1253 (Greek)',
			'windows-1254' => 'windows-1254 (Turkish)',
			'windows-1255' => 'windows-1255 (Hebrew)',
			'windows-1256' => 'windows-1256 (Arabic)',
			'windows-1257' => 'windows-1257 (Baltic languages)',
			'windows-1258' => 'windows-1258 (Vietnamese)',
			'CP932'        => 'CP932 (Japanese)',
		);

		return $arr;
	}

	/*
		$matches - sets array
		$matches - hash array of column names			
	*/
	public function copyMatches(&$sets, $matches, $columns) {

		// remove empty columns except the first one meaning 'not selected'
		//
		foreach ($columns as $ck => $cv) {
			if ($ck == 0 || !empty($cv)) {
				$tmp[$cv] = $ck;
			}
		}
		$columns = $tmp;

		foreach ($sets as $sk => $sv) {
		
			foreach ($sv as $f_idx => $f_data) {
				if ($sk == 'filter_groups') {
					$f_key = $f_data['filter_group_id'];
					
				} elseif ($sk == 'attributes') {
					$f_key = $f_data['attribute_id'];
					
				} elseif ($sk == 'options') {
					$f_key = $f_data['option_id'];
					
				} else {
					$f_key = (isset($f_data['field']) ? $f_data['field']:$f_idx);
				}
				
				if (isset($matches[$sk][$f_key])) {
					if (isset($columns[$matches[$sk][$f_key]])) {
						$sets[$sk][$f_idx]['column'] = $columns[$matches[$sk][$f_key]];
					}
				}
			}
		}
		
		return true;
	}

			
	public function getDelimiters() {
		return $this->delimiters;
	}

	
	/*
		
	
	*/
	public function requestSchedulerOperations() {
	
		if (!$this->isInstalled()) {
			return false;
		}
	
		$ret = array(
			'import' => 'Import'
		);
	
		return $ret;
	}
	
	
	public function requestSchedulerOperationParams($operation) {
	
		$ret = array(
			'profile' => array(
				'title' => 'Import Profile',
				'type'  => 'select',
				'required' => true,
			),
		);

		$ret['profile']['options'] = $this->getProfiles();

		return $ret;
	}

			
	protected function initSchedulerImport($profile_id) {
	
		/* profile parameters:
		
				'charset'             => 'UTF-8'
				'cat_separator'       => '///',
				'delimiter'           => 's',
				'store_id'            => array(0),
				'sort_by'             => 'name',
				'image_path'          => 'path',
				'category_ids'        => array(),
				...
		*/
		$params = $this->getProfileParams($profile_id);
		
		if (empty($params)) {
			$this->lastError = "Profile not found";
			return false;
		}
		
		if (!$this->initImport($params)) {
			return false;
		}

		return true;
	}


	/*
		return a code:			
			finished      - operation is complete
			not_finished  - still working (additional calls needed)
	*/
	public function runSchedulerOperation($operation, $params, &$stat) {

		$ret = 'finished';

		if (!$this->isInstalled()) {
			return $ret;
		}
		
		if (version_compare(VERSION, '1.5.5', '>=')) {
			$this->language->load('tool/ka_import');
		} else {
			$this->load->language('tool/ka_import');
		}

		if ($operation != 'import') {
			$stats['error'] = "Unsupported operation code";
			return $ret;
		}

		if (empty($params['profile'])) {
			$stats['error'] = "Unsupported parameters were passed to the module.";
			return $ret;
		}

		if (empty($this->params) || empty($stat)) {
			if (!$this->initSchedulerImport($params['profile'])) {
				$this->report('initSchedulerImport request was failed');
				return $ret;
			}
		}

		$this->processImport();
		
		$stat['Lines Processed']    = $this->stat['lines_processed'];
		$stat['Products Created']   = $this->stat['products_created'];		
		$stat['Products Updated']   = $this->stat['products_updated'];
		$stat['Products Deleted']   = $this->stat['products_deleted'];
		$stat['Categories Created'] = $this->stat['categories_created'];
		
		$stat['Time Passed']        = $this->timeFormat(time() - $this->stat['started_at']);
		$stat['File Size']          = $this->convertToMegabyte($this->stat['filesize']);
		
		if ($this->stat['status'] == 'in_progress') {
			$stat['Completion At'] = sprintf("%.2f%%", $this->stat['offset'] / ($this->stat['filesize']/100));
			$ret = 'not_finished';
			
		} elseif ($this->stat['status'] == 'completed') {
			$stat['Completion At'] = sprintf("%.2f%%", 100);
			$ret = 'finished';
			$this->params = null;
			$this->stat   = null;
		}

		return $ret;
	}
}

?>