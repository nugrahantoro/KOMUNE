<?php

class Manajemen_User extends Controller{
	
	function load_akses()
	{
		$dt = $_POST;
		$sql = "SELECT
					menu_parent_admin_porting.id,
					menu_parent_admin_porting.menu_name,
					id_sesi,
					GROUP_CONCAT( sub_menuname ORDER BY menu_child_admin_porting.urutan SEPARATOR '</li><li>'  ) as submenu,	
					IF( id_sesi IS NULL, 0, 1 ) AS akses 
				FROM
					menu_parent_admin_porting
				LEFT JOIN 
					(
						SELECT * FROM user_akses_porting WHERE id_sesi = '$dt[id_user]'
					) as  ua
					 ON menu_parent_admin_porting.id = ua.id_menu
				LEFT JOIN menu_child_admin_porting ON menu_parent_admin_porting.id = menu_child_admin_porting.menu_id 
				GROUP BY menu_parent_admin_porting.id
				ORDER BY
					menu_parent_admin_porting.urutan";
		$res = db_query($sql);
		$chklist = '';
		while($dt = mysqli_fetch_array($res))
		{
			$checked = '';
			if($dt['akses']==1)
			{
				$checked = 'checked';
			}
			$submenu = '';
			if(!is_null($dt['submenu']))
			{
				$submenu = 'data-bs-toggle="tooltip" data-bs-html="true"  data-bs-placement="right" 
							data-bs-title="<em>Submenu</em> <br> <b><ul><li>'.$dt['submenu'].'</li></ul></b>"';
			}
			$chklist .= '<div class="form-check mb-2 ml-3 cari-item">
							<input class="form-check-input" type="checkbox" id_menu="'.$dt['id'].'" id="menu'.$dt['id'].'" '.$checked.'>
							<label class="form-check-label cari-q" for="menu'.$dt['id'].'" '.$submenu.' >
							   '.$dt['menu_name'].'
							</label>
						</div>';
		}
		$nama_user = db_get("SELECT nama_us FROM users_log WHERE id = '$_POST[id_user]'");
		$div ='<div>
                    '.$chklist.'
                </div>
				<script>
					$('."'".'[data-bs-toggle="tooltip"]'."'".').tooltip(); 
					$("#cur_user").html("('.$nama_user.')")
				</script>';
		
		echo $div;
	}
	
	function save_akses()
	{
		$cb['stat']=0;
		$cb['ket']='';
		$id_menu=post('id_menu',[]);
		$id_user=$_POST['id_user'];
		$akses = db_query("
			SELECT
				* 
			FROM
				user_akses_porting 
			WHERE
				id_sesi = '$id_user'
		");
		$ins = $id_menu;
		$del = []; 
		while($dt=mysqli_fetch_array($akses))
		{
			if(count($id_menu)>0)
			{
				if(in_array($dt['id_menu'],$id_menu))
				{
					$ky = array_search($dt['id_menu'],$ins);
					unset($ins[$ky]);
				}
			}
			else
			{
				array_push($del,$dt['id_usakses']);
			}
		}
		$res = 1;
		if(count($ins)>0)
		{
			foreach($ins as $im)
			{
				$que="INSERT INTO 
						user_akses_porting(id_menu,id_sesi,statujs)
					VALUES ($im,'$id_user',1)";
				$res = db_query($que);
			}
		}
		if(count($del)>0)
		{
			$imp_id = implode(',',$del);
			$que = "DELETE FROM user_akses_porting WHERE id_usakses IN ($imp_id) AND id_sesi = '$id_user'";
			$res = db_query($que);
		}
		if($res)
		{
			$cb['stat']=1;
		}
		else
		{
			$cb['ket']='Error';
		}
		echo json_encode($cb);
	}
}

?>