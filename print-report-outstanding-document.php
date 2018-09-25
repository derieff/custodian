<?PHP session_start(); ?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Dokumen Outstanding</title>
<link rel="shortcut icon" type="image/x-icon" href="./images/favicon.ico">
<link href="./css/style-print.css" rel="stylesheet" type="text/css">
<script src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript">
 	$(document).ready(function(){
   	$(".stripeMe tr").mouseover(function(){$(this).addClass("over");}).mouseout(function(){$(this).removeClass("over");});
   	$(".stripeMe tr:even").addClass("alt");
 	});
</script>
<SCRIPT>
function printPage(){
document.getElementById('PrintButton').style.display = "none"
window.print()
document.getElementById('PrintButton').style.display = "block"
}
</SCRIPT>

</head>
<?PHP
// Validasi untuk user yang terdaftar
if(!isset($_SESSION['User_ID'])) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
?>

<body>
<div id='header'>
<input type='button' name="PrintButton" id="PrintButton" onclick='printPage()' value='CETAK' class='print-button' />
	<div id='header-inside'>
    	<div class="tap">PT Triputra Agro Persada </div>
        <div class="custodian">Custodian Department </div>
        <div class="alamat">Jalan DR.Ide Anak Agung Gde Agung Kav. E.3.2. No 1<br />
        Jakarta - 12950</div>
    </div>
</div>
<div id='content'>
<?PHP
if ($_POST[optDocumentGroup]<>"3") {
	$qcompany=(!$_POST['optCompany'])?"":"AND dl.DL_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
	$qcategory=(!$_POST['optDocumentCategory'])?"":"AND dl.DL_CategoryDocID='$_POST[optDocumentCategory]'";
		
	$query = "SELECT DISTINCT dl.DL_DocCode, dt.DocumentType_Name, dl.DL_NoDoc, dl.DL_PubDate, thlold.THLOLD_LoanCode,
					   	 thlold.THLOLD_LoanDate, u.User_FullName, dp.Department_Name, thrlold.THROLD_ReleaseCode,
						 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime, DL_Instance,
						 datediff(sysdate(), tdrlold.TDROLD_LeadTime) AS keterlambatan,
						 dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
						 dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentLegal dl, M_Company c, M_DocumentCategory dc, M_DocumentType dt, 
			  		   M_DocumentGroup dg, TH_ReleaseOfLegalDocument thrlold, TH_LoanOfLegalDocument thlold,
					   TD_ReleaseOfLegalDocument tdrlold, TD_LoanOfLegalDocument tdlold, M_User u, M_Department dp,
					   M_DivisionDepartmentPosition ddp
				  WHERE dl.DL_GroupDocID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qcategory
				  AND dl.DL_GroupDocID=dg.DocumentGroup_ID
				  AND dl.DL_CompanyID=c.Company_ID
				  AND dl.DL_CategoryDocID=dc.DocumentCategory_ID
				  AND dl.DL_TypeDocID=dt.DocumentType_ID
				  AND dl.DL_DocCode=tdlold.TDLOLD_DocCode 
				  AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				  AND thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				  AND thrlold.THROLD_ID=tdrlold.TDROLD_THROLD_ID
				  AND thlold.THLOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND tdrlold.TDROLD_LeadTime<>'0000-00-00 00:00:00'
				  AND dl.DL_Status='4'
				  AND tdrlold.TDROLD_ReturnCode NOT IN (SELECT TDRTOLD_ReturnCode 
				  										FROM TD_ReturnOfLegalDocument
														WHERE TDRTOLD_Delete_Time IS NULL)
				  AND dl.DL_Delete_Time IS NULL";
}
if ($_POST[optDocumentGroup]=="3") {
	$qcompany=(!$_POST['optCompany'])?"":"AND dla.DLA_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		
	$query = "SELECT DISTINCT dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period, dla.DLA_Village, dla.DLA_Block, dla.DLA_Owner, 
						 thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, u.User_FullName, dp.Department_Name, 
						 thrlolad.THRLOLAD_ReleaseCode,thrlolad.THRLOLAD_ReleaseDate, 
						 tdrlolad.TDRLOLAD_LeadTime, datediff(sysdate(), tdrlolad.TDRLOLAD_LeadTime) AS keterlambatan,
						 c.Company_ID,c.Company_Name,dg.DocumentGroup_ID,dg.DocumentGroup_Name
				  FROM M_DocumentLandAcquisition dla, M_Company c, M_DocumentGroup dg,
			  		   TH_ReleaseOfLandAcquisitionDocument thrlolad, TH_LoanOfLandAcquisitionDocument thlolad,
					   TD_ReleaseOfLandAcquisitionDocument tdrlolad, TD_LoanOfLandAcquisitionDocument tdlolad, 
					   M_User u, M_Department dp, M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  AND dla.DLA_CompanyID=c.Company_ID
				  AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode 
				  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				  AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				  AND thrlolad.THRLOLAD_ID=tdrlolad.TDRLOLAD_THRLOLAD_ID
				  AND thlolad.THLOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND dla.DLA_Status='4'
				  AND tdrlolad.TDRLOLAD_LeadTime<>'0000-00-00 00:00:00'
				  AND tdrlolad.TDRLOLAD_ReturnCode NOT IN (SELECT TDRTOLAD_ReturnCode 	 
				  										   FROM TD_ReturnOfLandAcquisitionDocument
														   WHERE TDRTOLAD_Delete_Time IS NULL)
				  AND dla.DLA_Delete_Time IS NULL";
}
$sql = mysql_query($query);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);
?>
<div id='title'>Laporan Dokumen Outstanding</div>
<div class='h5'>Tanggal Cetak : <?PHP echo date('j M Y'); ?></div>

