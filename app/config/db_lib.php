<?php
	//include db connection and variable $con as connection name
	//global $con as db connection
	
	function db_isAvail($tbl,$field,$val=null)
	{
		global $con;
		if ($val==null)
		{
			$dbsql = "SELECT * FROM $tbl  WHERE $field";
		}
		else
		{
			$dbsql = "SELECT * FROM $tbl  WHERE $field = $val";
		}
		$dbquery = mysqli_query($con,$dbsql);
		if (isset($dbquery))
		{
			if(mysqli_num_rows($dbquery)>0)
			{
				return true;
			}
			else
			{
				return false;
			}		
		}
		else
		{
			return false;
		}
		//return $dbsql;
	}
	
	function db_autocommit($val)
	{
		global $con;
		mysqli_autocommit($con,$val);
	}
	
	function db_commit()
	{
		global $con;
		return mysqli_commit($con);
	}
	
	function db_rollback()
	{
		global $con;
		return mysqli_rollback($con);
	}
	
	function db_begin_transaction()
	{
		global $con;
		mysqli_begin_transaction($con);
	}
	
	function db_check($sql)
	{
		$result = db_query($sql);
		$count = mysqli_num_rows($result);
		if ($count>0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	function db_lastRecord($tbl)
	{
		$dbsql = db_sqlSelect($tbl,"*",db_priKey($tbl)." = ".db_lastInsert($tbl));
		$dbquery = db_query($dbsql);
		return $dbquery;
	}
	
	function db_id()
	{
		return db_lastInsert();
	}

	function db_id_core()
	{
		return db_lastInsert_core();
	}
	
	function db_array($query)
	{
		$result = db_query($query);
		$result = mysqli_fetch_array($result);
		return $result;
	}
	
	function db_assoc($query)
	{
		$result = db_query($query);
		if ($result && is_object($result)) {
			return mysqli_fetch_assoc($result);
		}
		return null;
	}

	function db_assoc_core($query)
	{
		$result = db_query_core($query);
		if ($result && is_object($result)) {
			return mysqli_fetch_assoc($result);
		}
		return null;
	}
	
	function db_lastInsert($tbl=null,$field="egwgwegwegqegqgq",$where="")
	{
		global $con;
		global $database;
		if($tbl==null)
		{
			return mysqli_insert_id($con);
		}
		else
		{
			if ($where != "")
			{
				$where = "WHERE ".$where;
			}
			$pi_key = db_priKey($tbl);
			if ($field="egwgwegwegqegqgq")
			{
				$field = $pi_key;
			}
			$dbsql = "SELECT $field as value FROM $tbl WHERE $pi_key=(SELECT MAX($pi_key) FROM $tbl $where)";
			$dbquery = mysqli_query($con,$dbsql);
			while ($row=mysqli_fetch_array($dbquery))
			{
				$value=$row['value'];
			} 
			return $value;
		}
	}

	function db_lastInsert_core($tbl=null,$field="egwgwegwegqegqgq",$where="")
	{
		global $con2;
		global $database;
		if($tbl==null)
		{
			return mysqli_insert_id($con2);
		}
		else
		{
			if ($where != "")
			{
				$where = "WHERE ".$where;
			}
			$pi_key = db_priKey($tbl);
			if ($field="egwgwegwegqegqgq")
			{
				$field = $pi_key;
			}
			$dbsql = "SELECT $field as value FROM $tbl WHERE $pi_key=(SELECT MAX($pi_key) FROM $tbl $where)";
			$dbquery = mysqli_query($con2,$dbsql);
			while ($row=mysqli_fetch_array($dbquery))
			{
				$value=$row['value'];
			} 
			return $value;
		}
	}
	
	function db_get($data) 
	{
		//echo $data."<br>";
		return  db_fieldToVar($data);
	}

	function db_get_core($data) 
	{
		return  db_fieldToVar_core($data);
	}
	
	function db_escape_string($string)
	{
		global $con;
		return mysqli_real_escape_string($con,$string);
	}
	
	function db_fieldToVar($result,$reset='not')
	{
		$result_txt = $result;
		if (is_string($result))
		{
			$sql = $result;
			$result = db_query($result);
		}
		if(!$result)
		{
			echo "<b>query error</b><br>".$result_txt;
		}
		while ($row=mysqli_fetch_array($result))
		{
			while ($meta = mysqli_fetch_field($result))
			{
				$key = $meta->name;
				global ${$key};
				if ($reset == 'not')
				{
					${$key} = $row[$key];
					$lastval = $row[$key];
				}
				else
				{
					
					${$key} = "";
					$lastval = "";
				}
			}
		} 
		if (mysqli_num_fields($result)==1 && mysqli_num_rows($result)==1)
		{
			return $lastval;
		}
		if (!$result)
		{
			echo "<b>query result error</b> <br>";
			echo $sql;
		}
	}

	function db_fieldToVar_core($result,$reset='not')
	{
		$result_txt = $result;
		if (is_string($result))
		{
			$sql = $result;
			$result = db_query_core($result);
		}
		if(!$result)
		{
			echo "<b>query error</b><br>".$result_txt;
		}
		while ($row=mysqli_fetch_array($result))
		{
			while ($meta = mysqli_fetch_field($result))
			{
				$key = $meta->name;
				global ${$key};
				if ($reset == 'not')
				{
					${$key} = $row[$key];
					$lastval = $row[$key];
				}
				else
				{
					
					${$key} = "";
					$lastval = "";
				}
			}
		} 
		if (mysqli_num_fields($result)==1 && mysqli_num_rows($result)==1)
		{
			return $lastval;
		}
		if (!$result)
		{
			echo "<b>query result error</b> <br>";
			echo $sql;
		}
	}

	function db_VarReset($result)
	{
		
		/* global ${'tarif'};
		${'tarif'}=0; */
		while ($raw=mysqli_fetch_array($result))
		{
			
			while ($meta = mysqli_fetch_field($result))
			{
				$key = $meta->name;
				global ${$key};
				${$key} = "fg";
				$lastval = "fg";
				
			}
		}
		if (mysqli_num_fields($result)==1 && mysqli_num_rows($result)==1)
		{
			return $lastval;
		}
	}
	
	function db_log($tbl,$type,$ket,$id_sesi,$data1,$data2=null)
	{ 
		if($data2!=null)
		{
			$field=", data2";
			$data2=", '".$data2."'";
		}
		else
		{
			$field="";
			$data2="";
		}
		//$dbsql="INSERT INTO tbl_log(table_name,type,description,id_sesi,data1$field) VALUES ('$tbl','$type','$ket','$id_sesi','$data1'$data2)";
		//db_query($dbsql);
		//return "<br>".$dbsql."</br>";
	}
	
	function db_fieldToString($tbl,$exclude="",$pri_key=true,$tbl_name=false)
	{
		global $con;
		global $database;
		if ($tbl_name==true)
		{
			$tbl_name = $tbl.".";
		}
		else
		{
			$tbl_name = "";
		}
		if ($pri_key == false)
		{
			$exclude .= ",".db_priKey($tbl);
		}
		$dbquery = db_query("SELECT
								@rownum := @rownum + 1 AS NO,
								COLUMN_NAME 
							FROM
								information_schema.
								COLUMNS CROSS JOIN ( SELECT @rownum := - 1 ) r 
							WHERE
								table_schema = '$database' 
								AND table_name = '$tbl'");
		$no=0;
		$value=Array();
		while ($sate=mysqli_fetch_array($dbquery))
		{
			$tambah = true;
			if ($exclude != "")
			{
				$exclude2 = $exclude;
				$exclude2 = str_replace(" ","",$exclude2);
				$exclude2 = explode(",",$exclude2); 
				foreach($exclude2 as $replace)
				{
					if($sate['COLUMN_NAME'] == $replace)
					{
						$tambah=false;
						break;
					}
				}
			} 
			if ($tambah == true)
			{
				$no++;
				$value[] = $tbl_name.$sate['COLUMN_NAME'];
			}
		}
		//var_dump($value);
		$string = implode(", ",$value);
		return $string; 
	}
	 
	function v($var,$default="")
	{
		global ${$var};
		if (!isset(${$var}))
			{
				$value = $default;
			}
			else
			{
				$value = ${$var};
			}
		return $value;
	}
	
	function v_set($var,$val="")
	{
		global ${$var};
		${$var} = "";
	}
	
	function db_cond($val,$field,$orand = "OR",$oper="=",$ext="")
	{
		$sql ="";
		if($ext!="" && strpos($ext,"(val)") !== false)
		{
			$ext="xxx".$ext."xxx";
			$ext = explode("(val)",$ext);
			for($i=0;$i<count($ext);$i++)
			{
				$ext[$i] = str_replace("xxx","",$ext[$i]);
			}
		}
		else
		{
			unset($ext);
			$ext = ["",""];
		}
		$sql .= "(";
					$q=array();
					foreach($val as $n)
					{
						array_push($q,$n);
					}
					for ($i=0;$i<count($q);$i++) 
					{
						if ($i == 0)
						{
							$sql .= " $field $oper '$ext[0]$q[$i]$ext[1]'";
						}
						else
						{
							$sql .= " $orand $field $oper '$ext[0]$q[$i]$ext[1]'";
						}	
					}
					$sql .= " )";
		return $sql;
	}
	
	function db_select($tbl,$field="*",$where="",$lim1="",$lim2="")
	{
		global $con;
		if ($lim1!="")
		{
			$lim1="LIMIT ".$lim1;
		}
		if ($lim2!="")
		{
			$lim2=", ".$lim2;
		}
		$limit = $lim1.$lim2;
		if ($where != "")
		{
			$where = "WHERE ".$where;
		}
		$dbsql = "SELECT $field FROM $tbl $where $limit";
		$dbquery = mysqli_query($con,$dbsql);
		return $dbquery;
	}
	
	function db_insert($tbl,$field="",$val="")
	{
		if ($field!="")
		{
			$field="(".$field.")";
		}
		global $con;
		$dbsql = "INSERT INTO $tbl $field VALUES ($val)";
		$dbquery = mysqli_query($con,$dbsql);
		return $dbsql;
	}
	
	function db_update($tbl,$valset,$where="")
	{
		if ($where !="")
		{
			$where = "WHERE ".$where;
		}
		global $con;
		$dbsql = "UPDATE $tbl SET $valset $where";
		$dbquery = mysqli_query($con,$dbsql);
		return $dbsql;
	}
	
	
	function db_query($sql)
	{
		global $con;
		if (!$con) {
			return false;
		}
		$dbquery = @mysqli_query($con, $sql);
		if ($dbquery) {
			return $dbquery;
		} else {
			error_log("Database query error: " . mysqli_error($con) . " in: " . substr($sql, 0, 200));
			return false;
		}
	}
	
	/* function db_query($sql)
	{
		global $con;
		$dbquery = mysqli_query($con,$sql);
		if($dbquery)
		{
			return $dbquery;
			db_close($con);
		}
		else
		{
			echo "<br><b>query failed</b><br>$sql";
		}
	} */

	function db_query_core($sql)
	{
		global $con2;
		$dbquery = mysqli_query($con2,$sql);
		if($dbquery)
		{
			return $dbquery;
			db_close($con2);
		}
		else
		{
			echo "<br><b>query failed</b><br>$sql";
		}
	}

	function db_query_abs($sql)
	{
		global $con;
		return db_query($sql);
	}

	/**
	 * Eksekusi kueri terparameter (Prepared Statement) secara aman
	 */
	function db_prepared_query($sql, $params = [], $types = "")
	{
		global $con;
		if (!$con) return false;

		$stmt = mysqli_prepare($con, $sql);
		if (!$stmt) {
			error_log("Prepared statement failed: " . mysqli_error($con) . " SQL: " . $sql);
			return false;
		}

		if (!empty($params)) {
			if (empty($types)) {
				$types = "";
				foreach ($params as $p) {
					if (is_int($p)) $types .= "i";
					elseif (is_float($p)) $types .= "d";
					elseif (is_null($p)) $types .= "s";
					else $types .= "s";
				}
			}
			mysqli_stmt_bind_param($stmt, $types, ...$params);
		}

		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		return $result !== false ? $result : $stmt;
	}

	/**
	 * Mengambil satu baris associative array dari prepared statement
	 */
	function db_prepared_assoc($sql, $params = [], $types = "")
	{
		$res = db_prepared_query($sql, $params, $types);
		if ($res instanceof mysqli_result) {
			return mysqli_fetch_assoc($res);
		}
		return null;
	}

	/**
	 * Mengambil seluruh baris data array dari prepared statement
	 */
	function db_prepared_all($sql, $params = [], $types = "")
	{
		$res = db_prepared_query($sql, $params, $types);
		$rows = [];
		if ($res instanceof mysqli_result) {
			while ($r = mysqli_fetch_assoc($res)) {
				$rows[] = $r;
			}
		}
		return $rows;
	}

	/**
	 * Soft Delete record (mengubah is_deleted = 1 dan deleted_at = NOW())
	 */
	function db_soft_delete($table, $where_condition, $modul = 'general', $id_entitas = null)
	{
		global $con;
		if (!$con || empty($table) || empty($where_condition)) return false;

		$table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
		$sql = "UPDATE `$table` SET `is_deleted` = 1, `deleted_at` = NOW() WHERE $where_condition";
		$res = db_query($sql);

		if ($res) {
			audit_log('SOFT_DELETE', $modul, $id_entitas, "Soft delete pada tabel $table ($where_condition)");
		}
		return $res;
	}

	
	function db_priKey($tbl,$tbl_name=false)
	{
		global $con;
		global $database;
		if ($tbl_name==true)
		{
			$tbl_name = $tbl.".";
		}
		else
		{
			$tbl_name = "";
		}
		$pi_key = db_query("SELECT COLUMN_NAME
							FROM INFORMATION_SCHEMA.COLUMNS
							WHERE TABLE_SCHEMA = '$database'
							   AND TABLE_NAME = '$tbl'
							   AND COLUMN_KEY = 'PRI'");
		while ($row=mysqli_fetch_array($pi_key))
		{
			$key=$tbl_name.$row['COLUMN_NAME']; 
		} 
		return $key;
	}
	
	function db_sum($tbl,$field,$where="")
	{
		global $con;
		if ($where !="")
		{
			$where = "WHERE ".$where;
		}
		$dbsql = "SELECT SUM($field) as upil FROM $tbl $where";
		$dbquery = db_query($dbsql);
		$value = db_fieldToVar($dbquery);
		return $value; 
		//return $dbsql; 
	} 
	
	function db_delete($tbl,$where="(empty condition)")
	{
		if ($where !="(empty condition)")
		{
			$where = "WHERE ".$where;
		}
		global $con;
		$dbsql = "DELETE FROM $tbl $where";
		$dbquery = mysqli_query($con,$dbsql);
		return $dbsql;
	}
	
	function db_deleteAll($tbl)
	{
		global $con;
		$dbsql = "DELETE FROM $tbl";
		$dbquery = mysqli_query($con,$sql);
		return $dbquery;
	}
	
	function db_sqlSelect($tbl,$field="*",$where="",$lim1="",$lim2="")
	{
		global $con;
		if ($lim1!="")
		{
			$lim1="LIMIT ".$lim1;
		}
		if ($lim2!="")
		{
			$lim2=", ".$lim2;
		}
		$limit = $lim1.$lim2;
		if ($where != "")
		{
			$where = "WHERE ".$where;
		}
		$dbsql = "SELECT $field FROM $tbl $where $limit";
		return $dbsql;
	}
	
	function db_sqlInsert($tbl,$field="",$val="")
	{
		if ($field!="")
		{
			$field="(".$field.")";
		}
		if ($val!="")
		{
			$val="VALUES (".$val.")";
		}
		global $con;
		$dbsql = "INSERT INTO $tbl $field $val";
		$dbquery = mysqli_query($con,$dbsql);
		return $dbsql;
	}
	
	function db_sqlReset()
	{
		global $dbsqltemp;
		$dbsqltemp="";
		return $dbsqltemp;
	}
	
	function db_sqlAdd($sql)
	{
		global $dbsqltemp;
		if ( !isset($dbsqltemp ))
		{
			$dbsqltemp = "";
		}
		$dbsqltemp = $dbsqltemp." ".$sql;
		return $dbsqltemp;
	}
	
	function db_sqlAddJoin($tbl,$condition,$type="INNER")
	{
		global $dbsqltemp;
		$dbsqltemp = $dbsqltemp." ".$type." JOIN $tbl ON $condition ";
		return $dbsqltemp;
	}
	
	function db_sqlAddCon($condition,$type="WHERE") 
	{
		global $dbsqltemp;
		$dbsqltemp = $dbsqltemp." ".$type." ".$condition;
		return $dbsqltemp;
	}
	
	function db_sqlAddOrder($sql)
	{
		global $dbsqltemp;
		$dbsqltemp = $dbsqltemp." ORDER BY ".$sql;
		return $dbsqltemp;
	}

	function db_sqlAddLimit($lim1, $lim2="")
	{
		global $con;
		global $dbsqltemp;
		if ($lim1!="")
		{
			$lim1="LIMIT ".$lim1;
		}
		if ($lim2!="")
		{
			$lim2=", ".$lim2;
		}
		$limit = $lim1.$lim2;
		$dbsqltemp = $dbsqltemp." ".$limit;
		return $dbsqltemp;
	}
	
	function db_sqlGet()
	{
		global $dbsqltemp;
		if ( !isset($dbsqltemp ))
		{
			$dbsqltemp = "";
		}
		return $dbsqltemp;
	}
	
	function db_sqlQuery($sql='')
	{
		global $dbsqltemp;
		global $dbsqlresulttemp;
		if ($sql!='')
		{
			$dbsqltemp = $sql;
		}
		$dbsqlresulttemp = db_query($dbsqltemp);
		return $dbsqlresulttemp;
	}
	
	function db_sqlResult()
	{
		global $dbsqlresulttemp;
		return $dbsqlresulttemp;
	}
	
	function db_sqlResultFree()
	{ 
		global $dbsqlresulttemp;
		$dbsqlresulttemp = "";
	}
	
	function db_sqlToJson($sql,$id_json_awal="01809381028309183")
	{
		$result = db_query($sql);
		$count = mysqli_num_rows($result);
		$array = array();
		if ($count>1 || $id_json_awal==false)
		{
			while($aa=mysqli_fetch_array($result))
			{
				$result2 = db_query($sql);
				//$id_reg=$aa['id_registrasi'];
				if($id_json_awal=='01809381028309183')
				{
					$field=mysqli_fetch_field($result2);
					$id_json=$aa[$field->name];
				}
				else
				{
					$id_json=$aa[$id_json_awal];
				}
				while($field=mysqli_fetch_field($result2))
				{
					$array[$id_json][$field->name] = $aa[$field->name];
				}
			} 
		}
		else
		{
			while($aa=mysqli_fetch_array($result))
			{
				$result2 = db_query($sql);
				while($field=mysqli_fetch_field($result2))
				{
					$array[$field->name] = $aa[$field->name];
				}
			} 
		}
		return json_encode($array);
	}
	
	function db_resultToArray($arg,$ordered=true)
	{
		if (is_string($arg))
		{
			$result = db_query($arg);
			if (!$result)
			{
				echo"<br><b>query failed</b><br>".$arg;
			}
		}
		else
		{
			$result = $arg;
		}
		$array = array();
		$field_count = mysqli_num_fields($result);
		if ($field_count ==1)
		{
			$field=mysqli_fetch_field($result);
			while($aws=mysqli_fetch_array($result))
			{
				$array[] = $aws[$field->name];
			}
		}
		else 
		{
			if (mysqli_num_rows($result)==1)
			{
				while($aws=mysqli_fetch_array($result))
				{
					if (is_string($arg))
					{
						$result2 = db_query($arg);
					}
					else
					{
						$result2 = $arg;
					};
					if ($ordered==true)
					{
						while($field=mysqli_fetch_field($result2))
						{
							$array[] = $aws[$field->name];
						}
					}
					else
					{
						while($field=mysqli_fetch_field($result2))
						{
							$array[$field->name] = $aws[$field->name];
						}
					}
				}
			}
			else
			{
				$no = 0;
				while($aws=mysqli_fetch_array($result))
				{
					if (is_string($arg))
					{
						$result2 = db_query($arg);
					}
					else
					{
						$result2 = $arg;
					};
					if ($ordered==true)
					{
						while($field=mysqli_fetch_field($result2))
						{
							$array[$no][$field->name] = $aws[$field->name];
						}
						$no++;
					}
					else
					{
						$first_field = mysqli_fetch_field($result2);
						$id_field = $first_field->name;
						while($field=mysqli_fetch_field($result2))
						{
							$array[$aws[$id_field]][$field->name] = $aws[$field->name];
						}
					}
				}
			}
		}
		return $array;
	}

	function db_dtindo($tgl)
	{
		$arr_datetime = explode(" ",$tgl);
		$arr_date = explode("-",$arr_datetime[0]);
		if($tgl=='' || count($arr_date)==0)
		{
			return $tgl;
		}
		$indo_date = $arr_date[2].'-'.$arr_date[1].'-'.$arr_date[0];
		$arr_datetime[0]= $indo_date;			
		if(isset($arr_datetime[1]))
		{
			$arr_time = explode(":",$arr_datetime[1]);
			unset($arr_time[2]);
			$arr_datetime[1]=implode(":",$arr_time);
		}
		$date_final = implode(" ",$arr_datetime);
		return $date_final;
	}
	
	function db_close($con){
		mysqli_close($con);
	}
?>