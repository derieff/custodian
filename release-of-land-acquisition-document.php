<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.2																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 29 Mei 2012																						=
= Update Terakhir	: 26 Sep 2012																						=
= Revisi			:																									=
=		19/09/2012	: Perubahan Reminder Email																			=
=		26/09/2012	: Perubahan Query (LEFT JOIN)																		=
=========================================================================================================================
*/
session_start();
?>
<title>Custodian System | Pengeluaran Dokumen Pembebasan Lahan</title>
<head>
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.reldocla.php");
?>

<script type="text/javascript" src="./js/datetimepicker.js"></script>

<script language="JavaScript" type="text/JavaScript">
//LoV UTK DAFTAR PERMINTAAN DOKUMEN
function showList() {
	// var docGrup="grl";
	var docGrup="3"; // Arief F - 14082018
	sList = window.open("popupLoan.php?gID="+docGrup+"", "Daftar_Permintaan_Dokumen", "width=800,height=500,scrollbars=yes,resizable=yes");
}
function remLink() {
  if (window.sList && window.sList.open && !window.sList.closed)
	window.sList.opener = null;
}

// VALIDASI INPUT BAGIAN HEADER
function validateInputHeader(elem) {
	var returnValue;
	returnValue = true;
	checkDocCode = 0;

	var txtTHRLOLAD_THLOLAD_Code = document.getElementById('txtTHRLOLAD_THLOLAD_Code').value;
	var txtTHRLOLAD_UserID = document.getElementById('txtTHRLOLAD_UserID').value;

		if (txtTHRLOLAD_THLOLAD_Code.replace(" ", "") == "") {
			alert("Kode Permintaan Dokumen Belum Terisi!");
			returnValue = false;
		}
		else {
			txtTHRLOLAD_THLOLAD_Code.replace(" ", "");
			<?php
 			$query = "SELECT *
					  FROM TH_LoanOfLandAcquisitionDocument thlolad,TD_LoanOfLandAcquisitionDocument tdlolad
					  WHERE thlolad.THLOLAD_Delete_Time IS NULL
					  AND thlolad.THLOLAD_Status='accept'
					  AND tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
					  AND tdlolad.TDLOLAD_Response='0'";
 			$result = mysql_query($query);
			while ($data = mysql_fetch_array($result)) {
				$THLOLAD_LoanCode = $data['THLOLAD_LoanCode'];

				$a = "if (txtTHRLOLAD_THLOLAD_Code == '$THLOLAD_LoanCode') {";
				$a .= "checkDocCode = 1; ";
				$a .= "}";
			echo $a;
		 	}
			?>
			if (checkDocCode == 0) {
				alert("Kode Permintaan Dokumen SALAH!");
				returnValue = false;
			}
		}

	return returnValue;
}
// VALIDASI INPUT BAGIAN DETAIL UNTUK JENIS PERMINTAAN : PEMINJAMAN DOKUMEN
function validateInputDetail(elem) {
	var returnValue;
	returnValue = false;
	var notcheck = true;
	var TDLOLAD_ID = document.getElementsByName('TDLOLAD_ID[]');
	var DLA_Code = document.getElementsByName('DLA_Code[]');
	var txtTDRLOLAD_LeadTime = document.getElementsByName('txtTDRLOLAD_LeadTime[]');

	for (var i = 0; i < TDLOLAD_ID.length; i++){
		if (TDLOLAD_ID[i].checked) {
			if (txtTDRLOLAD_LeadTime[i].value.replace(/^\s+|\s+$/g,'') == "") {
				returnValue = false;
				notcheck = false;
				alert("Tanggal Pengembalian Untuk Dokumen "+DLA_Code[i].value+" Belum Ditentukan!");
			}
			else {
				returnValue = true;
				notcheck = false;
			}
		}
		else {
			notcheck = true;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Dokumen Yang Dipilih!");
		returnValue = false;
	}
	return returnValue;
}
// VALIDASI INPUT BAGIAN DETAIL UNTUK JENIS PERMINTAAN : PERMINTAAN DOKUMEN
function validateInputDetailx(elem) {
	var returnValue;
	returnValue = false;
	var notcheck = true;
	var TDLOLAD_ID = document.getElementsByName('TDLOLAD_ID[]');
	var DLA_Code = document.getElementsByName('DLA_Code[]');
	var txtTDRLOLAD_LeadTime = document.getElementsByName('txtTDRLOLAD_LeadTime[]');

	for (var i = 0; i < TDLOLAD_ID.length; i++){
		if (TDLOLAD_ID[i].checked) {
			returnValue = true;
			notcheck = false;
		}
		else {
			notcheck = true;
		}
	}

	if (notcheck) {
		alert ("Belum Ada Dokumen Yang Dipilih!");
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

$act=$_GET["act"];
if(isset($_GET["act"]))
{
	//Menambah Header / Dokumen Baru
	if($act=='add') {
		$ActionContent ="
		<form name='addRelDoc' method='post' action='$PHP_SELF'>
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengeluaran Dokumen Pembebasan Lahan</th>";

		$query = "SELECT u.User_FullName as FullName, ddp.DDP_DeptID as DeptID, ddp.DDP_DivID as DivID,
						 ddp.DDP_PosID as PosID, dp.Department_Name as DeptName, d.Division_Name as DivName,
						 p.Position_Name as PosName,u.User_SPV1,u.User_SPV2
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
				  WHERE u.User_ID='$_SESSION[User_ID]'";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$ActionContent .="
		<tr>
			<td width='30'>Nama</td>
			<td width='70%'>
				<input id='txtTHRLOLAD_UserID' type='hidden' value='$_SESSION[User_ID]'/>
				$field[FullName]
			</td>
		</tr>
		<tr>
			<td>Divisi</td>
			<td>
				<input id='txtTHRLOLAD_DivID' type='hidden' value='$field[DivID]'/>
				$field[DivName]
			</td>
		</tr>
		<tr>
			<td>Departemen</td>
			<td>
				<input id='txtTHRLOLAD_DeptID' type='hidden' value='$field[DeptID]'/>
				$field[DeptName]
			</td>
		</tr>
		<tr>
			<td>Jabatan</td>
			<td>
				<input id='txtTHRLOLAD_PosID' type='hidden' value='$field[PosID]'/>
				$field[PosName]
			</td>
		</tr>";

		if($field['User_SPV1']||$field['User_SPV2']){
			$ActionContent .="
			<tr>
				<td>Kode Permintaan Dokumen</td>
				<td>
					<input id='txtTHRLOLAD_THLOLAD_Code' name='txtTHRLOLAD_THLOLAD_Code' type='text' size='25' value='' readonly='readonly' onClick='javascript:showList();'/>
				</td>
			</tr>
			<tr>
				<td>Keterangan</td>
				<td><textarea name='txtTHRLOLAD_Information' id='txtTHRLOLAD_Information' cols='50' rows='2'></textarea></td>
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
		$code=$_GET["id"];

		$query ="SELECT releaseHeader.THRLOLAD_Information,
						releaseHeader.THRLOLAD_ReleaseDate,
						releaseHeader.THRLOLAD_ID,
						releaseHeader.THRLOLAD_ReleaseCode,
						u.User_FullName as FullName,
						ddp.DDP_DeptID as DeptID,
						ddp.DDP_DivID as DivID,
						ddp.DDP_PosID as PosID,
						dp.Department_Name as DeptName,
						d.Division_Name as DivName,
						p.Position_Name as PosName,
						u.User_SPV1,
						u.User_SPV2,
						c.Company_Name,
						dg.DocumentGroup_Name,
						thlolad.THLOLAD_DocumentType tipe_dokumen,
						thlolad.THLOLAD_LoanCategoryID kategori_permintaan
				 FROM TH_ReleaseOfLandAcquisitionDocument releaseHeader
				 LEFT JOIN M_User u
						ON u.User_ID=releaseHeader.THRLOLAD_UserID
					  LEFT JOIN M_DivisionDepartmentPosition ddp
						ON u.User_ID=ddp.DDP_UserID
						AND ddp.DDP_Delete_Time is NULL
					  LEFT JOIN M_Division d
						ON ddp.DDP_DivID=d.Division_ID
					  LEFT JOIN M_Department dp
						ON ddp.DDP_DeptID=dp.Department_ID
					  LEFT JOIN M_Position p
						ON ddp.DDP_PosID=p.Position_ID
					  LEFT JOIN TH_LoanOfLandAcquisitionDocument thlolad
						ON releaseHeader.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
						AND thlolad.THLOLAD_Delete_Time IS NULL
					  LEFT JOIN M_Company c
						ON thlolad.THLOLAD_CompanyID=c.Company_ID
					  LEFT JOIN M_DocumentGroup dg
						ON dg.DocumentGroup_ID='3'
				 WHERE releaseHeader.THRLOLAD_ReleaseCode='$code'
				 AND releaseHeader.THRLOLAD_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$field = mysql_fetch_array($sql);

		$fregdate=date("j M Y", strtotime($field['THRLOLAD_ReleaseDate']));
		// $atasan=($field['User_SPV2'])?$field['User_SPV2']:$field['User_SPV1'];

		$query = "SELECT u.User_ID, ra.RA_Name
				  FROM M_Role_Approver ra
				  LEFT JOIN M_Approver a
					ON ra.RA_ID=a.Approver_RoleID
				  LEFT JOIN M_User u
					ON a.Approver_UserID=u.User_ID
				  WHERE
				  	(ra.RA_Name='Custodian' or ra.RA_Name='Section Head Custodian')
					AND a.Approver_Delete_Time is NULL
				  ";
		$sql = mysql_query($query);
		while($d = mysql_fetch_array($sql)){
			$approvers[] = $d['User_ID'];  //Approval Untuk ke Custodian
		}

			$query = "SELECT u.User_ID, ra.RA_Name
					  FROM M_Role_Approver ra
					  LEFT JOIN M_Approver a
						ON ra.RA_ID=a.Approver_RoleID
					  LEFT JOIN M_User u
						ON a.Approver_UserID=u.User_ID
					  WHERE
					  	ra.RA_Name='Custodian Head'
						AND a.Approver_Delete_Time is NULL
					  ";
			$sql = mysql_query($query);
			while($d = mysql_fetch_array($sql)){
				$approvers[] = $d['User_ID'];  //Approval Untuk ke Custodian
			}

		$ActionContent ="
		<form name='add-detaildoc' method='post' action='$PHP_SELF' >
		<table width='100%' id='mytable' class='stripeMe'>
		<th colspan=3>Pengeluaran Dokumen Pembebasan Lahan</th>
		<tr>
			<td width='30'>Kode Pengeluaran Dokumen</td>
			<td width='70%'>
				<input name='txtTDRLOLAD_THRLOLAD_ID' type='hidden' value='$field[THRLOLAD_ID]'/>
				<input type='hidden' name='txtTDRLOLAD_THRLOLAD_ReleaseCode' value='$field[THRLOLAD_ReleaseCode]' readonly='true' class='readonly' style='width:80%;'/>
				$field[THRLOLAD_ReleaseCode]
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
			<td>Perusahaan</td>
			<td>$field[Company_Name]</td>
		</tr>
		<tr>
			<td>Keterangan</td>
			<td>
				<textarea name='txtTHRLOLAD_Information' id='txtTHRLOLAD_Information' cols='50' rows='2'>$field[THRLOLAD_Information]</textarea>
			</td>
		</tr>
		</table>

		<div style='space'>&nbsp;</div>";

		$query="SELECT tdlolad.TDLOLAD_ID, dla.DLA_Code, tdlolad.TDLOLAD_Information, thlolad.THLOLAD_LoanCategoryID,
					   dla.DLA_Phase, dla.DLA_Period, dla.DLA_DocDate, dla.DLA_Block, dla.DLA_Village,
					   dla.DLA_Owner, dla.DLA_Information
				FROM TD_LoanOfLandAcquisitionDocument tdlolad, TH_LoanOfLandAcquisitionDocument thlolad,
					 TH_ReleaseOfLandAcquisitionDocument thrlolad, M_DocumentLandAcquisition dla
				WHERE thrlolad.THRLOLAD_ReleaseCode='$code'
				AND thrlolad.THRLOLAD_Delete_Time IS NULL
				AND thrlolad.THRLOLAD_THLOLAD_Code=thlolad.THLOLAD_LoanCode
				AND thlolad.THLOLAD_ID=tdlolad.TDLOLAD_THLOLAD_ID
				AND tdlolad.TDLOLAD_Response='0'
				AND dla.DLA_Code=tdlolad.TDLOLAD_DocCode";
		$sql = mysql_query($query);
		$i=0;

		$ActionContent .="
		<table width='100%' id='detail' class='stripeMe'>
		<tr>
			<th></th>
			<th>Kode Dokumen</th>
			<th>Tahap GRL</th>
			<th>Periode GRL</th>
			<th>Tanggal Dokumen</th>
			<th>Blok</th>
			<th>Desa</th>
			<th>Pemilik</th>
			<th>Keterangan Pengeluaran Dokumen</th>
			<th>Waktu Pengembalian</th>
		</tr>";

		while ($arr=mysql_fetch_array($sql)) {
			$fperdate=date("j M Y", strtotime($arr['DLA_Period']));
			$fdocdate=date("j M Y", strtotime($arr['DLA_DocDate']));
			$LeadTime=($arr['THLOLAD_LoanCategoryID']=="1")?date('m/d/Y',strtotime("+7 day", strtotime($field['THRLOLAD_ReleaseDate']))):"";

			$ActionContent .="
			<tr>
				<td class='center'>
					<input id='TDLOLAD_ID[]' name='TDLOLAD_ID[]' type='checkbox' value='$arr[TDLOLAD_ID]'>
				</td>
				<td class='center'><input id='DLA_Code[]' name='DLA_Code[]' type='hidden' value='$arr[DLA_Code]'>$arr[DLA_Code]</td>
				<td class='center'>$arr[DLA_Phase]</td>
				<td class='center'>$fperdate</td>
				<td class='center'>$fdocdate</td>
				<td class='center'>$arr[DLA_Block]</td>
				<td class='center'>$arr[DLA_Village]</td>
				<td class='center'>$arr[DLA_Owner]</td>
				<td class='center'>
					<textarea id='txtTDRLOLAD_Information' name='txtTDRLOLAD_Information[]'></textarea>
				</td>
				<td class='center'>
					<input id='txtTDRLOLAD_LeadTime[$i]' name='txtTDRLOLAD_LeadTime[]' type='text' value='$LeadTime' size='10'  readonly='readonly' class='readonly'>
				</td>
			</tr>";
			$i++;
			$loanType=$arr['THLOLAD_LoanCategoryID'];
		}
		$ActionContent .="
		</table>

		<table width='100%'>
		<tr>
			<td>";
			foreach($approvers as $approver){
				$ActionContent .="<input type='text' name='txtA_ApproverID[]' value='$approver' readonly='true' class='readonly'/>";
			}
			$ActionContent .="</td>
		</tr>
		<tr>";

		if ($loanType=="1") {
			$ActionContent .="
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetail(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>";
		}else {
			$ActionContent .="
			<th>
				<input name='adddetail' type='submit' value='Daftar' class='button' onclick='return validateInputDetailx(this);'/>
				<input name='canceldetail' type='submit' value='Batal' class='button'/>
			</th>";
		}

		$ActionContent .="
		</table>

		<div class='alertRed10px'>
			PERINGATAN : <br>
			Periksa Kembali Data Anda. Apabila Data Telah Disimpan, Anda Tidak Dapat Mengubahnya Lagi.
		</div>
		</form>";
	}
	//Kirim Ulang Email Persetujuan
	elseif($act=='resend'){
		mail_release_doc($_GET['code'],'1');
		echo"<script>alert('Email Persetujuan Telah Dikirim Ulang.');</script>";
		echo "<meta http-equiv='refresh' content='0; url=release-of-land-acquisition-document.php'>";
	}
}

// Menampilkan Dokumen
$dataPerPage = 20;

if(isset($_GET['page']))
    $noPage = $_GET['page'];
else
	$noPage = 1;

$offset = ($noPage - 1) * $dataPerPage;

$query = "SELECT thrlolad.THRLOLAD_ID, thrlolad.THRLOLAD_ReleaseCode, thrlolad.THRLOLAD_ReleaseDate, u.User_FullName,
 		         drs.DRS_Description,thrlolad.THRLOLAD_Status
		  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u, M_DocumentRegistrationStatus drs
		  WHERE thrlolad.THRLOLAD_Delete_Time is NULL
		  AND thrlolad.THRLOLAD_UserID=u.User_ID
		  AND u.User_ID='$_SESSION[User_ID]'
		  AND thrlolad.THRLOLAD_Status=drs.DRS_Name
		  ORDER BY thrlolad.THRLOLAD_ID DESC
		  LIMIT $offset, $dataPerPage";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

$MainContent ="
<table width='100%' border='1' class='stripeMe'>
<tr>
	<th width='30%'>Kode Pengeluaran</th>
	<th width='25%'>Tanggal Pengeluaran</th>
	<th width='20%'>Dikeluarkan Oleh</th>
	<th width='20%'>Status</th>
	<th width='5%'></th>
</tr>";

if ($num==NULL) {
	$MainContent .="
	<tr>
		<td colspan=6 align='center'>Belum Ada Data</td>
	</tr>";
}else{
	while ($field = mysql_fetch_array($sql)) {
		$fregdate=date("j M Y", strtotime($field['THRLOLAD_ReleaseDate']));
		$resend=($field['THRLOLAD_Status']=="waiting")?"<b><a href='$PHP_SELF?act=resend&code=$field[THRLOLAD_ReleaseCode]'><img title='Kirim Ulang Email Persetujuan' src='./images/icon-resend.png' width='20'></a></b>":"";

		$MainContent .="
		<tr>
			<td class='center'>
				<a href='detail-of-release-land-acquisition-document.php?id=$field[THRLOLAD_ID]' class='underline'>$field[THRLOLAD_ReleaseCode]</a>
			</td>
			<td class='center'>$fregdate</td>
			<td class='center'>$field[User_FullName]</td>
			<td class='center'>$field[DRS_Description]</td>
			<td class='center'>$resend</td>
		</tr>";
 	}
}
$MainContent .="</table>";

$query1 = "SELECT thrlolad.THRLOLAD_ID, thrlolad.THRLOLAD_ReleaseCode, thrlolad.THRLOLAD_ReleaseDate, u.User_FullName,
 		          thrlolad.THRLOLAD_Status
		   FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u
		   WHERE thrlolad.THRLOLAD_Delete_Time is NULL
		   AND thrlolad.THRLOLAD_UserID=u.User_ID
		   AND u.User_ID='$_SESSION[User_ID]'
		   ORDER BY thrlolad.THRLOLAD_ID DESC";
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

/* ACTIONS */
if(isset($_POST[cancel])) {
	echo "<meta http-equiv='refresh' content='0; url=release-of-land-acquisition-document.php'>";
}

elseif(isset($_POST[canceldetail])) {
	$cd_query="UPDATE M_CodeTransaction ct, TH_ReleaseOfLandAcquisitionDocument thrlolad
			   SET ct.CT_Delete_UserID='$_SESSION[User_ID]',ct.CT_Delete_Time=sysdate(),
			       ct.CT_Update_UserID='$_SESSION[User_ID]',ct.CT_Update_Time=sysdate(),
			       thrlolad.THRLOLAD_Delete_UserID='$_SESSION[User_ID]',thrlolad.THRLOLAD_Delete_Time=sysdate(),
			       thrlolad.THRLOLAD_Update_UserID='$_SESSION[User_ID]',thrlolad.THRLOLAD_Update_Time=sysdate()
			   WHERE thrlolad.THRLOLAD_ID='$_POST[txtTDRLOLAD_THRLOLAD_ID]'
			   AND thrlolad.THRLOLAD_ReleaseCode=ct.CT_Code";
	if($mysqli->query($cd_query)) {
		echo "<meta http-equiv='refresh' content='0; url=release-of-land-acquisition-document.php'>";
	}
}

elseif(isset($_POST[addheader])) {
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
	$query = "SELECT c.Company_Code
			  FROM TH_LoanOfLandAcquisitionDocument thlolad, M_Company c
			  WHERE thlolad.THLOLAD_LoanCode='$_POST[txtTHRLOLAD_THLOLAD_Code]'
			  AND thlolad.THLOLAD_CompanyID=c.Company_ID";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	$Company_Code=$field['Company_Code'];
	$DocumentGroup_Code='GRL';

	// Cari No Pengeluaran Dokumen Terakhir
	$query = "SELECT MAX(CT_SeqNo)
			  FROM M_CodeTransaction
			  WHERE CT_Year='$regyear'
			  AND CT_Action='OUT'
			  AND CT_GroupDocCode='$DocumentGroup_Code'
			  AND CT_Delete_Time is NULL";
	$sql = mysql_query($query);
	$field = mysql_fetch_array($sql);

	if($field[0]==NULL)
		$maxnum=0;
	else
		$maxnum=$field[0];
	$nnum=$maxnum+1;
	$newnum=str_pad($nnum,3,"0",STR_PAD_LEFT);

	// Kode Pengeluaran Dokumen
	$CT_Code="$newnum/OUT/$Company_Code/$DocumentGroup_Code/$regmonth/$regyear";

	// Insert kode Pengeluaran dokumen baru
	$sql= "INSERT INTO M_CodeTransaction
		   VALUES (NULL,'$CT_Code','$nnum','OUT','$Company_Code','$DocumentGroup_Code','$rmonth','$regyear',
				   '$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',sysdate(),NULL,NULL)";

	if($mysqli->query($sql)) {
		$info=str_replace("<br>", "\n", $_POST['txtTHRLOLAD_Information']);
		//Insert Header Dokumen
		$sql1= "INSERT INTO TH_ReleaseOfLandAcquisitionDocument
				VALUES (NULL,'$CT_Code',sysdate(),'$_SESSION[User_ID]','$_POST[txtTHRLOLAD_THLOLAD_Code]',
					    '$info','0',NULL,NULL,'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		if($mysqli->query($sql1)) {
			echo "<meta http-equiv='refresh' content='0; url=release-of-land-acquisition-document.php?act=adddetail&id=$CT_Code'>";
		}
	}
	else {
		$ActionContent .="<div class='warning'>Penambahan Data Gagal.</div>";
	}
}

elseif(isset($_POST[adddetail])) {
	$TDLOLAD_ID=$_POST[TDLOLAD_ID];
	$txtTHRLOLAD_Information=str_replace("<br>", "\n",$_POST[txtTHRLOLAD_Information]);
	$txtTDRLOLAD_Information=str_replace("<br>", "\n",$_POST[txtTDRLOLAD_Information]);
	$txtTDRLOLAD_LeadTime=$_POST[txtTDRLOLAD_LeadTime];
	$sum=count($TDLOLAD_ID);

	for ($i=0 ; $i<$sum ; $i++) {
		$TDRLOLAD_LeadTime=date('Y-m-d H:i:s', strtotime($txtTDRLOLAD_LeadTime[$i]));
		if ($TDRLOLAD_LeadTime=="1970-01-01 08:00:00"){
			$TDRLOLAD_LeadTime="";
		}
		$sql1= "INSERT INTO TD_ReleaseOfLandAcquisitionDocument
				VALUES (NULL,NULL,'$_POST[txtTDRLOLAD_THRLOLAD_ID]', '$TDLOLAD_ID[$i]','$txtTDRLOLAD_Information[$i]',
						'$TDRLOLAD_LeadTime',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]',
						sysdate(),NULL,NULL)";
		$sql2= "UPDATE TD_LoanOfLandAcquisitionDocument
				SET TDLOLAD_Response='1', TDLOLAD_Update_UserID='$_SESSION[User_ID]',TDLOLAD_Update_Time=sysdate()
				WHERE TDLOLAD_ID='$TDLOLAD_ID[$i]'";
		$mysqli->query($sql1);
		$mysqli->query($sql2);
	}

	$txtA_ApproverID=$_POST['txtA_ApproverID'];
	$jumlah=count($txtA_ApproverID);

	for($i=0;$i<$jumlah;$i++){
		$step=$i+1;
		$sql2= "INSERT INTO M_Approval
				VALUES (NULL,'$_POST[txtTDRLOLAD_THRLOLAD_ReleaseCode]', '$txtA_ApproverID[$i]', '$step',
				        '1',NULL,'$_SESSION[User_ID]', sysdate(),'$_SESSION[User_ID]', sysdate(),NULL,NULL)";
		$mysqli->query($sql2);
		$sa_query="SELECT *
				   FROM M_Approval
				   WHERE A_TransactionCode='$_POST[txtTDRLOLAD_THRLOLAD_ReleaseCode]'
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

	$sql3= "UPDATE M_Approval
			SET A_Status='2', A_Update_UserID='$_SESSION[User_ID]',A_Update_Time=sysdate()
			WHERE A_TransactionCode ='$_POST[txtTDRLOLAD_THRLOLAD_ReleaseCode]'
			AND A_Step='1'";

	$sql4= "UPDATE TH_ReleaseOfLandAcquisitionDocument
			SET THRLOLAD_Status='waiting', THRLOLAD_Information='$txtTHRLOLAD_Information',
			THRLOLAD_Update_UserID='$_SESSION[User_ID]',THRLOLAD_Update_Time=sysdate()
			WHERE THRLOLAD_ReleaseCode='$_POST[txtTDRLOLAD_THRLOLAD_ReleaseCode]'
			AND THRLOLAD_Delete_Time IS NULL";

	if(($mysqli->query($sql3)) && ($mysqli->query($sql4)) ) {
		// Kirim Email ke Approver 1
		mail_release_doc($_POST['txtTDRLOLAD_THRLOLAD_ReleaseCode']);
		echo "<meta http-equiv='refresh' content='0; url=release-of-land-acquisition-document.php'>";
	}
}

$page->ActContent($ActionContent);
$page->Content($MainContent);
$page->Pagers($Pager);
$page->Show();
}
?>

<script language="JavaScript" type="text/JavaScript">
// Menampilkan DatePicker
function getLeadTime(rows){
	 var cal = Calendar.setup({
			  onSelect: function(cal) { cal.hide() },
			  showTime: true
		  });

  	for (i=0;i<=rows;i++){
		 cal.manageFields("txtTDRLOLAD_LeadTime["+i+"]", "txtTDRLOLAD_LeadTime["+i+"]", "%m/%d/%Y");
	}
}
</script>
