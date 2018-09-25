<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Juni 2012																						=
= Update Terakhir	: 04 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start(); 
?>

<title>Custodian System | Laporan Dokumen Outstanding</title>
<?PHP include ("./config/config_db.php"); ?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
// VALIDASI INPUT UNTUK MENAMPILKAN LIST DOKUMEN
function validateInput(elem) {
	var returnValue;
	returnValue = true;							

	var optDocumentGroup = document.getElementById('optDocumentGroup').selectedIndex;
		if(optDocumentGroup == 0) {
			alert("Grup Dokumen Belum Dipilih!");
			returnValue = false;
		}

	return returnValue;
}

// JQUERY UNTUK MENAMPILKAN TIPE DOKUMEN DARI KATEGORI DOKUMEN YANG DIPILIH
function showCategory(){
	$.post("jQuery.DocumentCategory.php", {
		GroupID: $("#optDocumentGroup").val()
	}, function(response){
		setTimeout("finishAjax('optDocumentCategory', '"+escape(response)+"')", 400);
	});
}
function finishAjax(id, response){
  $('#'+id).html(unescape(response));
} 

// VALIDASI INPUT UNTUK PRINT
function validatePrint(elem) {
	var returnValue;
	returnValue = true;							

	var user1 = document.getElementById('user1').value;
	var user2 = document.getElementById('user2').value;
	var user3 = document.getElementById('user3').value;
	var user4 = document.getElementById('user4').value;
		
		if(user1.replace(" ", "") == "") {
			alert("Nama Pembuat Laporan Belum Ditentukan!");
			returnValue = false;
		}
		
		if(user2.replace(" ", "") == "") {
			alert("Nama Pemeriksa Laporan Belum Ditentukan!");
			returnValue = false;
		}
		
		if(user3.replace(" ", "") == "") {
			alert("Nama Pemberi Persetujuan 1 Belum Ditentukan!");
			returnValue = false;
		}
		
		if(user4.replace(" ", "") == "") {
			alert("Nama Pemberi Persetujuan 2 Belum Ditentukan!");
			returnValue = false;
		}
	return returnValue;
}
</script>
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {
	
require_once "./include/template.inc";
$page=new Template();

$ActionContent ="
	<form name='list' method='post' action='$PHP_SELF'>
	<table width='100%'>
	<tr>
		<td width='9%'>Area</td>
		<td width='1%'>:</td>
		<td width='80%'>
			<select name='optArea' id='optArea'>
			<option value='0'>--- Semua Area ---</option>";
			$query="SELECT distinct Company_ID_Area, Company_Area 
					FROM M_Company
					WHERE Company_Delete_Time is NULL
					ORDER BY Company_Area";
			$result=mysql_query($query);
	
			while ($object = mysql_fetch_object($result) ){
				$ActionContent .="<option value='".$object->Company_ID_Area."'>".$object->Company_Area."</option>";
			}
$ActionContent.="
			</select>
		</td>
		<td width='10%'>
			<input name='listdocument' type='submit' value='Cari' class='button-small' onclick='return validateInput(this);'/>
		</td>
	</tr>
	<tr>
		<td>PT</td>
		<td>:</td>
		<td>
			<select name='optCompany' id='optCompany'>
				<option value='0'>--- Semua Perusahaan ---</option>
";

			$c_query="SELECT * 
					  FROM M_Company
					  WHERE Company_Delete_Time is NULL";
			$c_sql=mysql_query($c_query);
	
			while ($c_arr = mysql_fetch_array($c_sql) ){
$ActionContent .="	
				<option value='$c_arr[Company_ID]'>$c_arr[Company_Name]</option>";
			}
$ActionContent .="	
			</select>
		</td>
	</tr>
	<tr>
		<td width='9%'>Grup</td>
		<td width='1%'>:</td>
		<td width='80%'>
			<select name='optDocumentGroup' id='optDocumentGroup' onchange='showCategory()'>
				<option value='0'>--- Pilih Grup Dokumen ---</option>";
				
			$g_query="SELECT * 
					  FROM M_DocumentGroup 
					  WHERE DocumentGroup_Delete_Time is NULL";
			$g_sql = mysql_query($g_query);
	
			while ($g_arr=mysql_fetch_array($g_sql) ){
$ActionContent .="
				<option value='$g_arr[DocumentGroup_ID]'>$g_arr[DocumentGroup_Name]</option>";
			}
$ActionContent .="	
			</select>
		</td>
	</tr>
	<tr>
		<td>Kategori</td>
		<td>:</td>
		<td>
			<select name='optDocumentCategory' id='optDocumentCategory'>
				<option value='0'>--- Semua Kategori Dokumen ---</option>";
$ActionContent .="	
			</select>
		</td>
	</tr>
	</table>
	</form>
";

/* ====== */
/* ACTION */
/* ====== */

	if(isset($_POST[listdocument])) {
		

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page'])) 
    $noPage = $_GET['page'];
else 
	$noPage = 1;
	
$offset = ($noPage - 1) * $dataPerPage;
	
	if ($_POST[optDocumentGroup]<>'3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND dl.DL_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qcategory=(!$_POST['optDocumentCategory'])?"":"AND dl.DL_CategoryDocID='$_POST[optDocumentCategory]'";
		
		$query = "SELECT DISTINCT dl.DL_DocCode, dt.DocumentType_Name, dl.DL_NoDoc, dl.DL_PubDate, thlold.THLOLD_LoanCode,
					   	 thlold.THLOLD_LoanDate, u.User_FullName, dp.Department_Name, thrlold.THROLD_ReleaseCode,
						 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime, dl.DL_Instance,
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
				  AND dl.DL_Status='4'
				  AND tdrlold.TDROLD_ReturnCode NOT IN (SELECT TDRTOLD_ReturnCode 
				  										FROM TD_ReturnOfLegalDocument
														WHERE TDRTOLD_Delete_Time IS NULL)
				  AND tdrlold.TDROLD_LeadTime<>'0000-00-00 00:00:00'
				  AND dl.DL_Delete_Time IS NULL 
				  ORDER BY dl.DL_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_POST[optDocumentGroup]=='3'){
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
				  AND dla.DLA_Delete_Time IS NULL
				  ORDER BY dla.DLA_ID LIMIT $offset, $dataPerPage";
	}
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$no = 1;
$sqldg = mysql_query($query);
$arr = mysql_fetch_array($sqldg);
$h_sql= mysql_query($query);
$h_arr = mysql_fetch_array($h_sql);

	if ($_POST[optDocumentGroup]<>'3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND dl.DL_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qcategory=(!$_POST['optDocumentCategory'])?"":"AND dl.DL_CategoryDocID='$_POST[optDocumentCategory]'";
		
		if ($num==NULL) {
		$MainContent .="		
			<table width='100%' border='1' class='stripeMe'>
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
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
			<input type='hidden' name='optArea' value='$_POST[optArea]'>
			<input type='hidden' name='optDocumentCategory' value='$_POST[optDocumentCategory]'>
			
			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td>Perusahaan</td>
				<td>:</td>
				<td>$h_arr[Company_Name]</td>
				<td></td><td></td>
			</tr>
			<tr>
				<td width='19%'>Grup Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
				<td width='19%'>Kategori Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>$h_arr[DocumentCategory_Name]</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
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
		";
		
			while ($arr = mysql_fetch_array($sql)) {
			$berlaku=date("j M Y", strtotime($arr['DL_PubDate']));
			$reqdate=date("j M Y", strtotime($arr['THLOLD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THROLD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDROLD_LeadTime']));
		$MainContent .="
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
		";
			}
		$MainContent .="
			</table>
		";	
		}
		$query1 = "SELECT dl.DL_DocCode, dt.DocumentType_Name, dl.DL_NoDoc, dl.DL_PubDate, thlold.THLOLD_LoanCode,
					   	 thlold.THLOLD_LoanDate, u.User_FullName, dp.Department_Name, thrlold.THROLD_ReleaseCode,
						 thrlold.THROLD_ReleaseDate, tdrlold.TDROLD_LeadTime, DL_Instance,
						 datediff(sysdate(), tdrlold.TDROLD_LeadTime) AS keterlambatan,
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
				  AND dl.DL_Status='4'
				  AND dl.DL_Delete_Time IS NULL ";
	}

	elseif ($_POST[optDocumentGroup]=='3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND dla.DLA_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		
		if ($num==NULL) {
		$MainContent .="
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Kode Dokumen</th>
				<th>Tahap GRL</th>
				<th>Periode GRL</th>
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
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-outstanding-document.php' target='_blank'>
			<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
			<input type='hidden' name='optArea' value='$_POST[optArea]'>
			
			<table width='100% cellpadding='0' cellspacing='0' style='border:none;'>
			<tr>
				<td width='19%'>Perusahaan</td>
				<td width='1%'>:</td>
				<td width='30%'>$h_arr[Company_Name]</td>
				<td width='19%'>Grup Dokumen</td>
				<td width='1%'>:</td>
				<td width='30%'>
					<input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]
				</td>
			</tr>
			</table>
			<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
			<tr>
				<th>Kode Dokumen</th>
				<th>Tahap GRL</th>
				<th>Periode GRL</th>
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
		";
		
			while ($arr = mysql_fetch_array($sql)) {
			$periode=date("j M Y", strtotime($arr['DLA_Period']));
			$reqdate=date("j M Y", strtotime($arr['THLOLAD_LoanDate']));
			$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
			$duedate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
		$MainContent .="
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
			</tr>";
			}
		$MainContent .="
			</table>
		";	
		}
		$query1= "SELECT dla.DLA_Code, dla.DLA_Phase, dla.DLA_Period, dla.DLA_Village, dla.DLA_Block, dla.DLA_Owner, 
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
				  $area
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
				  AND dla.DLA_Delete_Time IS NULL ";
	}
	if ($num<>NULL){
	$MainContent .="
				<table width='100%' border='1' cellpadding='0' cellspacing='0'>
				<tr>
					<td width='24%'>Nama Pembuat Laporan</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user1' id='user1'></td>					
				</tr>
				<tr>
					<td width='24%'>Nama Pemeriksa Laporan</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user2' id='user2'></td>					
				</tr>
				<tr>
					<td width='24%'>Nama Pemberi Persetujuan 1</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user3' id='user3'></td>					
				</tr>
				<tr>
					<td width='24%'>Nama Pemberi Persetujuan 2</td>
					<td width='1%'>:</td>
					<td width='65%'><input type='text' name='user4' id='user4'></td>					
				</tr>
			</table>
			<center><input name='print' type='submit' value='Cetak Laporan' class='button' onclick='return validatePrint(this);'/></center>
			</form>";
	}
$sql1 = mysql_query($query1);
$num1 = mysql_num_rows($sql1);

$jumData = $num1;
$jumPage = ceil($jumData/$dataPerPage);

$prev=$noPage-1;
$next=$noPage+1;

if ($noPage > 1) 
	$Pager.="<a href=$PHP_SELF?page=$prev>&lt;&lt; Prev</a> ";
for($p=1; $p<=$jumPage; $p++) {
    if ((($p>=$noPage-3) && ($p<=$noPage+3)) || ($p==1) || ($p== $jumPage)) {   
    	if (($showPage == 1) && ($p != 2))  
			$Pager.="..."; 
        if (($showPage != ($jumPage - 1)) && ($p == $jumPage))  
			$Pager.="...";
        if ($p == $noPage) 
			$Pager.="<b><u>$p</b></u> ";
        else 
			$Pager.="<a href=$_SERVER[PHP_SELF]?page=$p>$p</a> ";
        
		$showPage = $p;          
	}
}

if ($noPage < $jumPage) 
	$Pager .= "<a href=$PHP_SELF?page=$next>Next &gt;&gt;</a> ";
	}
	
$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>