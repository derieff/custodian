<?PHP session_start(); ?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Pengeluaran Dokumen</title>
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
	$qperiod=((!$_POST['tStart'])&&(!$_POST['tEnd']))?"":"AND thrlold.THROLD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";
			
	$query = "SELECT DISTINCT thrlold.THROLD_ID, thrlold.THROLD_ReleaseCode, thrlold.THROLD_ReleaseDate, 
							  thlold.THLOLD_LoanCategoryID, thlold.THLOLD_LoanDate, drs.DRS_Description,
							  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
							  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
			  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc, M_DocumentRegistrationStatus drs,
				   TH_ReleaseOfLegalDocument thrlold, TD_ReleaseOfLegalDocument tdrlold,
				   TH_LoanOfLegalDocument thlold, TD_LoanOfLegalDocument tdlold
			  WHERE thlold.THLOLD_DocumentGroupID='$_POST[optDocumentGroup]'
			  $qcompany
			  $qarea
			  $qcategory
			  $qperiod
			  AND thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
			  AND thlold.THLOLD_ID=tdlold.TDLOLD_THLOLD_ID
			  AND thrlold.THROLD_Status=drs.DRS_Name
			  AND thlold.THLOLD_DocumentGroupID=dg.DocumentGroup_ID
			  AND thlold.THLOLD_CompanyID=c.Company_ID
			  AND tdlold.TDLOLD_DocumentCategoryID=dc.DocumentCategory_ID
			  AND thrlold.THROLD_Delete_Time IS NULL ";
}
elseif ($_POST[optDocumentGroup]=='3'){
	$qcompany=(!$_POST['optCompany'])?"":"AND thlolad.THLOLAD_CompanyID='$_POST[optCompany]'";
	$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
	$qperiod=((!$_POST['tStart'])&&(!$_POST['tEnd']))?"":"AND thrlolad.THRLOLAD_ReleaseDate BETWEEN '$txtStart' AND '$txtEnd'";
		
	$query = "SELECT DISTINCT thrlolad.THRLOLAD_ID, thrlolad.THRLOLAD_ReleaseCode, thrlolad.THRLOLAD_ReleaseDate, 
					 		  thlolad.THLOLAD_LoanCategoryID, thlolad.THLOLAD_LoanDate, drs.DRS_Description,
							  dg.DocumentGroup_ID, dg.DocumentGroup_Name, c.Company_ID, c.Company_Name, drs.DRS_Description
			  FROM M_DocumentGroup dg, M_Company c, M_DocumentRegistrationStatus drs,
		  		   TH_ReleaseOfLandAcquisitionDocument thrlolad, TD_ReleaseOfLandAcquisitionDocument tdrlolad,
		  		   TH_LoanOfLandAcquisitionDocument thlolad, TD_LoanOfLandAcquisitionDocument tdlolad
			  WHERE thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
			  AND thlolad.THLOLAD_ID=tdlolad.TDLOLAD_THLOLAD_ID
			  AND thrlolad.THRLOLAD_Status=drs.DRS_Name
			  $qcompany
			  $qarea
			  $qperiod
			  AND dg.DocumentGroup_ID='3'
			  AND thlolad.THLOLAD_CompanyID=c.Company_ID
			  AND thrlolad.THRLOLAD_Delete_Time IS NULL ";
}
$sql = mysql_query($query);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);
$jumdata=0;

