<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 06 Juni 2012																						=
= Update Terakhir	: 06 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start(); 
?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Pengembalian Dokumen</title>
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
$txtStart=date('Y-m-d H:i:s', strtotime($_POST['tStart']));
$txtEnd=date('Y-m-d H:i:s', strtotime($_POST['tEnd']));

$start=date('j M Y', strtotime($_POST['tStart']));
$end=date('j M Y', strtotime($_POST['tEnd']));
$periode=((!$_POST['tStart'])&&(!$_POST['tEnd']))?"":"Periode $start s/d $end";

if ($_POST[optDocumentGroup]<>"3") {
	$qcompany=(!$_POST['optCompany'])?"":"AND thlold.THLOLD_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
	$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdlold.TDLOLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
	$qperiod=((!$_POST['tStart'])&&(!$_POST['tEnd']))?"":"AND tdrtold.TDRTOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";
	
	$query = "SELECT DISTINCT tdrtold.TDRTOLD_ReturnCode, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate, 
								  u.User_FullName, dp.Department_Name, 
								  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
								  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc,
			  		   TH_ReleaseOfLegalDocument thrlold, TD_ReleaseOfLegalDocument tdrlold,
			  		   TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold,
			  		   TD_ReturnOfLegalDocument tdrtold, M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				  AND tdrtold.TDRTOLD_Delete_Time IS NULL 
				  AND tdrlold.TDROLD_ReturnCode=tdrtold.TDRTOLD_ReturnCode 
				  AND tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				  AND tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
				  AND tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode 
				  AND thlold.THLOLD_DocumentGroupID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thlold.THLOLD_CompanyID=c.Company_ID
				  AND tdlold.TDLOLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thlold.THLOLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
}
elseif ($_POST[optDocumentGroup]=='3'){
	$qcompany=(!$_POST['optCompany'])?"":"AND thlolad.THLOLAD_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
	$qperiod=((!$_POST['tStart'])&&(!$_POST['tEnd']))?"":"AND tdrtolad.TDRTOLAD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";

	$query = "SELECT DISTINCT tdrtolad.TDRTOLAD_ReturnCode, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, 
								  u.User_FullName, dp.Department_Name, 
								  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_User u, M_Department dp,
			  		   TH_ReleaseOfLandAcquisitionDocument thrlolad, TD_ReleaseOfLandAcquisitionDocument tdrlolad,
			  		   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad,
			  		   TD_ReturnOfLandAcquisitionDocument tdrtolad, M_DivisionDepartmentPosition ddp
				  WHERE thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL 
				  AND tdrlolad.TDRLOLAD_ReturnCode=tdrtolad.TDRTOLAD_ReturnCode 
				  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				  AND tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
				  $qcompany
				  $qarea
				  $qperiod
				  AND dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
				  AND thlolad.THLOLAD_CompanyID=c.Company_ID
				  AND thlolad.THLOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID";
}
$sql = mysql_query($query);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);
$jumdata=0;


echo"
<div id='title'>Laporan Pengembalian Dokumen</div>
<div class='h2'>$periode</div>
<div class='h5'>Tanggal Cetak : ".date('j M Y')."</div>";

if ($_POST[optDocumentGroup]<>"3") { 
	$jumdata=0;
	while ($h_arr = mysql_fetch_array($sql)) {
		if ($jumdata==2) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}

		$loandate=date("j M Y", strtotime($h_arr['THLOLD_LoanDate']));
		$d_query="SELECT tdrtold.TDRTOLD_ReturnTime, tdrtold.TDRTOLD_DocCode, dt.DocumentType_Name, 
						 dl.DL_Instance, dl.DL_NoDoc, dl.DL_ExpDate,thrlold.THROLD_ReleaseCode,
						 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime
				  FROM TD_ReturnOfLegalDocument tdrtold
				  LEFT JOIN M_DocumentLegal dl 
				  ON tdrtold.TDRTOLD_DocCode=dl.DL_DocCode
				  LEFT JOIN M_DocumentType dt
				  ON dl.DL_TypeDocID=dt.DocumentType_ID
				  LEFT JOIN TD_ReleaseOfLegalDocument tdrlold
				  ON tdrlold.TDROLD_ReturnCode= tdrtold.TDRTOLD_ReturnCode
				  INNER JOIN TH_ReleaseOfLegalDocument thrlold
				  ON tdrlold.TDROLD_TDLOLD_ID=thrlold.THROLD_ID
				  WHERE tdrtold.TDRTOLD_ReturnCode='$h_arr[TDRTOLD_ReturnCode]' 
				  AND tdrtold.TDRTOLD_Delete_Time IS NULL";		
		$d_sql=mysql_query($d_query);
					  
		echo"
		<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
		<tr>
			<td width='19%'>Kode Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$h_arr[THLOLD_LoanCode]</td>
			<td width='19%'>Tanggal Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$loandate</td>
		</tr>
		<tr>
			<td>Nama Peminta</td>
			<td>:</td>
			<td>$h_arr[User_FullName]</td>
			<td>Departemen</td>
			<td>:</td>
			<td>$h_arr[Department_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>:</td>
			<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
			<td></td><td></td><td></td>
		</tr>
		<tr>
			<td>Grup Dokumen</td>
			<td>:</td>
			<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
			<td>Kategori Dokumen</td>
			<td>:</td>
			<td><input type='hidden' name='optDocumentCategory' value=$h_arr[DocumentCategory_ID]>$h_arr[DocumentCategory_Name]</td>
		</tr>
		</table>
				
		<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
		<tr>
			<th width='200'>Tanggal Pengembalian</th>
			<th width='200'>Kode Dokumen</th>
			<th width='200'>Nama Dokumen</th>
			<th width='200'>Instansi Terkait</th>
			<th width='200'>Nomor Dokumen</th>
			<th width='200'>Berlaku Sampai</th>	
			<th width='200'>Kode Pengeluaran</th>	
			<th width='200'>Tanggal Pengeluaran</th>	
			<th width='200'>Lead Time</th>	
		</tr>";

		while ($arr = mysql_fetch_array($d_sql)) {
			$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
			$retdate=date("j M Y", strtotime($arr['TDRTOLD_ReturnTime']));
			if ($arr['DL_ExpDate']=="0000-00-00 00:00:00")
				$expdate="-";
			else
				$expdate=date("j M Y", strtotime($arr['DL_ExpDate']));
			if ($arr['TDROLD_LeadTime']=="0000-00-00 00:00:00")
				$leaddate="-";
			else
				$leaddate=date("j M Y", strtotime($arr['TDROLD_LeadTime']));
		echo"
		<tr>
			<td class='center'>$retdate</td>
			<td class='center'>$arr[TDRTOLD_DocCode]</td>
			<td class='center'>$arr[DocumentType_Name]</td>
			<td class='center'>$arr[DL_Instance]</td>
			<td class='center'>$arr[DL_NoDoc]</td>
			<td class='center'>$expdate</td>
			<td class='center'>$arr[THROLD_ReleaseCode]</td>
			<td class='center'>$reldate</td>
			<td class='center'>$leaddate</td>
		</tr>";
		}
		echo"</table>";
		$jumdata++;
	}	
}

