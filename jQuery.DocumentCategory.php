<?php
	include ("./config/config_db.php");

if($_REQUEST)
{
	$GroupID = $_REQUEST['GroupID'];
	$query="SELECT DISTINCT dc.DocumentCategory_ID,dc.DocumentCategory_Name 
			FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
			WHERE dgct.DGCT_DocumentGroupID=".$GroupID." 
			AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
			AND dgct.DGCT_Delete_Time is NULL";
	$sql = mysql_query($query);
	$num=mysql_num_rows($query);
	if ($num=="0"){?>
	<option value="0">--- Tidak Ada ---</option>
<?PHP		
	}
	else{?>
	<option value="0">--- Pilih Kategori Dokumen ---</option>
	<?php
	while ($arr = mysql_fetch_array($sql)) {?>
		<option value="<?php echo $arr['DocumentCategory_ID'];?>" style="width:500px"><?php echo $arr['DocumentCategory_Name'];?></option>
	<?php
	}
	}?>
	
<?php	
}
?>
