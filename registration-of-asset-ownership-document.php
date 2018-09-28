<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.2.3																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 20 Agustus 2018																					=
= Update Terakhir	: -																						            =
= Revisi			: -																									=
=========================================================================================================================
*/
session_start();
?>
<title>Custodian System | Registrasi Dokumen Kepemilikan Aset</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.regdocao.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var optTHROAOD_CompanyID = document.getElementById('optTHROAOD_CompanyID').selectedIndex;

		if(optTHROAOD_CompanyID == 0) {
			alert("Perusahaan Belum Dipilih!");
			return false;
		}

	return true;
}

// VALIDASI TANGGAL
var dtCh= "/";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   }
   return this
}

function checkdate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strMonth=dtStr.substring(0,pos1)
	var strDay=dtStr.substring(pos1+1,pos2)
	var strYear=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("Format Tanggal : MM/DD/YYYY")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("Bulan Tidak Valid")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("Hari Tidak Valid")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("Masukkan 4 Digit Tahun Dari "+minYear+" Dan "+maxYear)
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("Tanggal Tidak Valid")
		return false
	}
return true
}

// VALIDASI INPUT BAGIAN DETAIL
function validateInputDetail(elem) {
	var jrow = document.getElementById('countRow').value;
	for (i = 1; i <= jrow; i++){
		var optTDROAOD_Employee_NIK = document.getElementById('optTDROAOD_Employee_NIK' + i).selectedIndex;
		var optTDROAOD_MK_ID = document.getElementById('optTDROAOD_MK_ID' + i).selectedIndex;
		var txtTDROAOD_Type = document.getElementById('txtTDROAOD_Type' + i).value;
		var txtTDROAOD_Jenis = document.getElementById('txtTDROAOD_Jenis' + i).value;
		var txtTDROAOD_NoPolisi = document.getElementById('txtTDROAOD_NoPolisi' + i).value;
		var txtTDROAOD_NoRangka = document.getElementById('txtTDROAOD_NoRangka' + i).value;
		var txtTDROAOD_NoMesin = document.getElementById('txtTDROAOD_NoMesin' + i).value;
		var txtTDROAOD_NoBPKB = document.getElementById('txtTDROAOD_NoBPKB' + i).value;
		var txtTDROAOD_STNK_StartDate = document.getElementById('txtTDROAOD_STNK_StartDate' + i).value;
		var txtTDROAOD_STNK_ExpiredDate = document.getElementById('txtTDROAOD_STNK_ExpiredDate' + i).value;
		var txtTDROAOD_Pajak_StartDate = document.getElementById('txtTDROAOD_Pajak_StartDate' + i).value;
		var txtTDROAOD_Pajak_ExpiredDate = document.getElementById('txtTDROAOD_Pajak_ExpiredDate' + i).value;
		var txtROAOD_Location = document.getElementById('txtROAOD_Location' + i).value;
		var optTDROAOD_Region = document.getElementById('optTDROAOD_Region' + i).selectedIndex;
		var txtTDROAOD_Keterangan = document.getElementById('txtTDROAOD_Keterangan' + i).value;
		var Date1 = new Date(txtTDROAOD_STNK_StartDate);
		var Date2 = new Date(txtTDROAOD_STNK_ExpiredDate);
		var Date3 = new Date(txtTDROAOD_Pajak_StartDate);
		var Date4 = new Date(txtTDROAOD_Pajak_ExpiredDate);

		if(optTDROAOD_Employee_NIK == 0) {
			alert("Nama Pemilik pada Baris ke-" + i + " Belum Dipilih!");
			return false;
		}
		if(optTDROAOD_MK_ID == 0) {
			alert("Merk Kendaraan pada Baris ke-" + i + " Belum Dipilih!");
			return false;
		}
		if (txtTDROAOD_Type.replace(" ", "") == "")  {
			alert("Tipe Kendaraan pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_Jenis.replace(" ", "") == "")  {
			alert("Jenis Kendaraan pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_NoPolisi.replace(" ", "") == "")  {
			alert("Nomor Polisi pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_NoRangka.replace(" ", "") == "")  {
			alert("Nomor Rangka pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_NoMesin.replace(" ", "") == "")  {
			alert("Nomor Mesin pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_NoBPKB.replace(" ", "") == "")  {
			alert("Nomor BPKB pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_STNK_StartDate.replace(" ", "") == "")  {
			alert("Tanggal Mulai Berlaku STNK pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_STNK_StartDate.replace(" ", "") != "")  {
			if (checkdate(txtTDROAOD_STNK_StartDate) == false) {
				return false;
			}
		}
		if (txtTDROAOD_STNK_ExpiredDate.replace(" ", "") != "")  {
			if (checkdate(txtTDROAOD_STNK_ExpiredDate) == false) {
				return false;
			}
			else {
				if (Date2 < Date1) {
				alert("Tanggal Habis Masa Berlaku STNK pada baris ke-" + i + " Lebih Kecil Daripada Tanggal Mulai Berlaku STNK!");
				return false;
				}
			}
		}
		if (txtTDROAOD_Pajak_StartDate.replace(" ", "") == "")  {
			alert("Tanggal Mulai Berlaku STNK pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if (txtTDROAOD_Pajak_StartDate.replace(" ", "") != "")  {
			if (checkdate(txtTDROAOD_DatePublication) == false) {
				return false;
			}
		}
		if (txtTDROAOD_Pajak_ExpiredDate.replace(" ", "") != "")  {
			if (checkdate(txtTDROAOD_STNK_ExpiredDate) == false) {
				return false;
			}
			else {
				if (Date4 < Date3) {
				alert("Tanggal Habis Masa Berlaku Pajak Kendaraan pada baris ke-" + i + " Lebih Kecil Daripada Tanggal Mulai Berlaku Pajak Kendaraan!");
				return false;
				}
			}
		}
		if (txtROAOD_Location.replace(" ", "") == "")  {
			alert("Lokasi pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
		if(optROAOD_Region == 0) {
			alert("Region pada baris ke-" + i + " Belum Dipilih!");
			return false;
		}
		if (txtTDROAOD_Keterangan.replace(" ", "") == "")  {
			alert("Keterangan pada baris ke-" + i + " Belum Terisi!");
			return false;
		}
	}
	return true;
}
</script>
<!-- Select2 -->
<link rel="stylesheet" href="css/select2.min.css">
</head>

<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
$page=new Template();
$decrp = new custodian_encryp;

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='add-doc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Kepemilikan Aset</th>
		</tr>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2, grup.DocumentGroup_Name,grup.DocumentGroup_ID
				  FROM M_User u
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_DocumentGroup grup
  					ON grup.DocumentGroup_ID='4'
				  WHERE u.User_ID='$_SESSION[User_ID]'";
		$field = mysql_fetch_array(mysql_query($query));

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input name='txtTHROAOD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input name='txtTHROAOD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input name='txtTHROAOD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input name='txtTHROAOD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>
		<tr>
			<td>Grup Dokumen</td>
			<td>
				<input name='txtTHROAOD_DocumentGroupID' type='hidden' value='$field[DocumentGroup_ID]'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>";

		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="
			<tr>
				<td>Perusahaan</td>
				<td>
					<select name='optTHROAOD_CompanyID' id='optTHROAOD_CompanyID' style='width:350px'>
						<option value='0'>--- Pilih Perusahan ---</option>";

					$query = "SELECT *
							  FROM M_Company
							  WHERE Company_Delete_Time is NULL
							  ORDER BY Company_Name ASC";
					$sql = mysql_query($query);

					while ($field = mysql_fetch_array($sql) ){
						$ActionContent .="
						<option value='$field[Company_ID]'>$field[Company_Name]</option>";
					}
			$ActionContent .="
					</select>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHROAOD_Information' id='txtTHROAOD_Information' cols='50' rows='2'></textarea></td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='addheader' type='submit' value='Simpan' class='button' onclick='return validateInputHeader(this);'/>
					<input name='cancel' type='submit' value='Batal' class='button'/>
				</th>
			</tr>";
		}else{
			if(!$_POST['cancel']){
				echo "<script>alert('Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.');</script>";
			}
			$ActionContent .="
			<tr>
				<td colspan='3' align='center' style='font-weight:bolder; color:red;'>
					Anda Tidak Dapat Melakukan Transaksi Ini karena Anda Belum Memiliki Atasan.<br>
					Mohon Hubungi Tim Custodian Untuk Verifikasi Atasan.
				</td>
			</tr>
			<tr>
				<th colspan=3>
					<input name='cancel' type='submit' value='OK' class='button'/>
				</th>
			</tr>";
		}
		$ActionContent .="
			</table>
			</form>";
	}

	//Menambah Detail Dokumen
	elseif($act=='adddetail')	{
		//Start of Koneksi from db_master
		include ("./config/config_db_master.php");
		$ActionContent .="<select id='Daftar_Employee' style='display:none;'>
			<option value='0'>--- Pilih Nama Pemilik ---</option>";
		$query5="SELECT Employee_NIK, Employee_FullName
			FROM db_master.M_Employee
			WHERE Employee_ResignDate IS NULL";
		$sql5 = mysql_query($query5);

		while ($field5=mysql_fetch_array($sql5)) {
			$ActionContent .="
			<option value='$field5[Employee_NIK]'>$field5[Employee_FullName]</option>";
		}
		$ActionContent .="</select>";

		$ActionContent .="<select id='Daftar_MerkKendaraan' style='display:none;'>
			<option value='0'>--- Pilih Merk Kendaraan ---</option>";
		$query6="SELECT *
				 FROM M_MerkKendaraan
				 WHERE MK_DeleteTime is NULL";
		$sql6 = mysql_query($query6);

		while ($field6=mysql_fetch_array($sql6)) {
			$ActionContent .="
			<option value='$field6[MK_ID]'>$field6[MK_Name]</option>";
		}
		$ActionContent .="</select>";
		include ("./config/config_db.php");
		//End of Koneksi from db_master

		$code=$_GET["id"];
		$query = "SELECT header.THROAOD_ID,
						 header.THROAOD_RegistrationCode,
						 header.THROAOD_RegistrationDate,
						 header.THROAOD_Information,
						 u.User_FullName as FullName,
						 ddp.DDP_DeptID as DeptID,
						 ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID,
						 dp.Department_Name as DeptName,
						 d.Division_Name as DivName,
						 p.Position_Name as PosName,
						 grup.DocumentGroup_Name,
						 grup.DocumentGroup_ID,
						 comp.Company_Name, comp.Company_ID, comp.Company_Area
				  FROM TH_RegistrationOfAssetOwnershipDocument header
				  LEFT JOIN M_User u
					ON u.User_ID=header.THROAOD_UserID
				  LEFT JOIN M_DivisionDepartmentPosition ddp
					ON u.User_ID=ddp.DDP_UserID
					AND ddp.DDP_Delete_Time is NULL
				  LEFT JOIN M_Division d
					ON ddp.DDP_DivID=d.Division_ID
				  LEFT JOIN M_Department dp
					ON ddp.DDP_DeptID=dp.Department_ID
				  LEFT JOIN M_Position p
					ON ddp.DDP_PosID=p.Position_ID
				  LEFT JOIN M_Company comp
					ON comp.Company_ID=header.THROAOD_CompanyID
				  LEFT JOIN M_DocumentGroup grup
					ON grup.DocumentGroup_ID=header.THROAOD_DocumentGroupID
				  WHERE header.THROAOD_RegistrationCode='$code'
				  AND header.THROAOD_Delete_Time IS NULL";
		$field = mysql_fetch_array(mysql_query($query));

		$DocGroup=$field['DocumentGroup_ID'];
		$regdate=strtotime($field['THROAOD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);

		$ActionContent .="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<tr>
			<th colspan=3>Registrasi Dokumen Kepemilikan Aset</th>
		</tr>
		<tr>
			<td width='30'>No Pendaftaran</td>
			<td width='70%'>
				<input name='txtTDROAOD_THROAOD_ID' type='hidden' value='$field[THROAOD_ID]'/>
				<input type='hidden' name='txtTDROAOD_THROAOD_RegistrationCode' value='$field[THROAOD_RegistrationCode]' style='width:350px;'/>
				$field[THROAOD_RegistrationCode]
			</td>
		</tr>
		<tr>
			<td>Tanggal Pendaftaran</td>
			<td>$fregdate</td>
		</tr>
		<tr>
			<td>Nama</td>
			<td>$field[FullName]</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>$field[DivName]</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>$field[DeptName]</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>$field[PosName]</td>
		</tr>
		<tr>
			<td>Grup Dokumen</td>
			<td>
				<input type='hidden' id='txtGrupID' name='txtGrupID' value='$field[DocumentGroup_ID]'/>
				$field[DocumentGroup_Name]
			</td>
		</tr>
		<tr>
			<td>Perusahaan</td>
			<td>
				<input type='hidden' id='txtCompID' name='txtCompID' value='$field[Company_ID]'/>
				<input type='hidden' id='txtCompArea' name='txtCompArea' value='$field[Company_Area]'/>
				$field[Company_Name]
			</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHROAOD_Information' id='txtTHROAOD_Information' cols='50' rows='2'>$field[THROAOD_Information]</textarea>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>

		<table width='2000' id='detail' class='stripeMe'>
		<tr>
			<th rowspan='2'>Nama Pemilik</th>
			<th rowspan='2'>Merk Kendaraan</th>
			<th rowspan='2'>Type</th>
			<th rowspan='2'>Jenis</th>
			<th rowspan='2'>No. Polisi</th>
			<th rowspan='2'>No. Rangka</th>
			<th rowspan='2'>No. Mesin</th>
			<th rowspan='2'>No. BPKB</th>
			<th colspan='2'>STNK</th>
			<th colspan='2'>Pajak Kendaraan</th>
			<th rowspan='2'>Lokasi (PT)</th>
			<th rowspan='2'>Region</th>
			<th rowspan='2'>Keterangan</th>
		</tr>
		<tr>
			<th>Start Date</th>
			<th>Expired Date</th>
			<th>Start Date</th>
			<th>Expired Date</th>
		</tr>
		<tr>
			<td>
				<select name='optTDROAOD_Employee_NIK1' id='optTDROAOD_Employee_NIK1' class='select2'>
					<option value='0'>--- Pilih Nama Pemilik ---</option>
				</select>
			</td>
			<td>
				<select name='optTDROAOD_MK_ID1' id='optTDROAOD_MK_ID1'>
					<option value='0'>--- Pilih Merk Kendaraan ---</option>
				</select>
			</td>
			<td>
				<input type='text' name='txtTDROAOD_Type1' id='txtTDROAOD_Type1'/>
			</td>
			<td>
				<input type='text' name='txtTDROAOD_Jenis1' id='txtTDROAOD_Jenis1'/>
				<!--<input type='text' size='10' readonly='readonly' name='txtTDROAOD_DatePublication1' id='txtTDROAOD_DatePublication1' onclick=\"javascript:NewCssCal('txtTDROAOD_DatePublication1', 'MMddyyyy');\"/>-->
			</td>
			<td>
				<input type='text' name='txtTDROAOD_NoPolisi1' id='txtTDROAOD_NoPolisi1'/>
				<!--<input type='text' size='10' readonly='readonly' name='txtTDROAOD_DateExpired1' id='txtTDROAOD_DateExpired1' onclick=\"javascript:NewCssCal('txtTDROAOD_DateExpired1', 'MMddyyyy');\"/>-->
			</td>
			<td>
				<input type='text' name='txtTDROAOD_NoRangka1' id='txtTDROAOD_NoRangka1'/>
			</td>
			<td>
				<input type='text' name='txtTDROAOD_NoMesin1' id='txtTDROAOD_NoMesin1'/>
			</td>
			<td>
				<input type='text' name='txtTDROAOD_NoBPKB1' id='txtTDROAOD_NoBPKB1'/>
				<!--<textarea name='txtTDROAOD_DocumentInformation31' id='txtTDROAOD_DocumentInformation31' cols='20' rows='1'></textarea>-->
			</td>
			<td>
				<input type='text' size='10' readonly='readonly' name='txtTDROAOD_STNK_StartDate1' id='txtTDROAOD_STNK_StartDate1' onclick=\"javascript:NewCssCal('txtTDROAOD_STNK_StartDate1', 'MMddyyyy');\"/>
			</td>
			<td>
				<input type='text' size='10' readonly='readonly' name='txtTDROAOD_STNK_ExpiredDate1' id='txtTDROAOD_STNK_ExpiredDate1' onclick=\"javascript:NewCssCal('txtTDROAOD_STNK_ExpiredDate1', 'MMddyyyy');\"/>
			</td>
			<td>
				<input type='text' size='10' readonly='readonly' name='txtTDROAOD_Pajak_StartDate1' id='txtTDROAOD_Pajak_StartDate1' onclick=\"javascript:NewCssCal('txtTDROAOD_Pajak_StartDate1', 'MMddyyyy');\"/>
			</td>
			<td>
				<input type='text' size='10' readonly='readonly' name='txtTDROAOD_Pajak_ExpiredDate1' id='txtTDROAOD_Pajak_ExpiredDate1' onclick=\"javascript:NewCssCal('txtTDROAOD_Pajak_ExpiredDate1', 'MMddyyyy');\"/>
			</td>
			<td>
				<input type='text' name='txtROAOD_Location1' id='txtROAOD_Location1' />
			</td>
			<td>
				<select name='optTDROAOD_Region1' id='optTDROAOD_Region1'>
					<option value=''>--- Pilih Region ---</option>
					<option value='KALTIM'>Kaltim</option>
					<option value='KALTENG'>Kalteng</option>
					<option value='KALBAR'>Kalbar</option>
					<option value='JAMBI'>Jambi</option>
					<option value='HO'>Head Office</option>
				</select>
			</td>
			<td>
				<textarea name='txtTDROAOD_Keterangan1' id='txtTDROAOD_Keterangan1' cols='20' rows='1'></textarea>
			</td>
		</tr>
		</table>

		<table width='2000'>
		<th  class='bg-white'>
			<input onclick='addRowToTable();' type='button' class='addrow'/>
			<input onclick='removeRowFromTable();' type='button' class='deleterow'/>
			<input type='hidden' value='1' id='countRow' name='countRow' />
		</th>
		</table>

		<table width='100%'>
		<tr>
			<td>";
			/* PROSES APPROVAL */
			$user=$_SESSION['User_ID'];

			$result = array();

			//Cek Jabatan Pengaju
            // $query="SELECT Employee_Grade
            //     FROM db_master.M_Employee
            //     WHERE Employee_NIK='".$user."'
            //      AND Employee_GradeCode
            //         IN ('0000000005', '06', '0000000003', '05', '04', '0000000004')";
            // $sql=mysql_query($query);
            // $obj=mysql_fetch_object($sql);
            // $jabatan=$obj->Employee_Grade;
            $approvers = array();

			for($sApp=1;$sApp<2;$sApp++) {
				//ATASAN LANGSUNG
				$query="SELECT User_SPV1,User_SPV2
						FROM M_User
						WHERE User_ID='$user'";
				$obj=mysql_fetch_object(mysql_query($query));
				$atasan1=$obj->User_SPV1;
				$atasan2=$obj->User_SPV2;

				if($atasan2){
					$sApp=3;
					$atasan=$atasan2;
				}else{
					$atasan=$atasan1;
				}

				$query="SELECT Employee_NIK
						FROM db_master.M_Employee
						WHERE Employee_NIK='".$atasan."'
						AND Employee_Position NOT LIKE '%SECTION%'
						AND Employee_Position NOT LIKE '%SUB DEP%'";
				$canApprove=mysql_num_rows(mysql_query($query));

				if($canApprove){
					// $ActionContent .="<input type='text' name='txtA_ApproverID[]' value='$atasan' readonly='true' class='readonly'/>";
				}else{
					$sApp=3;
				}

				$user=$atasan1;
				$result[] = $user;
			}

			/*$query="SELECT a.Approver_UserID
					FROM M_Approver a
					LEFT JOIN M_Role_Approver ra
						ON ra.RA_ID=a.Approver_RoleID
						AND a.Approver_Delete_Time is NULL
					WHERE ra.RA_Name LIKE '%custodian%'
					ORDER BY ra.RA_ID";
			$sql=mysql_query($query);*/

			$jenis = "13"; //Kepemilikan Aset - Semua Tipe Dokumen

			$query = "
				SELECT ma.Approver_UserID, rads.RADS_StepID, rads.RADS_RA_ID, ra.RA_Name
				FROM M_Role_ApproverDocStepStatus rads
				LEFT JOIN M_Role_Approver ra
					ON rads.RADS_RA_ID = ra.RA_ID
				LEFT JOIN M_Approver ma
					ON ra.RA_ID = ma.Approver_RoleID
				WHERE rads.RADS_DocID = '{$jenis}'
					AND rads.RADS_ProsesID = '1'
					AND ma.Approver_Delete_Time IS NULL
					AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$field['Company_Area']}')
					ORDER BY rads.RADS_StepID
			";
			$sql=mysql_query($query);

			$output = array();
			$approve_dept_head_custodian = 0;
			while($obj=mysql_fetch_object($sql)){
				$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				// if($obj->RA_Name=="Section Head Custodian" && $obj->Approver_UserID == 0){
				// 	$approve_dept_head_custodian = 1;
				// }
				// if($obj->RA_Name=="Custodian Head" && $approve_dept_head_custodian == 1){
				// 	$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				// 	//Perlu Approval Dept Head Custodian karena tidak ada Section Head Custodian
				// }elseif($obj->RA_Name=="Custodian Head" && $approve_dept_head_custodian == 0){
				// 	//Tidak perlu Approval Dept Head Custodian karena ada Section Head Custodian
				// }else{
				// 	$output[$obj->RADS_StepID] = $obj->Approver_UserID;
				// }
				//$ActionContent .="
				//<input type='text' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}
			// print_r ($output);
			// AKHIR PROSES APPROVAL

			$i = 0;
			$newArray = array();
			foreach ($output as $k => $v) {
				if ($v == '0') { $newArray[$k] = $result[$i]; $i++; } else { $newArray[$k] = $v; }
			}

			$key = array_search('', $newArray);
			if (false !== $key) unset($newArray[$key]);

			foreach ($newArray as $key => $value) {
				$ActionContent .= "<input type='text' name='txtA_ApproverID[$key]' value='$value' readonly='true' class='readonly' />";
			}
			/*while($obj=mysql_fetch_object($sql)){
				$ActionContent .="
				<input type='hidden' name='txtA_ApproverID[]' value='".$obj->Approver_UserID."' readonly='true' class='readonly'/>";
			}*/
			// AKHIR PROSES APPROVAL

		$ActionContent .="
			</td>
		</tr>
		<tr>
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>
		</tr>
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_registration_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=registration-of-asset-ownership-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT DISTINCT THROAOD.THROAOD_ID, THROAOD.THROAOD_RegistrationCode, THROAOD.THROAOD_RegistrationDate, u.User_FullName,
 		  		 c.Company_Name,drs.DRS_Description,THROAOD.THROAOD_Status
		  FROM TH_RegistrationOfAssetOwnershipDocument THROAOD, M_User u, M_Company c,M_DocumentRegistrationStatus drs
		  WHERE THROAOD.THROAOD_Delete_Time is NULL
		  AND THROAOD.THROAOD_CompanyID=c.Company_ID
		  AND THROAOD.THROAOD_UserID=u.User_ID
		  AND u.User_ID='$_SESSION[User_ID]'
		  AND THROAOD.THROAOD_Status=drs.DRS_Name
		  ORDER BY THROAOD.THROAOD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='25%'>Kode Pendaftaran</th>
	<th width='15%'>Tanggal Pendaftaran</th>
	<th width='20%'>Nama Pendaftar</th>
	<th width='20%'>Nama Perusahaan</th>
	<th width='15%'>Status</th>
	<th width='5%'></th>
</tr>";

if ($num==NULL) {
$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)){
		$regdate=strtotime($field['THROAOD_RegistrationDate']);
		$fregdate=date("j M Y", $regdate);
		$resend=($field['THROAOD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[1]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-registration-asset-ownership-document.php?id=".$decrp->encrypt($field[0])."' class='underline'>$field[1]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[3]</td>
			<td class='center'>$field[4]</td>
			<td class='center'>$field[5]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT THROAOD.THROAOD_ID, THROAOD.THROAOD_RegistrationCode, THROAOD.THROAOD_RegistrationDate, u.User_FullName,
		   		  c.Company_Name, THROAOD.THROAOD_Status
		   FROM TH_RegistrationOfAssetOwnershipDocument THROAOD, M_User u, M_Company c
		   WHERE THROAOD.THROAOD_Delete_Time is NULL
		   AND THROAOD.THROAOD_CompanyID=c.Company_ID
		   AND THROAOD.THROAOD_UserID=u.User_ID
		   AND u.User_ID='$_SESSION[User_ID]'";
$num1 = mysql_num_rows(mysql_query($query1));

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

/* ACTIONS */
if(isset($_POST['cancel'])) {
	echo "<meta http-equiv='refresh' content='0; url=registration-of-asset-ownership-document.php'>";
}

elseif(isset($_POST['canceldetail'])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_RegistrationOfAssetOwnershipDocument THROAOD
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       THROAOD.THROAOD_Delete_UserID='$_SESSION[User_ID]',THROAOD.THROAOD_Delete_Time=sysdate(),
				   THROAOD.THROAOD_Update_UserID='$_SESSION[User_ID]',THROAOD.THROAOD_Update_Time=sysdate()
			   WHERE THROAOD.THROAOD_ID='$_POST[txtTDROAOD_THROAOD_ID]'
			   AND THROAOD.THROAOD_RegistrationCode=ct.CT_Code
			   AND THROAOD.THROAOD_Delete_Time IS NULL";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=registration-of-asset-ownership-document.php'>";
	}
}

elseif(isset($_POST['addheader'])) {
	$regyear=date("Y");
	$rmonth=date("n");

	// Mengubah Bulan ke Romawi
	switch ($rmonth)	{
		case 1: $regmonth="I"; break;
		case 2: $regmonth="II"; break;
		case 3: $regmonth="III"; break;
		case 4: $regmonth="IV"; break;
		case 5: $regmonth="V"; break;
		case 6: $regmonth="VI"; break;
		case 7: $regmonth="VII"; break;
		case 8: $regmonth="VIII"; break;
		case 9: $regmonth="IX"; break;
		case 10: $regmonth="X"; break;
		case 11: $regmonth="XI"; break;
		case 12: $regmonth="XII"; break;
	}

	// Cari Kode Perusahaan
	$query = "SELECT *
			  FROM M_Company
			  WHERE Company_ID='$_POST[optTHROAOD_CompanyID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$Company_Code=$field['Company_Code'];

	// Cari Kode Dokumen Grup
	$query = "SELECT *
			  FROM M_DocumentGroup
			  WHERE DocumentGroup_ID ='$_POST[txtTHROAOD_DocumentGroupID]'";
	$field = mysql_fetch_array(mysql_query($query));
	$DocumentGroup_Code=$field['DocumentGroup_Code'];

	// Cari No Registrasi Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='INS'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$field = mysql_fetch_array(mysql_query($query));

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Registrasi Dokumen
	$CT_Code="$newnum/INS/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode registrasi dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','INS','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
			  	   '$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$info = str_replace("<br>", "\n", $_POST['txtTHROAOD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_RegistrationOfAssetOwnershipDocument
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[optTHROAOD_CompanyID]',
				        '$info','$_POST[txtTHROAOD_DocumentGroupID]',
						'0',NULL,'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=registration-of-asset-ownership-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST['adddetail'])) {
	$A_TransactionCode = $_POST['txtTDROAOD_THROAOD_RegistrationCode'];
	$A_ApproverID=$_SESSION['User_ID'];

	$count=$_POST['countRow'];
	$txtTHROAOD_Information=str_replace("<br>", "\n", $_POST['txtTHROAOD_Information']);

	for ($i=1 ; $i<=$count ; $i++) {
		$optTDROAOD_Employee_NIK=$_POST["optTDROAOD_Employee_NIK".$i];
		$optTDROAOD_MK_ID=$_POST["optTDROAOD_MK_ID".$i];
		$txtTDROAOD_Type=$_POST["txtTDROAOD_Type".$i];
		$txtTDROAOD_Jenis=$_POST["txtTDROAOD_Jenis".$i];
		$txtTDROAOD_NoPolisi=$_POST["txtTDROAOD_NoPolisi".$i];
		$txtTDROAOD_NoRangka=$_POST["txtTDROAOD_NoRangka".$i];
		$txtTDROAOD_NoMesin=$_POST["txtTDROAOD_NoMesin".$i];
		$txtTDROAOD_NoBPKB=$_POST["txtTDROAOD_NoBPKB".$i];
		$txtTDROAOD_STNK_StartDate=$_POST["txtTDROAOD_STNK_StartDate".$i];
		$txtTDROAOD_STNK_StartDate=date('Y-m-d H:i:s', strtotime($txtTDROAOD_STNK_StartDate));
		$txtTDROAOD_STNK_ExpiredDate=$_POST["txtTDROAOD_STNK_ExpiredDate".$i];
		$txtTDROAOD_STNK_ExpiredDate=date('Y-m-d H:i:s', strtotime($txtTDROAOD_STNK_ExpiredDate));
		$txtTDROAOD_Pajak_StartDate=$_POST["txtTDROAOD_Pajak_StartDate".$i];
		$txtTDROAOD_Pajak_StartDate=date('Y-m-d H:i:s', strtotime($txtTDROAOD_Pajak_StartDate));
		$txtTDROAOD_Pajak_ExpiredDate=$_POST["txtTDROAOD_Pajak_ExpiredDate".$i];
		$txtTDROAOD_Pajak_ExpiredDate=date('Y-m-d H:i:s', strtotime($txtTDROAOD_Pajak_ExpiredDate));
		$txtROAOD_Location=$_POST["txtROAOD_Location".$i];
		$optTDROAOD_Region=$_POST["optTDROAOD_Region".$i];
		if 	(strstr($txtTDROAOD_STNK_ExpiredDate, ' ', true)=="1970-01-01"){
			$txtTDROAOD_STNK_ExpiredDate=NULL;
		}
		if 	(strstr($txtTDROAOD_Pajak_ExpiredDate, ' ', true)=="1970-01-01"){
			$txtTDROAOD_Pajak_ExpiredDate=NULL;
		}
		$txtTDROAOD_Keterangan=str_replace("<br>", "\n", $_POST["txtTDROAOD_Keterangan".$i]);

		$sql1= "INSERT INTO TD_RegistrationOfAssetOwnershipDocument
				VALUES (NULL,'$_POST[txtTDROAOD_THROAOD_ID]', '$optTDROAOD_Employee_NIK',
						'$optTDROAOD_MK_ID', '$txtTDROAOD_Type', '$txtTDROAOD_Jenis',
						'$txtTDROAOD_NoPolisi', '$txtTDROAOD_NoRangka', '$txtTDROAOD_NoMesin',
						'$txtTDROAOD_NoBPKB', '$txtTDROAOD_STNK_StartDate', '$txtTDROAOD_STNK_ExpiredDate',
						'$txtTDROAOD_Pajak_StartDate', '$txtTDROAOD_Pajak_ExpiredDate', '$txtROAOD_Location',
						'$optTDROAOD_Region', '$txtTDROAOD_Keterangan',
						'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		$mysqli->query($sql1);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	foreach ($txtA_ApproverID as $k=>$v) {
		if ($txtA_ApproverID[$k] <> NULL) {
			if ($txtA_ApproverID[$k] <> $_SESSION['User_ID']) {
				$appbefquery = "SELECT *
						FROM M_Approval
						WHERE A_TransactionCode='{$_POST['txtTDROAOD_THROAOD_RegistrationCode']}'
						AND A_ApproverID = '{$txtA_ApproverID[$k]}'";
				$numappbef = mysql_fetch_row(mysql_query($appbefquery));

				if ($numappbef == '0') {
					$step=$step+1;
					$sql2 = "INSERT INTO M_Approval
							VALUES (NULL, '$A_TransactionCode', '$txtA_ApproverID[$k]', '$k', '1', NULL, '$A_ApproverID', sysdate(),
							'$_SESSION[User_ID]', sysdate(), NULL, NULL)";
					$mysqli->query($sql2);
					$sa_query = "SELECT * FROM M_Approval
								WHERE A_TransactionCode='$A_TransactionCode' AND A_ApproverID='$txtA_ApproverID[$k]'
								AND A_Delete_Time IS NULL";
					$sa_arr = mysql_fetch_array(mysql_query($sa_query));
					$ARC_AID = $sa_arr['A_ID'];
					$str = rand(1,100);
					$RandomCode = crypt('T4pagri'.$str);
					$iSQL="INSERT INTO L_ApprovalRandomCode VALUES ('$ARC_AID', '$RandomCode')";
					$mysqli->query($iSQL);
				}
			}
		}
	}

//
	// MENCARI JUMLAH APPROVAL
	$query = "SELECT MAX(A_Step) AS jStep
				FROM M_Approval
				WHERE A_TransactionCode='$A_TransactionCode'";
	$arr = mysql_fetch_array(mysql_query($query));
	$jStep=$arr['jStep'];

	$jenis = "13"; //Kepemilikan Aset - Semua Tipe Dokumen

	for ($i=1; $i<=$jStep; $i++) {
		$query ="
			SELECT rads.RADS_StatusID, ma.A_ApproverID
			FROM M_Approval ma
			JOIN M_Role_ApproverDocStepStatus rads
				ON ma.A_Step = rads.RADS_StepID
			LEFT JOIN M_Role_Approver ra
				ON rads.RADS_RA_ID = ra.RA_ID
			WHERE ma.A_Step = '{$i}'
				AND (ra.RA_Name NOT LIKE '%CEO%' OR ra.RA_Name = 'CEO - {$_POST['txtCompArea']}')
				AND ma.A_TransactionCode = '{$A_TransactionCode}'
				AND rads.RADS_DocID = '{$jenis}'
				AND rads.RADS_ProsesID = '1'
		";
		$result = mysql_fetch_array(mysql_query($query));

		if ($result['RADS_StatusID'] == '1') {
			$query = "UPDATE M_Approval
					SET A_Status = '2', A_Update_UserID = '$A_ApproverID', A_Update_Time = sysdate()
					WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_registration_doc($A_TransactionCode);
			}
			break;
		} else if ($result['RADS_StatusID'] == '2') {
			$query = "UPDATE M_Approval
					SET A_Status = '3', A_Update_UserID = '$A_ApproverID', A_ApprovalDate = sysdate(), A_Update_Time = sysdate()
					WHERE A_TransactionCode = '$A_TransactionCode' AND A_Step = '$i'";
			if ($sql = mysql_query($query)) {
				mail_notif_registration_doc($A_TransactionCode, $result['A_ApproverID'], 3);
			}
		}
	}

	/*$jumlah=count($txtA_ApproverID);
	$step=0;
	for($i=0;$i<$jumlah;$i++){
		if($txtA_ApproverID[$i]<>NULL){
			if ($txtA_ApproverID[$i]<>$_SESSION['User_ID']){
				$appbefquery="SELECT *
							  FROM M_Approval
							  WHERE A_TransactionCode='$_POST[txtTDROAOD_THROAOD_RegistrationCode]'
							  AND A_ApproverID='$txtA_ApproverID[$i]'";
				$appbefsql = mysql_query($appbefquery);
				$numappbef=mysql_fetch_row($appbefsql);

				if ($numappbef==0) {
					$sc_query="SELECT *
							   FROM M_Approver a, M_Role_Approver ra
							   WHERE a.Approver_UserID='$txtA_ApproverID[$i]'
							   AND a.Approver_Delete_Time is NULL
							   AND ra.RA_ID=a.Approver_RoleID
							   AND ra.RA_Name LIKE '%Custodian%'";
					$sc_sql=mysql_query($sc_query);
					$sc_app=mysql_num_rows($sc_sql);
					if ($step==0 || $sc_app==1) {
						$step=$step+1;
						if ($step == '1') {
							$sql2 = "INSERT INTO M_Approval
									VALUES (NULL, '$_POST[txtTDROAOD_THROAOD_RegistrationCode]', '$txtA_ApproverID[$i]',
											'$step', '3', sysdate(), '$_SESSION[User_ID]', sysdate(), '$_SESSION[User_ID]',
											sysdate(), NULL, NULL)";
						} else {
							$sql2= "INSERT INTO M_Approval
									VALUES (NULL,'$_POST[txtTDROAOD_THROAOD_RegistrationCode]', '$txtA_ApproverID[$i]',
									        '$step', '1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',
											sysdate(),NULL,NULL)";
						}
						$mysqli->query($sql2);
						$sa_query="SELECT *
								   FROM M_Approval
								   WHERE A_TransactionCode='$_POST[txtTDROAOD_THROAOD_RegistrationCode]'
								   AND A_ApproverID='$txtA_ApproverID[$i]'
								   AND A_Delete_Time IS NULL";
						$sa_sql=mysql_query($sa_query);
						$sa_arr=mysql_fetch_array($sa_sql);
						$ARC_AID=$sa_arr['A_ID'];
						$str=rand(1,100);
						$RandomCode=crypt('T4pagri'.$str);
						$iSQL="INSERT INTO L_ApprovalRandomCode
							   VALUES ('$ARC_AID','$RandomCode')";
						$mysqli->query($iSQL);
					}
				}
			}
		}
	}*/
	/*$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$_SESSION[User_ID]',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDROAOD_THROAOD_RegistrationCode]'
			AND A_Step='2'";*/

	$sql4= "UPDATE TH_RegistrationOfAssetOwnershipDocument
			SET THROAOD_Status='waiting', THROAOD_Information='$txtTHROAOD_Information',
			THROAOD_Update_UserID='$_SESSION[User_ID]',THROAOD_Update_Time=sysdate()
			WHERE THROAOD_RegistrationCode='$_POST[txtTDROAOD_THROAOD_RegistrationCode]'
			AND THROAOD_Delete_Time IS NULL";
	$mysqli->query($sql4);

	/*if($mysqli->query($sql4)) {
		// Kirim Email ke Approver 1
		mail_registration_doc($_POST['txtTDROAOD_THROAOD_RegistrationCode']);
		mail_notif_registration_doc($_POST['txtTDROAOD_THROAOD_RegistrationCode'], $txtA_ApproverID[0], 3);

	}*/

	echo "<meta http-equiv='refresh' content='0; url=registration-of-asset-ownership-document.php'>";
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>
<!-- Select2 -->
<script src="js/select2.full.min.js"></script>

<script language="JavaScript" type="text/JavaScript">
document.getElementById('optTDROAOD_Employee_NIK1').innerHTML = $('#Daftar_Employee').html();
document.getElementById('optTDROAOD_MK_ID1').innerHTML = $('#Daftar_MerkKendaraan').html();

// UNTUK DATETIME PICKER
function getDatePublication(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROAOD_DatePublication"+i, "txtTDROAOD_DatePublication"+i, "%m/%d/%Y");
	}

}
function getDateExpired(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

	for (i=1;i<=rows;i++){
			 cal.manageFields("txtTDROAOD_DateExpired"+i, "txtTDROAOD_DateExpired"+i, "%m/%d/%Y");
	}

}

// TAMBAH BARIS
function addRowToTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	document.getElementById('countRow').value = (document.getElementById('countRow').value*1) + 1;
	var iteration = lastRow;
	// alert(lastrow);
	var row = tbl.insertRow(lastRow);

	// Nama Pemilik
	var cell0 = row.insertCell(0);
	var sel = document.createElement('select');
	sel.name = 'optTDROAOD_Employee_NIK' + iteration;
	sel.id = 'optTDROAOD_Employee_NIK' + iteration;
	sel.setAttribute("class", "select2");
	sel.innerHTML = $('#Daftar_Employee').html();
	//sel.setAttribute("onchange","javascript:showType(this.value);");
	// sel.onchange=function(){ showType(this.value);  };
	cell0.appendChild(sel);

	// Merk Kendaraan
	var cell1 = row.insertCell(1);
	var sel = document.createElement('select');
	sel.name = 'optTDROAOD_MK_ID' + iteration;
	sel.id = 'optTDROAOD_MK_ID' + iteration;
	sel.options[0] = new Option('--- Pilih Merk Kendaraan ---', '0');
	<?PHP
		include ("./config/config_db_master.php");
		$query8="SELECT *
				 FROM M_MerkKendaraan
				 WHERE MK_DeleteTime is NULL";
		$sql7 = mysql_query($query6);
		$i = 1;

		while ($field7=mysql_fetch_array($sql7)) {
			$s_tmp2 = "sel.options[$i] = new Option('$field7[1]','$field7[0]');";
			echo $s_tmp2;
			$i++;
		}
		include ("./config/config_db.php");
	?>
	cell1.appendChild(sel);

	// Tipe Kendaraan
	var cell2 = row.insertCell(2);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROAOD_Type' + iteration;
	el.id = 'txtTDROAOD_Type' + iteration;
	cell2.appendChild(el);

	// Jenis Kendaraan
	var cell3 = row.insertCell(3);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROAOD_Jenis' + iteration;
	el.id = 'txtTDROAOD_Jenis' + iteration;
	cell3.appendChild(el);

	// Nomor Polisi
	var cell4 = row.insertCell(4);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROAOD_NoPolisi' + iteration;
	el.id = 'txtTDROAOD_NoPolisi' + iteration;
	cell4.appendChild(el);

	// Nomor Rangka
	var cell5 = row.insertCell(5);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROAOD_NoRangka' + iteration;
	el.id = 'txtTDROAOD_NoRangka' + iteration;
	cell5.appendChild(el);

	// Nomor Mesin
	var cell6 = row.insertCell(6);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROAOD_NoMesin' + iteration;
	el.id = 'txtTDROAOD_NoMesin' + iteration;
	cell6.appendChild(el);

	// Nomor BPKB
	var cell7 = row.insertCell(7);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtTDROAOD_NoBPKB' + iteration;
	el.id = 'txtTDROAOD_NoBPKB' + iteration;
	cell7.appendChild(el);

	// STNK - Start Date
	var cell8 = row.insertCell(8);
	var elPubDate = document.createElement('input');
	elPubDate.type = 'text';
	elPubDate.name = 'txtTDROAOD_STNK_StartDate' + iteration;
	elPubDate.id = 'txtTDROAOD_STNK_StartDate' + iteration;
	elPubDate.size = '10';
	//el.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	elPubDate.onclick=function(){ NewCssCal(elPubDate.id, 'MMddyyyy');  };
	cell8.appendChild(elPubDate);

	// STNK - Expired Date
	var cell9 = row.insertCell(9);
	var elExpDate = document.createElement('input');
	elExpDate.type = 'text';
	elExpDate.name = 'txtTDROAOD_STNK_ExpiredDate' + iteration;
	elExpDate.id = 'txtTDROAOD_STNK_ExpiredDate' + iteration;
	elExpDate.size = '10';
	//elExpDate.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	elExpDate.onclick=function(){ NewCssCal(elExpDate.id, 'MMddyyyy');  };
	cell9.appendChild(elExpDate);

	// Pajak - Start Date
	var cell10 = row.insertCell(10);
	var elPubDate = document.createElement('input');
	elPubDate.type = 'text';
	elPubDate.name = 'txtTDROAOD_Pajak_StartDate' + iteration;
	elPubDate.id = 'txtTDROAOD_Pajak_StartDate' + iteration;
	elPubDate.size = '10';
	//el.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	elPubDate.onclick=function(){ NewCssCal(elPubDate.id, 'MMddyyyy');  };
	cell10.appendChild(elPubDate);

	// Pajak - Expired Date
	var cell11 = row.insertCell(11);
	var elExpDate = document.createElement('input');
	elExpDate.type = 'text';
	elExpDate.name = 'txtTDROAOD_Pajak_ExpiredDate' + iteration;
	elExpDate.id = 'txtTDROAOD_Pajak_ExpiredDate' + iteration;
	elExpDate.size = '10';
	//elExpDate.setAttribute("onclick","javascript:NewCssCal('"+el.id+"', 'MMddyyyy');");
	elExpDate.onclick=function(){ NewCssCal(elExpDate.id, 'MMddyyyy');  };
	cell11.appendChild(elExpDate);

	// Lokasi (PT)
	var cell12 = row.insertCell(12);
	var el = document.createElement('input');
	el.type = 'text';
	el.name = 'txtROAOD_Location' + iteration;
	el.id = 'txtROAOD_Location' + iteration;
	cell12.appendChild(el);

	// Region
	var cell13 = row.insertCell(13);
	var sel = document.createElement('select');
	sel.name = 'optTDROAOD_Region' + iteration;
	sel.id = 'optTDROAOD_Region' + iteration;
	sel.options[0] = new Option('--- Pilih Region ---', '0');
	sel.options[1] = new Option('Kaltim', 'KALTIM');
	sel.options[2] = new Option('Kalteng', 'KALTENG');
	sel.options[3] = new Option('Kalbar', 'KALBAR');
	sel.options[4] = new Option('Jambi', 'JAMBI');
	sel.options[5] = new Option('Head Office', 'HO');
	cell13.appendChild(sel);

	// Keterangan
	var cell14 = row.insertCell(14);
	var el = document.createElement('textarea');
	el.setAttribute("cols","20");
	el.setAttribute("rows","1");
	el.name = 'txtTDROAOD_Keterangan' + iteration;
	el.id = 'txtTDROAOD_Keterangan' + iteration;
	cell14.appendChild(el);
}

// HAPUS BARIS
function removeRowFromTable() {
	var tbl = document.getElementById('detail');
	var lastRow = tbl.rows.length;
	if(document.getElementById('countRow').value > 1)
		document.getElementById('countRow').value -= 1;
	if (lastRow > 2)
		tbl.deleteRow(lastRow - 1);
}


$(function (){
	$('.select2').select2();
});
</script>
