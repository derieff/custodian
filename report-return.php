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
<title>Custodian System | Laporan Pengembalian Dokumen</title>
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
		GroupID: $('#optDocumentGroup').val()
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
	<tr>
		<td>Periode</td>
		<td>:</td>
		<td>
			<input type='text' size='10' readonly='readonly' name='txtStart' id='txtStart' onclick=\"javascript:NewCssCal('txtStart', 'MMddyyyy');\"/> s/d <input type='text' size='10' readonly='readonly' name='txtEnd' id='txtEnd' onclick=\"javascript:NewCssCal('txtEnd', 'MMddyyyy');\"/>
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

$txtStart=date('Y-m-d H:i:s', strtotime($_POST['txtStart']));
$txtEnd=date('Y-m-d H:i:s', strtotime($_POST['txtEnd']));

	if ($_POST[optDocumentGroup]<>'3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND thlold.THLOLD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdlold.TDLOLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtold.TDRTOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";
		
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
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtold.TDRTOLD_ReturnCode LIMIT $offset, $dataPerPage";
	}
	elseif ($_POST[optDocumentGroup]=='3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND thlolad.THLOLAD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtolad.TDRTOLAD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";
		
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
				  AND ddp.DDP_DeptID=dp.Department_ID
				  ORDER BY tdrtolad.TDRTOLAD_ReturnCode LIMIT $offset, $dataPerPage";
	}
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$no = 1;

	if ($_POST[optDocumentGroup]<>'3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND thlold.THLOLD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdlold.TDLOLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtold.TDRTOLD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";
		
		if ($num==NULL) {
		$MainContent .="		
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>Nama Dokumen</th>
				<th>Instansi Terkait</th>
				<th>Nomor Dokumen</th>
				<th>Berlaku Sampai</th>	
				<th>Tanggal Pengeluaran</th>	
				<th>Lead Time</th>	
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='tStart' value='$_POST[txtStart]'>
					<input type='hidden' name='tEnd' value='$_POST[txtEnd]'>
					<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
					<input type='hidden' name='optArea' value='$_POST[optArea]'>
					<input type='hidden' name='optDocumentCategory' value='$_POST[optDocumentCategory]'>";
			while ($h_arr = mysql_fetch_array($sql)) {
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
							  
				$MainContent .="
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
					<td>$h_arr[Company_Name]</td>
					<td></td><td></td><td></td>
				</tr>
				<tr>
					<td>Grup Dokumen</td>
					<td>:</td>
					<td><input type='hidden' name='optDocumentGroup' value=$h_arr[DocumentGroup_ID]>$h_arr[DocumentGroup_Name]</td>
					<td>Kategori Dokumen</td>
					<td>:</td>
					<td>$h_arr[DocumentCategory_Name]</td>
				</tr>
				</table>
				
				<table width='100%' border='1' class='stripeMe'  cellpadding='0' cellspacing='0'>
				<tr>
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>Nama Dokumen</th>
					<th>Instansi Terkait</th>
					<th>Nomor Dokumen</th>
					<th>Berlaku Sampai</th>	
					<th>Kode Pengeluaran</th>	
					<th>Tanggal Pengeluaran</th>	
					<th>Lead Time</th>	
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
				$MainContent .="
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
				$MainContent .="
					</table>
				";
			}
			
		}
		$query1= "SELECT DISTINCT tdrtold.TDRTOLD_ReturnCode, thlold.THLOLD_LoanCode, thlold.THLOLD_LoanDate, 
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
				  AND ddp.DDP_DeptID=dp.Department_ID ";
	}

	elseif ($_POST[optDocumentGroup]=='3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND thlolad.THLOLAD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND tdrtolad.TDRTOLAD_ReturnTime BETWEEN '$txtStart' AND '$txtEnd'";
		
		if ($num==NULL) {
		$MainContent .="		
			<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th>Tanggal Pengembalian</th>
				<th>Kode Dokumen</th>
				<th>Tahap</th>
				<th>Periode</th>
				<th>Desa</th>
				<th>Blok</th>	
				<th>Pemilik</th>	
				<th>Kode Pengeluaran</th>	
				<th>Tanggal Pengeluaran</th>	
				<th>Lead Time</th>	
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-return.php' target='_blank'>
					<input type='hidden' name='tStart' value='$_POST[txtStart]'>
					<input type='hidden' name='tEnd' value='$_POST[txtEnd]'>
					<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
					<input type='hidden' name='optArea' value='$_POST[optArea]'>";
			while ($h_arr = mysql_fetch_array($sql)) {
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
				$MainContent .="
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
					<th>Tanggal Pengembalian</th>
					<th>Kode Dokumen</th>
					<th>Tahap</th>
					<th>Periode</th>
					<th>Desa</th>
					<th>Blok</th>	
					<th>Pemilik</th>	
					<th>Kode Pengeluaran</th>	
					<th>Tanggal Pengeluaran</th>	
					<th>Lead Time</th>	
				</tr>";

				while ($arr = mysql_fetch_array($d_sql)) {
					$reldate=date("j M Y", strtotime($arr['THRLOLAD_ReleaseDate']));
					$retdate=date("j M Y", strtotime($arr['TDRTOLAD_ReturnTime']));
					$period=date("j M Y", strtotime($arr['DLA_Period']));
					if ($arr['TDRLOLAD_LeadTime']=="0000-00-00 00:00:00")
						$leaddate="-";
					else
						$leaddate=date("j M Y", strtotime($arr['TDRLOLAD_LeadTime']));
				$MainContent .="
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
				$MainContent .="
					</table>
				";
			}
			
		}
		$query1= "SELECT DISTINCT tdrtolad.TDRTOLAD_ReturnCode, thlolad.THLOLAD_LoanCode, thlolad.THLOLAD_LoanDate, 
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
				  AND dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thlolad.THLOLAD_CompanyID=c.Company_ID
				  AND thlolad.THLOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID ";
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