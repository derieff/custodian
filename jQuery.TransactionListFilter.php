<?php
	include ("./config/config_db.php");

if($_REQUEST) {
	$optTHROLD_DocumentGroupID = $_REQUEST['optTHROLD_DocumentGroupID'];
	$optFilterHeader = $_REQUEST['optFilterHeader'];

	if ($optFilterHeader=="1") {
		$query="SELECT * 
				FROM M_Company
				WHERE Company_Delete_Time is NULL
				ORDER BY Company_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Perusahaan ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['Company_ID'];?>"><?php echo $arr['Company_Name'];?></option>
		<?php
		}
	}
	
	if ($optFilterHeader=="2") {
		$query="SELECT DISTINCT dc.DocumentCategory_ID, dc.DocumentCategory_Name
				FROM L_DocumentGroupCategoryType dgct, M_DocumentCategory dc
				WHERE dgct.DGCT_Delete_Time is NULL
				AND dgct.DGCT_DocumentGroupID=".$optTHROLD_DocumentGroupID."
				AND dgct.DGCT_DocumentCategoryID=dc.DocumentCategory_ID
				ORDER BY dc.DocumentCategory_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Kategori Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DocumentCategory_ID'];?>"><?php echo $arr['DocumentCategory_Name'];?></option>
		<?php
		}
	}

	if ($optFilterHeader=="3") {
		$query="SELECT DISTINCT dt.DocumentType_ID, dt.DocumentType_Name
				FROM L_DocumentGroupCategoryType dgct, M_DocumentType dt
				WHERE dgct.DGCT_Delete_Time is NULL
				AND dgct.DGCT_DocumentGroupID=".$optTHROLD_DocumentGroupID."
				AND dgct.DGCT_DocumentTypeID=dt.DocumentType_ID
				ORDER BY dt.DocumentType_Name";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Tipe Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DocumentType_ID'];?>"><?php echo $arr['DocumentType_Name'];?></option>
		<?php
		}
	}

	if ($optFilterHeader=="4") {
		$query="SELECT *
				FROM M_DocumentRegistrationStatus
				WHERE DRS_Delete_Time is NULL";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Status Transaksi ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['DRS_Name'];?>"><?php echo $arr['DRS_Description'];?></option>
		<?php
		}
	}
	
	if ($optFilterHeader=="5") {
		$query="SELECT *
				FROM M_LoanDetailStatus
				WHERE LDS_Delete_Time is NULL";
		$sql = mysql_query($query);?>
		<option value="0">--- Pilih Status Dokumen ---</option>
		<?php
		while ($arr = mysql_fetch_array($sql)) {?>
			<option value="<?php echo $arr['LDS_ID'];?>"><?php echo $arr['LDS_Name'];?></option>
		<?php
		}
	}?>
	
<?php	
}
?>
