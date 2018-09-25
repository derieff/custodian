<?PHP session_start(); ?>
<?PHP include ("./config/config_db.php"); ?>
<!DOCTYPE html PUBLIC "-//W3C//Dth XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/Dth/xhtml1-transitional.dth">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Laporan Pendaftaran Dokumen</title>
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
$qcompany=(!$_POST['optCompany'])?"":"AND thrgold.THROLD_CompanyID='$_POST[optCompany]'";
$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdrgold.TDROLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
$qperiod=((!$_POST['tStart'])&&(!$_POST['tEnd']))?"":"AND thrgold.THROLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

$query = "SELECT DISTINCT thrgold.THROLD_ID, thrgold.THROLD_RegistrationCode, thrgold.THROLD_RegistrationDate, 
						 		  u.User_FullName, dp.Department_Name, drs.DRS_Description,
						 		  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
						 		  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc, M_DocumentType dt, 
			  		   TH_RegistrationOfLegalDocument thrgold, TD_RegistrationOfLegalDocument tdrgold,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp,M_DocumentRegistrationStatus drs
				  WHERE thrgold.THROLD_DocumentGroupID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thrgold.THROLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thrgold.THROLD_CompanyID=c.Company_ID
				  AND thrgold.THROLD_Status=drs.DRS_Name
				  AND tdrgold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thrgold.THROLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgold.THROLD_Delete_Time IS NULL ";
}
if ($_POST[optDocumentGroup]=="3") {
$qcompany=(!$_POST['optCompany'])?"":"AND thrgolad.THRGOLAD_CompanyID='$_POST[optCompany]'";
$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrgolad.THRGOLAD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";

$query = "SELECT DISTINCT thrgolad.THRGOLAD_ID, thrgolad.THRGOLAD_RegistrationCode, drs.DRS_Description,
								  thrgolad.THRGOLAD_RegistrationDate, u.User_FullName, dp.Department_Name, 
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfLandAcquisitionDocument thrgolad, 
				  	   TD_RegistrationOfLandAcquisitionDocument tdrgolad,M_DocumentRegistrationStatus drs,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thrgolad.THRGOLAD_CompanyID=c.Company_ID
				  AND thrgolad.THRGOLAD_UserID=u.User_ID
				  AND thrgolad.THRGOLAD_RegStatus=drs.DRS_Name
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgolad.THRGOLAD_Delete_Time IS NULL ";
}
$sql = mysql_query($query);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);

