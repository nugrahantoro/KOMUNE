<?php

class Manajemen_Menu extends Controller{
	
	function data_menu()
	{
		$sql = 'SELECT
					* 
				FROM
					menu_parent_admin_porting
				ORDER BY
					urutan';
		$res = db_query($sql);
		$order=[];
		while($dt = mysqli_fetch_array($res))
		{
			$icon ='';
			if(!is_null($dt['img_two']))
			{
				$icon='<i data-feather="'.$dt['img_two'].'"></i>';
			}
			else
			{
				$icon='<i data-feather="circle"></i>';
			}
			$url = 'URL : '.$dt['url'];
			if($dt['class']=='accordion')
			{
				$url = $dt['class'];
			}
			echo '<li class="cari-item item_menu" id_menu="'.$dt['id'].'" onclick="menu_click(this);">
					<a href="javascript: void(0);" style="padding: 6px 20px;">
						<div class="d-flex align-items-start">
							<div class="flex-shrink-0 user-img align-self-center me-2">
								'.$dt['urutan'].'
							</div>
							<div class="flex-shrink-0 user-img align-self-center me-3">
								'.$icon.'
							</div>
							<div class="flex-grow-1 overflow-hidden">
								<h5 class="text-truncate font-size-14 mb-0 cari-q">'.$dt['menu_name'].'</h5>
								<p class="text-truncate mb-0 cari-q">'.$url.'</p>
								<div class="unread-message">
									<button class="btn btn-sm btn-outline-dark" title ="Edit" onclick="modal_menu(2,'.$dt['id'].')"><icon class="bx bx-edit"></i></button>
								</div>
							</div>
						</div>
					</a>
				</li>';
			array_push($order,$dt['id']);
		}
		echo '<li class="menu_no_result" style="display:none;">
				<a href="javascript: void(0);">
					<div class="d-flex align-items-start">
						<div class="flex-shrink-0 user-img align-self-center me-3 "></div>
						<div class="flex-grow-1 overflow-hidden">
							<p class="text-truncate mb-0 ">Tidak ada data</p>
						</div>
					</div>
				</a>
			</li>';
		echo "<script>
				feather.replace();
				$('#col_data_menu').sortable({
				animation: 150,
				ghostClass: 'drag-background-class'
			});
			glob_order='".implode(',',$order)."';
			</script>";
	}
	
	function get_menu()
	{
		 $sql = "SELECT
					* 
				FROM
					menu_parent_admin_porting
				WHERE 
					id = $_POST[id_menu]";
		$data = db_assoc($sql);
		echo json_encode($data);
	}
	
	function get_submenu()
	{
		 $sql = "SELECT
					* 
				FROM
					menu_child_admin_porting
				WHERE 
					id = $_POST[id_submenu]";
		$data = db_assoc($sql);
		echo json_encode($data);
	}
	
	function save_menu()
	{
		$cb['stat']=0;
		$cb['ket']='';
		$mn=$_POST;
		$id_menu=post('id_menu',[]);
		$res = 1;
		if($mn['mode']==1)
		{
			$sql = "INSERT INTO menu_parent_admin_porting(menu_name, url, class, urutan, img_two)
					VALUES 
					(
						'$mn[nama]',
						'$mn[url]',
						'$mn[jenis]',
						$mn[urutan],
						'$mn[icon]'
					)";						
		}
		else
		{
			$sql = "UPDATE menu_parent_admin_porting SET 
						menu_name = '$mn[nama]',
						url = '$mn[url]',
						class = '$mn[jenis]',
						urutan = $mn[urutan],
						img_two = '$mn[icon]'
					WHERE
						id = $id_menu";
		}
		$res = db_query($sql);
		if(!($id_menu>0))
		{
			$id_menu = db_id();
		}
		if($res)
		{
			$cb['stat']=1;
			$cb['id_menu']=$id_menu;
		}
		else
		{
			$cb['ket']='Error';
		}
		echo json_encode($cb);
	}
	
	function save_submenu()
	{
		$cb['stat']=0;
		$cb['ket']='';
		$smn=$_POST;
		$id_submenu=post('id_submenu',[]);
		$res = 1;
		if($smn['mode']==1)
		{
			$sql = "INSERT INTO menu_child_admin_porting(sub_menuname, menu_name, href, urutan,menu_id)
					VALUES 
					(
						'$smn[nama]',
						'#',
						'$smn[url]',
						$smn[urutan],
						$smn[id_menu]
					)";						
		}
		else
		{
			$sql = "UPDATE menu_child_admin_porting SET 
						sub_menuname = '$smn[nama]',
						href = '$smn[url]',
						urutan = $smn[urutan]
					WHERE
						id = $id_submenu";
		}
		$res = db_query($sql);
		if(!($id_submenu>0))
		{
			$id_submenu = db_id();
		}
		if($res)
		{
			$cb['stat']=1;
			$cb['id_submenu']=$id_submenu;
		}
		else
		{
			$cb['ket']='Error';
		}
		echo json_encode($cb);
	}
	
