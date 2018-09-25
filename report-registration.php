<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 05 Juni 2012																						=
= Update Terakhir	: 05 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Laporan Pendaftaran Dokumen</title>
<?PHP include ("./config/config_db.php"); ?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
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
			<select name='optDocumentGroup' id='optDocumentGroup' onchange='javascript:showCategory();'>
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
		$qcompany=(!$_POST['optCompany'])?"":"AND thrgold.THROLD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdrgold.TDROLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrgold.THROLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";
		
		$query = "SELECT DISTINCT thrgold.THROLD_ID, thrgold.THROLD_RegistrationCode, thrgold.THROLD_RegistrationDate, 
						 		  u.User_FullName, dp.Department_Name, drs.DRS_Description,
						 		  dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
						 		  dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc, M_DocumentType dt, 
			  		   TH_RegistrationOfLegalDocument thrgold, TD_RegistrationOfLegalDocument tdrgold,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp, M_DocumentRegistrationStatus drs
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
				  AND thrgold.THROLD_Delete_Time IS NULL 
				  ORDER BY thrgold.THROLD_ID LIMIT $offset, $dataPerPage";
	}
	elseif ($_POST[optDocumentGroup]=='3'){
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
				  AND thrgolad.THRGOLAD_Delete_Time IS NULL 
				  ORDER BY thrgolad.THRGOLAD_ID LIMIT $offset, $dataPerPage";
	}
$sql = mysql_query($query);
$num = mysql_num_rows($sql);
$no = 1;

	if ($_POST[optDocumentGroup]<>'3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND thrgold.THROLD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qcategory=(!$_POST['optDocumentCategory'])?"":"AND tdrgold.TDROLD_DocumentCategoryID='$_POST[optDocumentCategory]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrgold.THROLD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";
		
		if ($num==NULL) {
		$MainContent .="		
			<table width='100%' border='1' class='stripeMe'>
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
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-registration.php' target='_blank'>
					<input type='hidden' name='tStart' value='$_POST[txtStart]'>
					<input type='hidden' name='tEnd' value='$_POST[txtEnd]'>
					<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
					<input type='hidden' name='optArea' value='$_POST[optArea]'>
					<input type='hidden' name='optDocumentCategory' value='$_POST[optDocumentCategory]'>";
		while ($h_arr = mysql_fetch_array($sql)) {
		$regdate=date("j M Y", strtotime($h_arr['THROLD_RegistrationDate']));

		$MainContent .="
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
				<td>$h_arr[Company_Name]</td>
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
				<td>$h_arr[DocumentCategory_Name]</td>
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
		$MainContent .="
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
		$MainContent .="
			</table>
		";
		}
		}
		$query1= "SELECT DISTINCT thrgold.THROLD_ID, thrgold.THROLD_RegistrationCode, thrgold.THROLD_RegistrationDate, 
						 u.User_FullName, dp.Department_Name, 
						 dg.DocumentGroup_ID, dc.DocumentCategory_ID, c.Company_ID,
						 dg.DocumentGroup_Name, dc.DocumentCategory_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, M_DocumentCategory dc, M_DocumentType dt, 
			  		   TH_RegistrationOfLegalDocument thrgold, TD_RegistrationOfLegalDocument tdrgold,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE thrgold.THROLD_DocumentGroupID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qcategory
				  $qperiod
				  AND thrgold.THROLD_DocumentGroupID=dg.DocumentGroup_ID
				  AND thrgold.THROLD_CompanyID=c.Company_ID
				  AND tdrgold.TDROLD_DocumentCategoryID=dc.DocumentCategory_ID
				  AND thrgold.THROLD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgold.THROLD_Delete_Time IS NULL ";
	}

	elseif ($_POST[optDocumentGroup]=='3'){
		$qcompany=(!$_POST['optCompany'])?"":"AND thrgolad.THRGOLAD_CompanyID='$_POST[optCompany]'";
		$qarea=(!$_POST['optArea'])?"":"AND c.Company_ID_Area='$_POST[optArea]'";
		$qperiod=((!$_POST['txtStart'])&&(!$_POST['txtEnd']))?"":"AND thrgolad.THRGOLAD_RegistrationDate BETWEEN '$txtStart' AND '$txtEnd'";
		
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
				<th>Keterangan</th>	
			</tr>
			<tr>
				<td colspan='20' align='center'>Belum Ada Data</td>
			</tr>
			</table>
		";
		}
		if ($num<>NULL){
		$MainContent .="
			<form name='list' method='post' action='print-report-registration.php' target='_blank'>
					<input type='hidden' name='tStart' value='$_POST[txtStart]'>
					<input type='hidden' name='tEnd' value='$_POST[txtEnd]'>
					<input type='hidden' name='optCompany' value='$_POST[optCompany]'>
					<input type='hidden' name='optArea' value='$_POST[optArea]'>";
		while ($h_arr = mysql_fetch_array($sql)) {
		$regdate=date("j M Y", strtotime($h_arr['THRGOLAD_RegistrationDate']));

		$MainContent .="
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
				cNama Pendaftar</td>
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
				<td>$h_arr[Company_Name]</td>
				<td>Status Pendaftaran</td>
				<td>:</td>
				<td>$h_arr[DRS_Description]</td>
			</tr>
			<tr>
				<td>Grup Dokumen</td>
				<td>:</td>
				<td>$h_arr[DocumentGroup_Name]</td>
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
		$MainContent .="
			<tr>
				<td class='center'>$arr[THRGOLAD_Phase]</td>
				<td class='center'>$periode</td>
				<td class='center'>$arr[TDRGOLAD_Village]</td>
				<td class='center'>$arr[TDRGOLAD_Block]</td>
				<td class='center'>$arr[TDRGOLAD_Owner]</td>
				<td class='center'>$arr[TDRGOLAD_Information]</td>
			</tr>";
			}
		$MainContent .="
			</table>
		";	
		}
		}
		$query1= "SELECT DISTINCT thrgolad.THRGOLAD_ID, thrgolad.THRGOLAD_RegistrationCode, 
								  thrgolad.THRGOLAD_RegistrationDate, u.User_FullName, dp.Department_Name, 
						 		  dg.DocumentGroup_ID, c.Company_ID, dg.DocumentGroup_Name, c.Company_Name
				  FROM M_DocumentGroup dg, M_Company c, TH_RegistrationOfLandAcquisitionDocument thrgolad, 
				  	   TD_RegistrationOfLandAcquisitionDocument tdrgolad,
					   M_User u, M_Department dp,M_DivisionDepartmentPosition ddp
				  WHERE dg.DocumentGroup_ID='$_POST[optDocumentGroup]'
				  $qcompany
				  $qarea
				  $qperiod
				  AND thrgolad.THRGOLAD_CompanyID=c.Company_ID
				  AND thrgolad.THRGOLAD_UserID=u.User_ID
				  AND ddp.DDP_UserID=u.User_ID
				  AND ddp.DDP_DeptID=dp.Department_ID
				  AND thrgolad.THRGOLAD_Delete_Time IS NULL ";
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