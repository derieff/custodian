<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Pengeluaran Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
function pick(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOLD_DocCode'+n).value = result;
	window.close();
	}
}
function pickla(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOLAD_DocCode'+n).value = result;
	window.close();
	}
}
function pickao(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOAOD_DocCode'+n).value = result;
	window.close();
	}
}
function pickol(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOOLD_DocCode'+n).value = result;
	window.close();
	}
}
function pickonl(result, n) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.getElementById('txtTDRTOONLD_DocCode'+n).value = result;
	window.close();
	}
}
</SCRIPT>
<link href="./css/style.css" rel="stylesheet" type="text/css">
<style>
	.pageNumber{
		margin-left:3%;
		margin-right:3%;
		float:left;
	}
	.pagerContainer{
		width:25%;
		margin:auto;
	}
</style>
</HEAD>
<BODY>
<?PHP
$grup=$_GET['gID'];
$txtKe=$_GET['txtKe'];
$dataPerPage = 20;
$currPage = isset($_GET['page'])?$_GET['page']:0;
$search= isset($_GET['txtSearch'])?$_GET['txtSearch']:"";
if($currPage>0){
	$currPage--;
}
$maxDataNum=0;

if($grup==1){
	/* Query arief
	
	$query="SELECT *
              FROM TD_ReleaseOfLegalDocument tdrlold, TD_LoanOfLegalDocument tdlold, M_DocumentLegal dl
              WHERE tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
              AND dl.DL_DocCode=tdlold.TDLOLD_DocCode
              AND dl.DL_Status='4'
              AND tdrlold.TDROLD_ReturnCode='0'";
			  */
			  
	$query="SELECT tdlold.TDLOLD_DocCode,dc.DocumentCategory_Name,
				dt.DocumentType_Name,dl.DL_Instance,DATE_FORMAT(dl.DL_PubDate, '%j %M %Y') DL_PubDate,
				(SELECT COUNT(tdlold.TDLOLD_DocCode) Total
					FROM TD_ReleaseOfLegalDocument tdrlold
					LEFT JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
					  AND tdlold.TDLOLD_Delete_Time IS NULL
					LEFT JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode 
					  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
					LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
					  AND dc.DocumentCategory_Delete_Time IS NULL
					LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
					  AND dt.DocumentType_Delete_Time IS NULL
					WHERE tdrlold.TDROLD_ReturnCode='0'
					  AND tdrlold.TDROLD_Delete_Time IS NULL) Total
			FROM TD_ReleaseOfLegalDocument tdrlold
			LEFT JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
			  AND tdlold.TDLOLD_Delete_Time IS NULL
			LEFT JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode 
			  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
			LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
			  AND dc.DocumentCategory_Delete_Time IS NULL
			LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
			  AND dt.DocumentType_Delete_Time IS NULL
			WHERE tdrlold.TDROLD_ReturnCode='0'
			  AND tdrlold.TDROLD_Delete_Time IS NULL
			ORDER BY tdlold.TDLOLD_DocCode ASC
			LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='get' action='$PHP_SELF'>
				<input type='hidden' name='gID' value='$grup'/>
				<input type='hidden' name='txtKe' value='$txtKe'/>
				<input type='hidden' name='page' value='0'/>
				<div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
					<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
				</div>
			</form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<!--th>No. Pengeluaran</th-->
			<th width='20%'>Kode Dokumen</th>
			<th width='20%'>Kategori Dokumen</th>
			<th width='20%'>Tipe Dokumen</th>
			<th width='20%'>Instansi Terkait</th>
			<th width='20%'>Tanggal Terbit</th>
		<tr>
		<?PHP
		if(isset($_GET['txtSearch'])) {
			$query =   "SELECT tdlold.TDLOLD_DocCode,dc.DocumentCategory_Name,
							dt.DocumentType_Name,dl.DL_Instance,DATE_FORMAT(dl.DL_PubDate, '%j %M %Y') DL_PubDate,
							(SELECT COUNT(tdlold.TDLOLD_DocCode) Total
								FROM TD_ReleaseOfLegalDocument tdrlold
								LEFT JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
								  AND tdlold.TDLOLD_Delete_Time IS NULL
								LEFT JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode 
								  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
								LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
								  AND dc.DocumentCategory_Delete_Time IS NULL
								LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
								  AND dt.DocumentType_Delete_Time IS NULL
								WHERE tdrlold.TDROLD_ReturnCode='0'
								  AND tdrlold.TDROLD_Delete_Time IS NULL
								AND (
									tdlold.TDLOLD_DocCode LIKE '%$search%'
									OR dc.DocumentCategory_Name LIKE '%$search%'
									OR dt.DocumentType_Name LIKE '%$search%'
									OR dl.DL_Instance LIKE '%$search%'
									OR DL_PubDate LIKE '%$search%'
									OR MONTH(DL_PubDate) LIKE '%$search%'
								))Total
						FROM TD_ReleaseOfLegalDocument tdrlold
						LEFT JOIN TD_LoanOfLegalDocument tdlold ON tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
						  AND tdlold.TDLOLD_Delete_Time IS NULL
						LEFT JOIN M_DocumentLegal dl ON dl.DL_DocCode=tdlold.TDLOLD_DocCode 
						  AND dl.DL_Status='4' AND dl.DL_Delete_Time IS NULL
						LEFT JOIN M_DocumentCategory dc	ON dl.DL_CategoryDocID=dc.DocumentCategory_ID
						  AND dc.DocumentCategory_Delete_Time IS NULL
						LEFT JOIN M_DocumentType dt ON dl.DL_TypeDocID=dt.DocumentType_ID
						  AND dt.DocumentType_Delete_Time IS NULL
						WHERE tdrlold.TDROLD_ReturnCode='0'
						  AND tdrlold.TDROLD_Delete_Time IS NULL
						AND (
							tdlold.TDLOLD_DocCode LIKE '%$search%'
							OR dc.DocumentCategory_Name LIKE '%$search%'
							OR dt.DocumentType_Name LIKE '%$search%'
							OR dl.DL_Instance LIKE '%$search%'
							OR DL_PubDate LIKE '%$search%'
							OR MONTH(DL_PubDate) LIKE '%$search%'
						)
						ORDER BY tdlold.TDLOLD_DocCode ASC
						LIMIT ".($currPage*$dataPerPage).",$dataPerPage";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$maxDataNum=$arr['Total'];
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['TDLOLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['DocumentCategory_Name'] ?></td>
				<td align='center'><?= $arr['DocumentType_Name'] ?></td>
				<td align='center'><?= $arr['DL_Instance'] ?></td>
				<td align='center'><?= $arr['DL_PubDate'] ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==3){
	$query="SELECT *
			  FROM TD_ReleaseOfLandAcquisitionDocument tdrlolad,
				   TD_LoanOfLandAcquisitionDocument tdlolad, M_DocumentLandAcquisition dla
			  WHERE tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
			  AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode
			  AND dla.DLA_Status='4'
			  AND tdrlolad.TDRLOLAD_ReturnCode='0'";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>No. Pengeluaran</th>
			<th width='25%'>Jenis Dokumen</th>
			<th width='25%'>No. Dokumen</th>
			<th width='25%'>Tgl. Terbit</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_Information, thloaod.THLOAOD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloaod.THLOAOD_Delete_Time is NULL
						AND thloaod.THLOAOD_CompanyID=c.Company_ID
						AND thloaod.THLOAOD_UserID=u.User_ID
						AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
						AND thloaod.THLOAOD_Status='accept'
						AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
						AND tdloaod.TDLOAOD_Response='0'
						AND (
							thloaod.THLOAOD_LoanCode LIKE '%$search%'
							OR thloaod.THLOAOD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloaod.THLOAOD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$tgl_terbit=date("d M Y", strtotime($arr['DLA_RegTime']));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pickla('<?= $arr['TDLOLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['DL_NoDoc'] ?></td>
				<td align='center'><?= $tgl_terbit?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==4){
	$query="SELECT *
            #DL_NoDoc no_dokumen, DL_RegTime tgl_terbit_dokumen
              FROM TD_ReleaseOfAssetOwnershipDocument tdrloaod, TD_LoanOfAssetOwnershipDocument tdloaod, M_DocumentAssetOwnership dao
              WHERE tdrloaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
              AND dao.DAO_DocCode=tdloaod.TDLOAOD_DocCode
              AND dao.DAO_Status='4'
              AND tdrloaod.TDROAOD_ReturnCode='0'";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>No. Pengeluaran</th>
			<th width='25%'>Jenis Dokumen</th>
			<th width='25%'>No. Dokumen</th>
			<th width='25%'>Tgl. Terbit</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_Information, thloaod.THLOAOD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloaod.THLOAOD_Delete_Time is NULL
						AND thloaod.THLOAOD_CompanyID=c.Company_ID
						AND thloaod.THLOAOD_UserID=u.User_ID
						AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
						AND thloaod.THLOAOD_Status='accept'
						AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
						AND tdloaod.TDLOAOD_Response='0'
						AND (
							thloaod.THLOAOD_LoanCode LIKE '%$search%'
							OR thloaod.THLOAOD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloaod.THLOAOD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$tgl_terbit=date("d M Y", strtotime($arr['DAO_RegTime']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickao('<?= $arr['THLOAOD_LoanCode'] ?>', '<?=$txtKe;?>')"><?= $arr['THLOAOD_LoanCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['User_FullName'] ?></td>
				<td align='center'><?= $tgl_terbit ?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==5){
	$query="SELECT *
			  FROM TD_ReleaseOfOtherLegalDocuments tdrloold, TD_LoanOfOtherLegalDocuments tdloold, M_DocumentsOtherLegal dol
			  WHERE tdrloold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
			  AND dol.DOL_DocCode=tdloold.TDLOOLD_DocCode
			  AND dol.DOL_Status='4'
			  AND tdrloold.TDROOLD_ReturnCode='0'";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>No. Pengeluaran</th>
			<th width='25%'>Jenis Dokumen</th>
			<th width='25%'>No. Dokumen</th>
			<th width='25%'>Tgl. Terbit</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_Information, thloaod.THLOAOD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloaod.THLOAOD_Delete_Time is NULL
						AND thloaod.THLOAOD_CompanyID=c.Company_ID
						AND thloaod.THLOAOD_UserID=u.User_ID
						AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
						AND thloaod.THLOAOD_Status='accept'
						AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
						AND tdloaod.TDLOAOD_Response='0'
						AND (
							thloaod.THLOAOD_LoanCode LIKE '%$search%'
							OR thloaod.THLOAOD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloaod.THLOAOD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$tgl_terbit=date("d M Y", strtotime($arr['DOL_RegTime']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickol('<?= $arr['TDLOLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['DL_NoDoc'] ?></td>
				<td align='center'><?= $tgl_terbit?></td>
			</tr>
			<?PHP
		}
	}
}

elseif($grup==6){
	$query="SELECT *
			  FROM TD_ReleaseOfOtherNonLegalDocuments tdrloonld, TD_LoanOfOtherNonLegalDocuments tdloonld, M_DocumentsOtherNonLegal donl
			  WHERE tdrloonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
			  AND donl.DONL_DocCode=tdloonld.TDLOONLD_DocCode
			  AND donl.DONL_Status='4'
			  AND tdrloonld.TDROONLD_ReturnCode='0'";
	$sql = mysql_query($query);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Pengeluaran Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='25%'>No. Pengeluaran</th>
			<th width='25%'>Jenis Dokumen</th>
			<th width='25%'>No. Dokumen</th>
			<th width='25%'>Tgl. Terbit</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT DISTINCT thloaod.THLOAOD_LoanCode, thloaod.THLOAOD_Information, thloaod.THLOAOD_LoanDate, u.User_FullName,
										c.Company_Name, lc.LoanCategory_Name
						FROM TH_LoanOfAssetOwnershipDocument thloaod, TD_LoanOfAssetOwnershipDocument tdloaod,
							 M_User u, M_Company c, M_LoanCategory lc
						WHERE thloaod.THLOAOD_Delete_Time is NULL
						AND thloaod.THLOAOD_CompanyID=c.Company_ID
						AND thloaod.THLOAOD_UserID=u.User_ID
						AND thloaod.THLOAOD_LoanCategoryID=lc.LoanCategory_ID
						AND thloaod.THLOAOD_Status='accept'
						AND tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
						AND tdloaod.TDLOAOD_Response='0'
						AND (
							thloaod.THLOAOD_LoanCode LIKE '%$search%'
							OR thloaod.THLOAOD_LoanDate LIKE '%$search%'
							OR u.User_FullName LIKE '%$search%'
							OR c.Company_Name LIKE '%$search%'
							OR lc.LoanCategory_Name LIKE '%$search%'
						)
						ORDER BY thloaod.THLOAOD_LoanCode ASC
						LIMIT 0,10";
			$sql = mysql_query($query);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$tgl_terbit=date("d M Y", strtotime($arr['DONL_RegTime']));

			?>
			<tr>
				<td align='center'><u><a href="javascript:pickonl('<?= $arr['TDLOLD_DocCode'] ?>', '<?=$txtKe;?>')"><?= $arr['TDLOLD_DocCode'] ?></a></u></td>
				<td align='center'><?= $loandate ?></td>
				<td align='center'><?= $arr['DL_NoDoc'] ?></td>
				<td align='center'><?= $tgl_terbit?></td>
			</tr>
			<?PHP
		}
	}
}

?>
</TABLE>
<?php
	echo "<div id='pagerContainer' class='pagerContainer'>";
	if($maxDataNum>0){
		if($maxDataNum<$dataPerPage){
			echo "<span>1</span>";
		}
		else{
			$pageNum = ceil($maxDataNum/$dataPerPage);
			if($currPage>0){
				echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=0&txtSearch=$search'>".(1)."</a>";
			}
			if($currPage>3){
				echo "<span class='pageNumber'>...</span>";
			}
			if($currPage>1){
				if($currPage>2){
					echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1-2)."&txtSearch=$search'>".($currPage+1-2)."</a>";
				}
					echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1-1)."&txtSearch=$search'>".($currPage+1-1)."</a>";
			}
			echo "<form class='pageNumber' method='get' style='width:10%'>
						<input type='hidden' name='gID' value='$grup'/>
						<input type='hidden' name='txtKe' value='$txtKe'/>
						<input type='hidden' name='txtSearch' value='$search'/>
						<input type='text' style='width:100%;text-align:center;' name='page' value='".($currPage+1)."'/>
					</form>";
			
			if($currPage<$pageNum-2){
				echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1+1)."&txtSearch=$search'>".($currPage+1+1)."</a>";				
				if($currPage<$pageNum-3){
					echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($currPage+1+2)."&txtSearch=$search'>".($currPage+1+2)."</a>";
				}
			}
			if($currPage<$pageNum-4){
				echo "<span class='pageNumber'>...</span>";
			}
			if($currPage<$pageNum-1){
				echo "<a class='pageNumber' href='?gID=$grup&txtKe=$txtKe&page=".($pageNum-1)."&txtSearch=$search'>".$pageNum."</a>";
			}
		}
	}
	echo "<div style='clear:both'></div>";
?>
</BODY>
</HTML>