	function del_menu ()
	{
		$cb['stat']=0;
		$cb['ket']=''; 
		$id_menu = $_POST['id_menu'];
		$force = $_POST['force'];
		$count = db_get("SELECT
							COUNT(*) 
						FROM
							user_akses 
						WHERE
							id_menu = $id_menu");
		if($count==0 || $force ==1)
		{
			$sql = "DELETE FROM menu_parent_admin_porting WHERE id = $id_menu";
			$res = db_query($sql);
			if($res)
			{
				$cb['stat']=1;
			}
			else
			{
				$cb['ket']='Error';
			}
		}
		else
		{
			$cb['stat']=2;
		}
		echo json_encode($cb);
	}
	
	function del_submenu ()
	{
		$cb['stat']=0;
		$cb['ket']=''; 
		$id_submenu = $_POST['id_submenu'];
		$force = $_POST['force'];
		/* $count = db_get("SELECT
							COUNT(*) 
						FROM
							user_akses 
						WHERE
							id_submenu = $id_submenu"); */
		$count=0;
		$force=1;
		if($count==0 || $force ==1)
		{
			$sql = "DELETE FROM menu_child_admin_porting WHERE id = $id_submenu";
			$res = db_query($sql);
			if($res)
			{
				$cb['stat']=1;
			}
			else
			{
				$cb['ket']='Error';
			}
		}
		else
		{
			$cb['stat']=2;
		}
		echo json_encode($cb);
	}
	
	function sort_menu()
	{
		$cb['stat']=0;
		$cb['ket']=''; 
		$order = $_POST['order'];
		$no = 1;
		$condition = '';
		foreach($order as $id_menu)
		{
			$condition.= " WHEN id = $id_menu THEN $no 
							";
			$no++;
		}
		$sql = "UPDATE menu_parent_admin_porting SET urutan = 
					(
						CASE
							$condition
							ELSE urutan
						END
					)";
		$res = db_query($sql);
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
	
	function sort_submenu()
	{
		$cb['stat']=0;
		$cb['ket']=''; 
		$order = $_POST['suborder'];
		$no = 1;
		$condition = '';
		foreach($order as $id_submenu)
		{
			$condition.= " WHEN id = $id_submenu THEN $no 
							";
			$no++;
		}
		$sql = "UPDATE menu_child_admin_porting SET urutan = 
					(
						CASE
							$condition
							ELSE urutan
						END
					)";
		$res = db_query($sql);
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
	
	function data_submenu()
	{
		$id_menu=$_POST['id_menu'];
		$sql = "SELECT
					* 
				FROM
					menu_child_admin_porting 
				WHERE
					menu_id = $id_menu 
				ORDER BY
					urutan";
		$res = db_query($sql);
		$menu = db_assoc("SELECT
							menu_name,
							class
						FROM
							menu_parent_admin_porting 
						WHERE
							id = $id_menu");
		$order=[];
		$li = '';
		while($dt = mysqli_fetch_array($res))
		{
			$url = 'URL : '.$dt['href'];
			if($dt['href']=='')
			{
				$url.='-';
			}
			$li.= '<li class="cari-item item_submenu" id_submenu="'.$dt['id'].'" >
					<a href="javascript: void(0);" style="padding: 6px 20px;">
						<div class="d-flex align-items-start">
							<div class="flex-shrink-0 user-img align-self-center me-3">
								'.$dt['urutan'].'
							</div>
							<div class="flex-grow-1 overflow-hidden">
								<h5 class="text-truncate font-size-14 mb-0 cari-q">'.$dt['sub_menuname'].'</h5>
								<p class="text-truncate mb-0 cari-q">'.$url.'</p>
								<div class="unread-message">
									<button class="btn btn-sm btn-outline-dark" title ="Edit" onclick="modal_submenu(2,'.$dt['id'].')"><icon class="bx bx-edit"></i></button>
								</div>
							</div>
						</div>
					</a>
				</li>';
			array_push($order,$dt['id']);
		}
		$li.= '<li class="submenu_no_result" style="display:none;">
				<a href="javascript: void(0);">
					<div class="d-flex align-items-start">
						<div class="flex-shrink-0 user-img align-self-center me-3 "></div>
						<div class="flex-grow-1 overflow-hidden">
							<p class="text-truncate mb-0 ">Tidak ada data</p>
						</div>
					</div>
				</a>
			</li>';
		$li.= "<script>
				//feather.replace();
				$('#col_data_submenu').sortable({
				animation: 150,
				ghostClass: 'drag-background-class'
			});
			glob_suborder='".implode(',',$order)."';
			</script>";
		
		
		$data = $menu;
		if($menu['class']=='single')
		{
			$data['count']='-';
		}
		else
		{
			$data['count']=mysqli_num_rows($res);
		}
		$data['html']=$li;
		echo json_encode($data);
	}
	
}

?>