echo"
<div id='title'>Laporan Pendaftaran Dokumen</div>
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

		$regdate=date("j M Y", strtotime($h_arr['THROLD_RegistrationDate']));

		echo"
			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>No. Pendaftaran</td>
				<td width='1%'>:</td>
				<td width='25%'>
					$h_arr[THROLD_RegistrationCode]
				</td>
				<td width='19%'>Tanggal Pendaftaran</td>
				<td width='1%'>:</td>
				<td width='30%'>
					$regdate
				</td>
			</tr>
			<tr>
				<td>Nama Pendaftar</td>
				<td>:</td>
				<td>
					$h_arr[User_FullName]
				</td>
				<td>Departemen</td>
				<td>:</td>
				<td>
					$h_arr[Department_Name]
				</td>
			</tr>
			<tr>
				<td>Perusahaan</td>
				<td>:</td>
				<td>
					<input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]
				</td>
				<td>Status Pendaftaran</td>
				<td>:</td>
				<td>$h_arr[DRS_Description]</td>
			</tr>
			<tr>
				<td>Grup Dokumen</td>
				<td>:</td>
				<td>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
				<td>Kategori Dokumen</td>
				<td>:</td>
				<td>
					<input type='hidden' name='optDocumentCategory' value=$h_arr[DocumentCategory_ID]>$h_arr[DocumentCategory_Name]
				</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Tanggal Terbit</th>
				<th>Berlaku Sampai</th>	
				<th>Keterangan 1</th>	
				<th>Keterangan 2</th>	
				<th>Keterangan 3</th>	
			</tr>
		";
		$d_query="SELECT dt.DocumentType_Name, tdrgold.TDROLD_Instance, tdrgold.TDROLD_DocumentNo, 
							 tdrgold.TDROLD_DatePublication,tdrgold.TDROLD_DateExpired,
							 di1.DocumentInformation1_Name, di2.DocumentInformation2_Name, 
							 tdrgold.TDROLD_DocumentInformation3
				  	  FROM M_DocumentType dt, TH_RegistrationOfLegalDocument thrgold,
					   	   TD_RegistrationOfLegalDocument tdrgold, M_DocumentInformation1 di1, 
					   	   M_DocumentInformation2 di2
				 	  WHERE thrgold.THROLD_ID='$h_arr[THROLD_ID]'
					  AND tdrgold.TDROLD_THROLD_ID=thrgold.THROLD_ID
					  AND tdrgold.TDROLD_DocumentTypeID=dt.DocumentType_ID
					  AND tdrgold.TDROLD_DocumentInformation1ID=di1.DocumentInformation1_ID
					  AND tdrgold.TDROLD_DocumentInformation2ID=di2.DocumentInformation2_ID
					  AND tdrgold.TDROLD_Delete_Time IS NULL ";
		$d_sql=mysql_query($d_query);
			  
		while ($arr = mysql_fetch_array($d_sql)) {
		$berlaku=date("j M Y", strtotime($arr['TDROLD_DatePublication']));
			if ($arr['TDROLD_DateExpired']=="0000-00-00 00:00:00")
				$expdate="-";
			else
				$expdate=date("j M Y", strtotime($arr['TDROLD_DateExpired']));
		echo"
			<tr>
				<td class='center'>$arr[DocumentType_Name]</td>
				<td class='center'>$arr[TDROLD_Instance]</td>
				<td class='center'>$arr[TDROLD_DocumentNo]</td>
				<td class='center'>$berlaku</td>
				<td class='center'>$expdate</td>
				<td class='center'>$arr[DocumentInformation1_Name]</td>
				<td class='center'>$arr[DocumentInformation2_Name]</td>
				<td class='center'>$arr[TDROLD_DocumentInformation3]</td>
			</tr>
		";
		}
		$jumdata ++;

		echo"
			</table>
		";
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

		$regdate=date("j M Y", strtotime($h_arr['THRGOLAD_RegistrationDate']));

		echo"
		<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
		<tr>
			<td width='19%'>No. Pendaftaran</td>
			<td width='1%'>:</td>
			<td width='25%'>
				$h_arr[THRGOLAD_RegistrationCode]
			</td>
			<td width='19%'>Tanggal Pendaftaran</td>
			<td width='1%'>:</td>
			<td width='30%'>
				$regdate
			</td>
		</tr>
		<tr>
			<td>Nama Pendaftar</td>
			<td>:</td>
			<td>
				$h_arr[User_FullName]
			</td>
			<td>Departemen</td>
			<td>:</td>
			<td>
				$h_arr[Department_Name]
			</td>
		</tr>
		<tr>
			<td width='24%'>Perusahaan</td>
			<td width='1%'>:</td>
			<td width='25%'>
				<input type='hidden' name='optCompany' value=$h_arr[Company_ID]>$h_arr[Company_Name]
			</td>
			<td>Status Pendaftaran</td>
				<td>:</td>
				<td>$h_arr[DRS_Description]</td>
		</tr>
		<tr>
			<td width='24%'>Grup Dokumen</td>
			<td width='1%'>:</td>
			<td width='25%'>
				<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
			</td>
			<td></td><td></td><td></td>
		</tr>
		</table>
		<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
		<tr>
			<th>Tahap GRL</th>
			<th>Periode GRL</th>
			<th>Desa</th>
			<th>Blok</th>
			<th>Pemilik</th>
			<th>Keterangan</th>	
		</tr>
		";
		$d_query="SELECT DISTINCT thrgolad.THRGOLAD_Phase, thrgolad.THRGOLAD_Period,tdrgolad.TDRGOLAD_Village,tdrgolad.TDRGOLAD_Block,
									  tdrgolad.TDRGOLAD_Owner,tdrgolad.TDRGOLAD_Information
				  	  FROM TH_RegistrationOfLandAcquisitionDocument thrgolad, TD_RegistrationOfLandAcquisitionDocument tdrgolad
				 	  WHERE thrgolad.THRGOLAD_ID='$h_arr[THRGOLAD_ID]'
					  AND tdrgolad.TDRGOLAD_THRGOLAD_ID=thrgolad.THRGOLAD_ID
					  AND tdrgolad.TDRGOLAD_Delete_Time IS NULL ";
		$d_sql=mysql_query($d_query);
	
		while ($arr = mysql_fetch_array($d_sql)) {
		$periode=date("j M Y", strtotime($arr['THRGOLAD_Period']));
		echo "
			<tr>
				<td class='center'>$arr[THRGOLAD_Phase]</td>
				<td class='center'>$periode</td>
				<td class='center'>$arr[TDRGOLAD_Village]</td>
				<td class='center'>$arr[TDRGOLAD_Block]</td>
				<td class='center'>$arr[TDRGOLAD_Owner]</td>
				<td class='center'>$arr[TDRGOLAD_Information]</td>
			</tr>";
			}
		$jumdata ++;
		echo"
			</table>
		";	
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