<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian	(Tahap 2)																				=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 07 Juni 2012																						=
= Update Terakhir	: 07 Juni 2012																						=
= Revisi			:																									=
=========================================================================================================================
*/
session_start(); 
?>
<title>Custodian System | Laporan Working Paper</title>
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

	var optCompany = document.getElementById('optCompany').selectedIndex;
	
		if(optCompany == 0) {
			alert("Perusahaan Belum Dipilih!");
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
	<form name='list' method='post' action='print-report-working-paper.php' target='_blank'>
	<table width='100%'>
	<tr>
		<td>PT</td>
		<td>:</td>
		<td>
			<select name='optCompany' id='optCompany'>
				<option value='0'>--- Pilih Perusahaan ---</option>
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
		<td width='10%'>
			<input name='listdocument' type='submit' value='Cari' class='button-small' onclick='return validateInput(this);'/>
		</td>
	</tr>
	<tr>
		<td width='9%'>Grup</td>
		<td width='1%'>:</td>
		<td width='80%'>
			<select name='optDocumentGroup' id='optDocumentGroup' onchange='javascript:showCategory();'>
				<option value='0'>--- Semua Grup Dokumen ---</option>";
				
			$g_query="SELECT * 
					  FROM M_DocumentGroup 
					  WHERE DocumentGroup_Delete_Time is NULL
					  AND DocumentGroup_ID<>'3'";
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

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->ShowWTopMenu();
}
?>