$start=date('j M Y', strtotime($_POST['tStart']));
$end=date('j M Y', strtotime($_POST['tEnd']));
echo"
<div id='title'>Laporan Pengeluaran Dokumen</div>
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
		$d_query="SELECT dl.DL_DocCode, dt.DocumentType_Name, dl.DL_Instance, dl.DL_NoDoc, dl.DL_ExpDate,
						 lc.LoanCategory_Name, thlold.THLOLD_LoanCode, u.User_FullName,dp.Department_Name,
						 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime, thrlold.THROLD_ReleaseCode,
						 thlold.THLOLD_LoanDate, a.A_ApprovalDate
				  FROM M_DocumentLegal dl, M_DocumentType dt, TH_ReleaseOfLegalDocument thrlold,
					   TD_ReleaseOfLegalDocument tdrlold, TH_LoanOfLegalDocument thlold,
					   TD_LoanOfLegalDocument tdlold, M_LoanCategory lc, M_Approval a,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrlold.THROLD_ID='$h_arr[THROLD_ID]'
				  AND tdrlold.TDROLD_THROLD_ID=thrlold.THROLD_ID
				  AND tdrlold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
				  AND thrlold.THROLD_THLOLD_Code=thlold.THLOLD_LoanCode
				  AND thlold.THLOLD_ID=tdlold.TDLOLD_THLOLD_ID
				  AND tdlold.TDLOLD_DocCode=dl.DL_DocCode
				  AND thlold.THLOLD_UserID=u.User_ID
				  AND thlold.THLOLD_LoanCategoryID=lc.LoanCategory_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND dl.DL_TypeDocID=dt.DocumentType_ID
				  AND a.A_TransactionCode='$h_arr[THROLD_ReleaseCode]'
				  AND a.A_Step=(SELECT MAX(A_Step) 
				  				FROM M_Approval 
								WHERE A_TransactionCode='$h_arr[THROLD_ReleaseCode]')
				  AND thrlold.THROLD_Delete_Time IS NULL ";
		$d_sql=mysql_query($d_query);
					  
		if ($h_arr['THLOLD_LoanCategoryID']=="1"){
			$loandate=date("j M Y", strtotime($h_arr['THLOLD_LoanDate']));
	
			echo"
			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>Perusahaan</td>
				<td width='1%'>:</td>
				<td width='30%'><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
				<td width='19%'>Tanggal Permintaan</td>
				<td width='1%'>:</td>
				<td width='30%'>$loandate</td>
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
				<th width='200'>Kode Dokumen</th>
				<th width='200'>Nama Dokumen</th>
				<th width='200'>Instansi Terkait</th>
				<th width='200'>Nomor Dokumen</th>
				<th width='200'>Berlaku Sampai</th>	
				<th width='200'>Jenis Permintaan</th>	
				<th width='200'>Kode Permintaan</th>	
				<th width='200'>Nama Peminta</th>	
				<th width='200'>Departemen</th>	
				<th width='200'>Tanggal Pengeluaran</th>	
				<th width='200'>Lead Time</th>	
			</tr>";
		}
		else {
			$reldate=date("j M Y", strtotime($h_arr['THROLD_ReleaseDate']));
	
			echo"
			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>Perusahaan</td>
				<td width='1%'>:</td>
				<td width='30%'><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
				<td width='19%'>Tanggal Pengeluaran</td>
				<td width='1%'>:</td>
				<td width='30%'>$reldate</td>
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
				<th width='200'>Kode Dokumen</th>
				<th width='200'>Nama Dokumen</th>
				<th width='200'>Instansi Terkait</th>
				<th width='200'>Nomor Dokumen</th>
				<th width='200'>Berlaku Sampai</th>	
				<th width='200'>Kode Pengeluaran</th>	
				<th width='200'>Jenis Permintaan</th>	
				<th width='200'>Kode Permintaan</th>	
				<th width='200'>Tanggal Permintaan</th>	
				<th width='200'>Nama Peminta</th>	
				<th width='200'>Departemen</th>	
				<th width='200'>Tanggal Persetujuan</th>	
			</tr>";
		}
				
		while ($arr = mysql_fetch_array($d_sql)) {
		if ($h_arr['THLOLD_LoanCategoryID']=="1"){
			$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
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
				<td class='center'>$arr[DL_DocCode]</td>
				<td class='center'>$arr[DocumentType_Name]</td>
				<td class='center'>$arr[DL_Instance]</td>
				<td class='center'>$arr[DL_NoDoc]</td>
				<td class='center'>$expdate</td>
				<td class='center'>$arr[LoanCategory_Name]</td>
				<td class='center'>$arr[THLOLD_LoanCode]</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$reldate</td>
				<td class='center'>$leaddate</td>
			</tr>";
		}
		else {
			$loandate=date("j M Y", strtotime($arr['THLOLD_LoanDate']));
			$appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));
			if ($arr['DL_ExpDate']=="0000-00-00 00:00:00")
				$expdate="-";
			else
				$expdate=date("j M Y", strtotime($arr['DL_ExpDate']));
				
			echo"
			<tr>
				<td class='center'>$arr[DL_DocCode]</td>
				<td class='center'>$arr[DocumentType_Name]</td>
				<td class='center'>$arr[DL_Instance]</td>
				<td class='center'>$arr[DL_NoDoc]</td>
				<td class='center'>$expdate</td>
				<td class='center'>$arr[THROLD_ReleaseCode]</td>
				<td class='center'>$arr[LoanCategory_Name]</td>
				<td class='center'>$arr[THLOLD_LoanCode]</td>
				<td class='center'>$loandate</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$appdate</td>
			</tr>";
		}
	}
	$jumdata++;
	echo"</table>";
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
			$d_query="SELECT dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period, dla.DLA_Village, dla.DLA_Block,
						 	 dla.DLA_Owner, dla.DLA_DocDate, lc.LoanCategory_Name, thlolad.THLOLAD_LoanCode, 
							 u.User_FullName,dp.Department_Name,thrlolad.THRLOLAD_ReleaseDate, a.A_ApprovalDate,
							 tdrlolad.TDRLOLAD_LeadTime, thrlolad.THRLOLAD_ReleaseCode, thlolad.THLOLAD_LoanDate
					  FROM M_DocumentLandAcquisition dla,TH_ReleaseOfLandAcquisitionDocument thrlolad,
						   TD_ReleaseOfLandAcquisitionDocument tdrlolad, TH_LoanOfLandAcquisitionDocument thlolad,
						   TD_LoanOfLandAcquisitionDocument tdlolad, M_LoanCategory lc, M_Approval a,
						   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
					  WHERE thrlolad.THRLOLAD_ID='$h_arr[THRLOLAD_ID]'
					  AND tdrlolad.TDRLOLAD_THRLOLAD_ID=thrlolad.THRLOLAD_ID
					  AND tdrlolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
					  AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
					  AND thlolad.THLOLAD_ID=tdlolad.TDLOLAD_THLOLAD_ID
					  AND tdlolad.TDLOLAD_DocCode=dla.DLA_Code
					  AND thlolad.THLOLAD_UserID=u.User_ID
					  AND thlolad.THLOLAD_LoanCategoryID=lc.LoanCategory_ID
					  AND ddp.DDP_UserID=u.User_ID
					  AND ddp.DDP_DeptID=dp.Department_ID
					  AND a.A_TransactionCode='$h_arr[THRLOLAD_ReleaseCode]'
					  AND a.A_Step=(SELECT MAX(A_Step) 
					  				FROM M_Approval 
									WHERE A_TransactionCode='$h_arr[THRLOLAD_ReleaseCode]')
					  AND thrlolad.THRLOLAD_Delete_Time IS NULL ";
			$d_sql=mysql_query($d_query);
						  
			if ($h_arr['THLOLAD_LoanCategoryID']=="1"){
				$loandate=date("j M Y", strtotime($h_arr['THLOLAD_LoanDate']));
		
				echo"
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Perusahaan</td>
					<td width='1%'>:</td>
					<td width='30%'><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
					<td width='19%'>Tanggal Permintaan</td>
					<td width='1%'>:</td>
					<td width='30%'>$loandate</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				</table>
				
				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th width='200'>Kode Dokumen</th>
					<th width='50'>Tahap</th>
					<th width='200'>Periode</th>
					<th width='200'>Desa</th>
					<th width='200'>Blok</th>
					<th width='200'>Pemilik</th>
					<th width='200'>Tanggal Dokumen</th>
					<th width='200'>Kode Pengeluaran</th>	
					<th width='200'>Jenis Permintaan</th>	
					<th width='200'>Kode Permintaan</th>	
					<th width='200'>Nama Peminta</th>	
					<th width='200'>Departemen</th>	
					<th width='200'>Tanggal Pengeluaran</th>	
					<th width='200'>Lead Time</th>	
				</tr>";
			}
			else {
				$reldate=date("j M Y", strtotime($h_arr['THRLOLAD_ReleaseDate']));
		
				echo"
				<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
				<tr>
					<td width='19%'>Perusahaan</td>
					<td width='1%'>:</td>
					<td width='30%'><input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]</td>
					<td width='19%'>Tanggal Pengeluaran</td>
					<td width='1%'>:</td>
					<td width='30%'>$reldate</td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				</table>
				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th width='200'>Kode Dokumen</th>
					<th width='50'>Tahap</th>
					<th width='200'>Periode</th>
					<th width='200'>Desa</th>
					<th width='200'>Blok</th>
					<th width='200'>Pemilik</th>
					<th width='200'>Tanggal Dokumen</th>
					<th width='200'>Kode Pengeluaran</th>	
					<th width='200'>Jenis Permintaan</th>	
					<th width='200'>Kode Permintaan</th>	
					<th width='200'>Tanggal Permintaan</th>	
					<th width='200'>Nama Peminta</th>	
					<th width='200'>Departemen</th>	
					<th width='200'>Tanggal Persetujuan</th>	
				</tr>";
			}
				while ($arr = mysql_fetch_array($d_sql)) {
			if ($h_arr['THLOLAD_LoanCategoryID']=="1"){
				$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
				$period=date("j M Y", strtotime($arr['DLA_Period']));
				$docdate=date("j M Y", strtotime($arr['DLA_DocDate']));
				if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
					$leaddate="-";
				else
					$leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
						
				echo"
				<tr>
					<td class='center'>$arr[DLA_Code]</td>
					<td class='center'>$arr[DLA_Phase]</td>
					<td class='center'>$period</td>
					<td class='center'>$arr[DLA_Village]</td>
					<td class='center'>$arr[DLA_Block]</td>
					<td class='center'>$arr[DLA_Owner]</td>
					<td class='center'>$docdate</td>
					<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
					<td class='center'>$arr[LoanCategory_Name]</td>
					<td class='center'>$arr[THLOLAD_LoanCode]</td>
					<td class='center'>$arr[User_FullName]</td>
					<td class='center'>$arr[Department_Name]</td>
					<td class='center'>$reldate</td>
					<td class='center'>$leaddate</td>
				</tr>";
			}
			else {
				$loandate=date("j M Y", strtotime($arr['THLOLAD_LoanDate']));
				$appdate=date("j M Y", strtotime($arr['A_ApprovalDate']));
				$period=date("j M Y", strtotime($arr['DLA_Period']));
				$docdate=date("j M Y", strtotime($arr['DLA_DocDate']));
					
				echo"
				<tr>
					<td class='center'>$arr[DLA_Code]</td>
					<td class='center'>$arr[DLA_Phase]</td>
					<td class='center'>$period</td>
					<td class='center'>$arr[DLA_Village]</td>
					<td class='center'>$arr[DLA_Block]</td>
					<td class='center'>$arr[DLA_Owner]</td>
					<td class='center'>$docdate</td>
					<td class='center'>$arr[THRLOLAD_ReleaseCode]</td>
					<td class='center'>$arr[LoanCategory_Name]</td>
					<td class='center'>$arr[THLOLAD_LoanCode]</td>
					<td class='center'>$loandate</td>
					<td class='center'>$arr[User_FullName]</td>
					<td class='center'>$arr[Department_Name]</td>
					<td class='center'>$appdate</td>
				</tr>";
			}
			}
			$jumdata++;
			echo"</table>";
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