<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
    <?PHP if ($_POST[optDocumentGroup]<>"3") { ?>
	<tr>
    	<td width="14%">Perusahaan</td>
        <td width="1%">:</td>
    	<td width="35%"><?PHP echo"$h_arr[Company_Name]"; ?></td>
        <td width="14%"></td>
        <td width="1%"></td>
        <td width="35%"></td>
    </tr>
	<tr>
    	<td width="14%">Grup Dokumen</td>
        <td width="1%">:</td>
    	<td width="35%"><?PHP echo"$h_arr[DocumentGroup_Name]"; ?></td>
        <td width="14%">Kategori Dokumen</td>
        <td width="1%">:</td>
        <td width="35%"><?PHP echo"$h_arr[DocumentCategory_Name]"; ?></td>
    </tr>
    <?PHP } 
	else if ($_POST[optDocumentGroup]=="3") { ?>
	<tr>
    	<td width="14%">Perusahaan</td>
        <td width="1%">:</td>
    	<td width="35%"><?PHP echo"$h_arr[Company_Name]"; ?></td>
    	<td width="14%">Grup Dokumen</td>
        <td width="1%">:</td>
    	<td width="35%"><?PHP echo"$h_arr[DocumentGroup_Name]"; ?></td>
    </tr>
    <?PHP } ?>

</table>
<?PHP if ($_POST[optDocumentGroup]<>"3") { ?>
<table width='100%' border='1' cellpadding='0' cellspacing='0'>
	<tr>
		<th>Kode Dokumen</th>
		<th>Nama Dokumen</th>
		<th>Instansi Terkait</th>
		<th>Nomor Dokumen</th>
		<th>Berlaku</th>
		<th>Kode Permintaan</th>	
		<th>Tanggal Permintaan</th>	
		<th>Nama Peminta</th>
		<th>Departemen</th>
		<th>Kode Pengeluaran</th>	
		<th>Tanggal Pengeluaran</th>	
		<th>Tanggal Jatuh Tempo</th>
		<th>Lead Time (hari)</th>
	</tr>
<?PHP
	$jumdata=0;
	while ($arr = mysql_fetch_array($sql)) {
		$berlaku=date("j M Y", strtotime($arr['DL_PubDate']));
		$reqdate=date("j M Y", strtotime($arr['THLOLD_LoanDate']));
		$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
		$duedate=date("j M Y", strtotime($arr['TDROLD_LeadTime']));
	
		if ($jumdata==8) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else {
			$style="";
		}
	
		echo ("
			<tr>
				<td class='center'>$arr[DL_DocCode]</td>
				<td class='center'>$arr[DocumentType_Name]</td>
				<td class='center'>$arr[DL_Instance]</td>
				<td class='center'>$arr[DL_NoDoc]</td>
				<td class='center'>$berlaku</td>
				<td class='center'>$arr[THLOLD_LoanCode]</td>
				<td class='center'>$reqdate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[THROLD_ReleaseCode]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$duedate</td>
				<td class='center'>$arr[keterlambatan]</td>
			</tr>
	
		");
		$jumdata ++;
	}
?>
</table>
<?PHP } ?>
<?PHP if ($_POST[optDocumentGroup]=="3") { ?>
<table width='100%' border='1' cellpadding='0' cellspacing='0'>
	<tr>
		<th>Kode Dokumen</th>
		<th>Tahap Pembebasan Lahan</th>
		<th>Periode Pembebasan Lahan</th>
		<th>Desa</th>
		<th>Blok</th>
		<th>Pemilik</th>
		<th>Kode Permintaan</th>	
		<th>Tanggal Permintaan</th>	
		<th>Nama Peminta</th>
		<th>Departemen</th>
		<th>Kode Pengeluaran</th>	
		<th>Tanggal Pengeluaran</th>	
		<th>Tanggal Jatuh Tempo</th>
		<th>Lead Time (hari)</th>
	</tr>
<?PHP
	$jumdata=0;
	while ($arr = mysql_fetch_array($sql)) {
			$periode=date("j M Y", strtotime($arr['DLA_Period']));
			$reqdate=date("j M Y", strtotime($arr['THLOLAD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
	
		if ($jumdata==8) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else {
			$style="";
		}
	
		echo ("
			<tr>
				<td class='center'>$arr[DLA_Code]</td>
				<td class='center'>$arr[DLA_Phase]</td>
				<td class='center'>$periode</td>
				<td class='center'>$arr[DLA_Village]</td>
				<td class='center'>$arr[DLA_Block]</td>
				<td class='center'>$arr[DLA_Owner]</td>
				<td class='center'>$arr[THLOLAD_LoanCode]</td>
				<td class='center'>$reqdate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$duedate</td>
				<td class='center'>$arr[keterlambatan]</td>
			</tr>
	
		");
		$jumdata ++;
	}
?>
</table>
<?PHP } ?>

<table width='40%' border='1' cellpadding='0' cellspacing='0' align='right'>
	<tr>
    	<td width='10%' class='center'>
        	Dibuat
        </td>
    	<td width='10%' class='center'>
        	Diperiksa
        </td>
    	<td width='20%' class='center' colspan='2'>
        	Disetujui
        </td>
    </tr>
    <tr>
    	<td height='60px'>&nbsp;
</td>
        <td>&nbsp;
</td><td>&nbsp;
</td><td>&nbsp;
</td>
    </tr>
	<tr>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user1]"; ?>
        </td>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user2]"; ?>
        </td>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user3]"; ?>
        </td>
    	<td width='10%' class='center'>
        	<?PHP echo"$_POST[user4]"; ?>
        </td>
    </tr>
</table>
</div>
</body>
</html>
<?PHP } ?>