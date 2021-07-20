<?php
/*
  Project: Ka Extensions
  Author : karapuz <support@ka-station.com>

  Version: 2.0 ($Revision: 43 $)

*/
class KaDb {
	private $db;
	
	public function __construct(&$db) {
		$this->db = &$db;
	}
		
 	public function query($sql) {
 		$res = $this->db->query($sql);

		if (empty($res->rows)) {
			return $res;
		}
 		
		return $res->rows;
 	}
	
	public function escape($value) {
		return $this->db->escape($value);
	}
	
 	public function countAffected() {
		return $this->db->countAffected();
 	}

 	public function getLastId() {
		return $this->db->getLastId();
 	}	


	public function queryInsert($tbl, $arr, $is_replace = false) {

	    if (empty($tbl) || empty($arr) || !is_array($arr))
    	    return false;

	    $query = $is_replace ? 'REPLACE' : 'INSERT';

    	$arr_keys = array_keys($arr);
	    foreach ($arr_keys as $k => $v) {
    	    if (!preg_match('/^`.*`$/Si', $v, $out)) {
        	    $arr_keys[$k] = "`" . $v . "`";
	        }
	    }

	    $arr_values = array_values($arr);
    	foreach ($arr_values as $k => $v) {
	    	$arr_values[$k] = "'" . $this->db->escape($v) . "'";
	    }

    	$tbl = DB_PREFIX . $tbl;
	    $query .= ' INTO `' . $tbl . '` (' . implode(', ', $arr_keys) . ') VALUES (' . implode(', ', $arr_values) . ')';

    	if (!$this->db->query($query)) {
	    	return false;
		}

		return $this->db->getLastId();
	}

	
	public function queryUpdate($tbl, $arr, $where = '') {
	    if (empty($tbl) || empty($arr) || !is_array($arr)) {
    	    return false;
	    }

		$tbl = DB_PREFIX . $tbl;

    	$r = array();

	    foreach ($arr as $k => $v) {
	    	if (!(($k{0} == '`') && ($k{strlen($k) - 1} == '`'))) {
    	        $k = "`$k`";
        	}

	        $v = "'" . $this->db->escape($v) . "'";
    		$r[] = $k . "=" . $v;
    	}

	    $query = 'UPDATE `' . $tbl . '` SET ' . implode(', ', $r) . ($where ? ' WHERE ' . $where : '');

    	return $this->db->query($query);
	}


	public function queryFirst($qry) {
		$res = $this->db->query($qry);
		return $res->row;
	}
}
?>