elseif ($_POST[optDocumentGroup]=='3'){
	$jumdata=0;
	while ($h_arr = mysql_fetch_array($sql)) {
		if ($jumdata==2) {
			$style="style='page-break-after:always'";
			$jumdata=0;
		}
		else
		{
			$style="";
		}

		$loandate=date("j M Y", strtotime($h_arr['THLOLAD_LoanDate']));

		$d_query="SELECT tdrtolad.TDRTOLAD_ReturnTime, tdrtolad.TDRTOLAD_DocCode, dla.DLA_Phase, 
						 dla.DLA_Period, dla.DLA_Village, dla.DLA_Block,
					 	 dla.DLA_Owner, thrlolad.THRLOLAD_ReleaseCode,
						 thrlolad.THRLOLAD_ReleaseDate, tdrlolad.TDRLOLAD_LeadTime
				  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad
				  LEFT JOIN M_DocumentLandAcquisition dla
				  ON tdrtolad.TDRTOLAD_DocCode=dla.DLA_Code
				  LEFT JOIN TD_ReleaseOfLandAcquisitionDocument tdrlolad
				  ON tdrlolad.TDRLOLAD_ReturnCode= tdrtolad.TDRTOLAD_ReturnCode
				  INNER JOIN TH_ReleaseOfLandAcquisitionDocument thrlolad
				  ON tdrlolad.TDRLOLAD_TDLOLAD_ID=thrlolad.THRLOLAD_ID
				  WHERE tdrtolad.TDRTOLAD_ReturnCode='$h_arr[TDRTOLAD_ReturnCode]' 
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL";		
		$d_sql=mysql_query($d_query);

		echo"
		<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
		<tr>
			<td width='19%'>Kode Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$h_arr[THLOLAD_LoanCode]</td>
			<td width='19%'>Tanggal Permintaan</td>
			<td width='1%'>:</td>
			<td width='30%'>$loandate</td>
		</tr>
		<tr>
			<td>Nama Peminta</td>
			<td>:</td>
			<td>$h_arr[User_FullName]</td>
			<td>Departemen</td>
		<td>:</td>
			<td>$h_arr[Department_Name]</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>:</td>
			<td><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
			<td>Grup Dokumen</td>
			<td>:</td>
			<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
		</tr>
		</table>
		
		<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
		<tr>
			<th width='200'>Tanggal Pengembalian</th>
			<th width='200'>Kode Dokumen</th>
			<th width='50'>Tahap</th>
			<th width='200'>Periode</th>
			<th width='200'>Desa</th>
			<th width='200'>Blok</th>	
			<th width='200'>Pemilik</th>	
			<th width='200'>Kode Pengeluaran</th>	
			<th width='200'>Tanggal Pengeluaran</th>	
			<th width='200'>Lead Time</th>	
		</tr>";

		while ($arr = mysql_fetch_array($d_sql)) {
			$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
			$retdate=date("j M Y", strtotime($arr['TDRTOLAD_ReturnTime']));
			$period=date("j M Y", strtotime($arr['DLA_Period']));
			if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
				$leaddate="-";
			else
				$leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
		echo"
		<tr>
			<td class='center'>$retdate</td>
			<td class='center'>$arr[TDRTOLAD_DocCode]</td>
			<td class='center'>$arr[DLA_Phase]</td>
			<td class='center'>$period</td>
			<td class='center'>$arr[DLA_Village]</td>
			<td class='center'>$arr[DLA_Block]</td>
			<td class='center'>$arr[DLA_Owner]</td>
			<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
			<td class='center'>$reldate</td>
			<td class='center'>$leaddate</td>
		</tr>";
		}
		echo"</table>";
		$jumdata++;
	}		
}
?>

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
