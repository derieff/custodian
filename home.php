<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 04 Mei 2012																						=
= Update Terakhir	: 29 Mei 2012																						=
= Revisi			:																									=
=		25 Mei 2012 : Persetujuan Untuk Registrasi Dokumen GRL (OK)														=
=		29 Mei 2012 : Persetujuan Permintaan & Pengeluaran Dokumen GRL (OK)												=
=========================================================================================================================
*/
session_start();
?>
<title>Custodian System | Beranda</title>
<?PHP
// Validasi untuk user yang terdaftar & memiliki hak akes untuk page tersebut
$path_parts=pathinfo($_SERVER['PHP_SELF']);
if(!isset($_SESSION['User_ID']) || !(in_array ($path_parts['basename'],$_SESSION['Access_Page']))) {
	echo "<meta http-equiv='refresh' content='0; url=index.php?act=error'>";
} else {

require_once "./include/template.inc";
include ("./include/class.endencrp.php");

$decrp = new custodian_encryp;
$page=new Template();

// Cari apakah user yang login mempunyai hak untuk menyetujui transaksi
$query = "SELECT *
		  FROM M_Approval
		  WHERE A_ApproverID='$_SESSION[User_ID]'
		  AND A_Status='2'
		  AND A_Delete_Time IS NULL";
$sql = mysql_query($query);
$num = mysql_num_rows($sql);

// Jika memiliki hak untuk menyetujui transaksi :
if ($num>0) {
	/* ---------------------------- */
	/* Daftar Persetujuan Transaksi */
	/* ---------------------------- */

	$MainContent ="<div class='home-title'>Menunggu Persetujuan Anda</div>";

	$query = "SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_RegistrationCode KodeTransaksi, throld.THROLD_RegistrationDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
							  'detail-of-registration-document.php' Link
			  FROM TH_RegistrationOfLegalDocument throld, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throld.THROLD_Delete_Time is NULL
			  AND throld.THROLD_CompanyID=c.Company_ID
			  AND throld.THROLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throld.THROLD_RegistrationCode
			  AND throld.THROLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throld.THRGOLAD_ID ID, throld.THRGOLAD_RegistrationCode KodeTransaksi, throld.THRGOLAD_RegistrationDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
							  'detail-of-registration-land-acquisition-document.php' Link
			  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throld.THRGOLAD_Delete_Time is NULL
			  AND throld.THRGOLAD_CompanyID=c.Company_ID
			  AND throld.THRGOLAD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throld.THRGOLAD_RegistrationCode
			  AND throld.THRGOLAD_RegStatus=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throaod.THROAOD_ID ID, throaod.THROAOD_RegistrationCode KodeTransaksi, throaod.THROAOD_RegistrationDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
							  'detail-of-registration-asset-ownership-document.php' Link
			  FROM TH_RegistrationOfAssetOwnershipDocument throaod, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throaod.THROAOD_Delete_Time is NULL
			  AND throaod.THROAOD_CompanyID=c.Company_ID
			  AND throaod.THROAOD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throaod.THROAOD_RegistrationCode
			  AND throaod.THROAOD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throold.THROOLD_ID ID, throold.THROOLD_RegistrationCode KodeTransaksi, throold.THROOLD_RegistrationDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
							  'detail-of-registration-other-legal-documents.php' Link
			  FROM TH_RegistrationOfOtherLegalDocuments throold, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throold.THROOLD_Delete_Time is NULL
			  AND throold.THROOLD_CompanyID=c.Company_ID
			  AND throold.THROOLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throold.THROOLD_RegistrationCode
			  AND throold.THROOLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throonld.THROONLD_ID ID, throonld.THROONLD_RegistrationCode KodeTransaksi, throonld.THROONLD_RegistrationDate TanggalTransaksi,
  			  				  u.User_FullName User, c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi, '1' IDKategori, 'Registrasi' Kategori,
  							  'detail-of-registration-other-non-legal-documents.php' Link
  			  FROM TH_RegistrationOfOtherNonLegalDocuments throonld, M_User u, M_Company c, M_Approval a,
  			  	   M_DocumentRegistrationStatus drs
  			  WHERE throonld.THROONLD_Delete_Time is NULL
  			  AND throonld.THROONLD_CompanyID=c.Company_ID
  			  AND throonld.THROONLD_UserID=u.User_ID
  			  AND a.A_ApproverID='$_SESSION[User_ID]'
  			  AND a.A_Status='2'
  			  AND a.A_TransactionCode=throonld.THROONLD_RegistrationCode
  			  AND throonld.THROONLD_Status=drs.DRS_Name
  			  UNION
			  SELECT DISTINCT thlold.THLOLD_ID ID, thlold.THLOLD_LoanCode KodeTransaksi, thlold.THLOLD_LoanDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
							  'detail-of-loan-document.php' Link
			  FROM TH_LoanOfLegalDocument thlold, M_User u, M_Company c, M_Approval a,M_DocumentRegistrationStatus drs
			  WHERE thlold.THLOLD_Delete_Time is NULL
			  AND thlold.THLOLD_CompanyID=c.Company_ID
			  AND thlold.THLOLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=thlold.THLOLD_LoanCode
			  AND thlold.THLOLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thlolad.THLOLAD_ID ID, thlolad.THLOLAD_LoanCode KodeTransaksi, thlolad.THLOLAD_LoanDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
							  'detail-of-loan-land-acquisition-document.php' Link
			  FROM TH_LoanOfLandAcquisitionDocument thlolad, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE thlolad.THLOLAD_Delete_Time is NULL
			  AND thlolad.THLOLAD_CompanyID=c.Company_ID
			  AND thlolad.THLOLAD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=thlolad.THLOLAD_LoanCode
			  AND thlolad.THLOLAD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thloaod.THLOAOD_ID ID, thloaod.THLOAOD_LoanCode KodeTransaksi, thloaod.THLOAOD_LoanDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
							  'detail-of-loan-asset-ownership-document.php' Link
			  FROM TH_LoanOfAssetOwnershipDocument thloaod, M_User u, M_Company c, M_Approval a,
			  	   M_DocumentRegistrationStatus drs
			  WHERE thloaod.THLOAOD_Delete_Time is NULL
			  AND thloaod.THLOAOD_CompanyID=c.Company_ID
			  AND thloaod.THLOAOD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=thloaod.THLOAOD_LoanCode
			  AND thloaod.THLOAOD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thloold.THLOOLD_ID ID, thloold.THLOOLD_LoanCode KodeTransaksi, thloold.THLOOLD_LoanDate TanggalTransaksi,
			 				 u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
			 				 'detail-of-loan-other-legal-documents.php' Link
			  FROM TH_LoanOfOtherLegalDocuments thloold, M_User u, M_Company c, M_Approval a,
			 	  M_DocumentRegistrationStatus drs
			  WHERE thloold.THLOOLD_Delete_Time is NULL
			  AND thloold.THLOOLD_CompanyID=c.Company_ID
			  AND thloold.THLOOLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=thloold.THLOOLD_LoanCode
			  AND thloold.THLOOLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thloonld.THLOONLD_ID ID, thloonld.THLOONLD_LoanCode KodeTransaksi, thloonld.THLOONLD_LoanDate TanggalTransaksi,
			 				 u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '2' IDKategori, 'Permintaan' Kategori,
			 				 'detail-of-loan-other-non-legal-documents.php' Link
			  FROM TH_LoanOfOtherNonLegalDocuments thloonld, M_User u, M_Company c, M_Approval a,
			 	  M_DocumentRegistrationStatus drs
			  WHERE thloonld.THLOONLD_Delete_Time is NULL
			  AND thloonld.THLOONLD_CompanyID=c.Company_ID
			  AND thloonld.THLOONLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=thloonld.THLOONLD_LoanCode
			  AND thloonld.THLOONLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_ReleaseCode KodeTransaksi, throld.THROLD_ReleaseDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-document.php' Link
			  FROM TH_ReleaseOfLegalDocument throld, M_User u, M_Company c, M_Approval a, TH_LoanOfLegalDocument thlold,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throld.THROLD_Delete_Time is NULL
			  AND thlold.THLOLD_LoanCode=throld.THROLD_THLOLD_Code
			  AND thlold.THLOLD_CompanyID=c.Company_ID
			  AND throld.THROLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throld.THROLD_ReleaseCode
			  AND throld.THROLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT thrlolad.THRLOLAD_ID ID, thrlolad.THRLOLAD_ReleaseCode KodeTransaksi, thrlolad.THRLOLAD_ReleaseDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-land-acquisition-document.php' Link
			  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, M_User u, M_Company c, M_Approval a,
			  	   TH_LoanOfLandAcquisitionDocument thlolad, M_DocumentRegistrationStatus drs
			  WHERE thrlolad.THRLOLAD_Delete_Time is NULL
			  AND thlolad.THLOLAD_LoanCode=thrlolad.THRLOLAD_THLOLAD_Code
			  AND thlolad.THLOLAD_CompanyID=c.Company_ID
			  AND thrlolad.THRLOLAD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=thrlolad.THRLOLAD_ReleaseCode
			  AND thrlolad.THRLOLAD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throaod.THROAOD_ID ID, throaod.THROAOD_ReleaseCode KodeTransaksi, throaod.THROAOD_ReleaseDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-asset-ownership-document.php' Link
			  FROM TH_ReleaseOfAssetOwnershipDocument throaod, M_User u, M_Company c, M_Approval a,
			  	   TH_LoanOfAssetOwnershipDocument thloaod, M_DocumentRegistrationStatus drs
			  WHERE throaod.THROAOD_Delete_Time is NULL
			  AND thloaod.THLOAOD_LoanCode=throaod.THROAOD_THLOAOD_Code
			  AND thloaod.THLOAOD_CompanyID=c.Company_ID
			  AND throaod.THROAOD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throaod.THROAOD_ReleaseCode
			  AND throaod.THROAOD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throold.THROOLD_ID ID, throold.THROOLD_ReleaseCode KodeTransaksi, throold.THROOLD_ReleaseDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-other-legal-documents.php' Link
			  FROM TH_ReleaseOfOtherLegalDocuments throold, M_User u, M_Company c, M_Approval a, TH_LoanOfOtherLegalDocuments thloold,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throold.THROOLD_Delete_Time is NULL
			  AND thloold.THLOOLD_LoanCode=throold.THROOLD_THLOOLD_Code
			  AND thloold.THLOOLD_CompanyID=c.Company_ID
			  AND throold.THROOLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throold.THROOLD_ReleaseCode
			  AND throold.THROOLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT throonld.THROONLD_ID ID, throonld.THROONLD_ReleaseCode KodeTransaksi, throonld.THROONLD_ReleaseDate TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '3' IDKategori, 'Pengeluaran' Kategori,
							  'detail-of-release-other-non-legal-documents.php' Link
			  FROM TH_ReleaseOfOtherNonLegalDocuments throonld, M_User u, M_Company c, M_Approval a, TH_LoanOfOtherNonLegalDocuments thloonld,
			  	   M_DocumentRegistrationStatus drs
			  WHERE throonld.THROONLD_Delete_Time is NULL
			  AND thloonld.THLOONLD_LoanCode=throonld.THROONLD_THLOONLD_Code
			  AND thloonld.THLOONLD_CompanyID=c.Company_ID
			  AND throonld.THROONLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=throonld.THROONLD_ReleaseCode
			  AND throonld.THROONLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT tdrtold.TDRTOLD_ID ID, tdrtold.TDRTOLD_ReturnCode KodeTransaksi, tdrtold.TDRTOLD_ReturnTime TanggalTransaksi,
			  				  u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
							  'return-of-document.php' Link
			  FROM TD_ReturnOfLegalDocument tdrtold, M_User u, M_Company c, M_Approval a,
			  	   	M_DocumentRegistrationStatus drs, M_DocumentLegal dl
			  WHERE tdrtold.TDRTOLD_Delete_Time is NULL
			  AND dl.DL_DocCode=tdrtold.TDRTOLD_DocCode
			  AND c.Company_ID=dl.DL_CompanyID
			  AND tdrtold.TDRTOLD_UserID=u.User_ID
			  AND a.A_ApproverID='$_SESSION[User_ID]'
			  AND a.A_Status='2'
			  AND a.A_TransactionCode=tdrtold.TDRTOLD_ReturnCode
			  AND tdrtold.TDRTOLD_Status=drs.DRS_Name
			  UNION
			  SELECT DISTINCT tdrtolad.TDRTOLAD_ID ID, tdrtolad.TDRTOLAD_ReturnCode KodeTransaksi, tdrtolad.TDRTOLAD_ReturnTime TanggalTransaksi,
							 u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
							 'return-of-land-acquisition-document.php' Link
			 FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_User u, M_Company c, M_Approval a,
				  M_DocumentRegistrationStatus drs, M_DocumentLandAcquisition dla
			 WHERE tdrtolad.TDRTOLAD_Delete_Time is NULL
			 AND dla.DLA_Code=tdrtolad.TDRTOLAD_DocCode
			 AND dla.DLA_CompanyID=c.Company_ID
			 AND tdrtolad.TDRTOLAD_UserID=u.User_ID
			 AND a.A_ApproverID='$_SESSION[User_ID]'
			 AND a.A_Status='2'
			 AND a.A_TransactionCode=tdrtolad.TDRTOLAD_ReturnCode
			 AND tdrtolad.TDRTOLAD_Status=drs.DRS_Name
			 UNION
			 SELECT DISTINCT tdrtoaod.TDRTOAOD_ID ID, tdrtoaod.TDRTOAOD_ReturnCode KodeTransaksi, tdrtoaod.TDRTOAOD_ReturnTime TanggalTransaksi,
							 u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
							 'return-of-asset-ownership-document.php' Link
			 FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod, M_User u, M_Company c, M_Approval a,
				  M_DocumentRegistrationStatus drs, M_DocumentAssetOwnership dao
			 WHERE tdrtoaod.TDRTOAOD_Delete_Time is NULL
			 AND dao.DAO_DocCode=tdrtoaod.TDRTOAOD_DocCode
			 AND dao.DAO_CompanyID=c.Company_ID
			 AND tdrtoaod.TDRTOAOD_UserID=u.User_ID
			 AND a.A_ApproverID='$_SESSION[User_ID]'
			 AND a.A_Status='2'
			 AND a.A_TransactionCode=tdrtoaod.TDRTOAOD_ReturnCode
			 AND tdrtoaod.TDRTOAOD_Status=drs.DRS_Name
			 UNION
			 SELECT DISTINCT tdrtoold.TDRTOOLD_ID ID, tdrtoold.TDRTOOLD_ReturnCode KodeTransaksi, tdrtoold.TDRTOOLD_ReturnTime TanggalTransaksi,
							 u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
							 'return-of-other-legal-documents.php' Link
			 FROM TD_ReturnOfOtherLegalDocuments tdrtoold, M_User u, M_Company c, M_Approval a,
				  M_DocumentRegistrationStatus drs, M_DocumentsOtherLegal dol
			 WHERE tdrtoold.TDRTOOLD_Delete_Time is NULL
			 AND dol.DOL_DocCode=tdrtoold.TDRTOOLD_DocCode
			 AND dol.DOL_CompanyID=c.Company_ID
			 AND tdrtoold.TDRTOOLD_UserID=u.User_ID
			 AND a.A_ApproverID='$_SESSION[User_ID]'
			 AND a.A_Status='2'
			 AND a.A_TransactionCode=tdrtoold.TDRTOOLD_ReturnCode
			 AND tdrtoold.TDRTOOLD_Status=drs.DRS_Name
			 UNION
			 SELECT DISTINCT tdrtoonld.TDRTOONLD_ID ID, tdrtoonld.TDRTOONLD_ReturnCode KodeTransaksi, tdrtoonld.TDRTOONLD_ReturnTime TanggalTransaksi,
							 u.User_FullName User, c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
							 'return-of-other-non-legal-documents.php' Link
			 FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_User u, M_Company c, M_Approval a,
				  M_DocumentRegistrationStatus drs, M_DocumentsOtherNonLegal donl
			 WHERE tdrtoonld.TDRTOONLD_Delete_Time is NULL
			 AND donl.DONL_DocCode=tdrtoonld.TDRTOONLD_DocCode
			 AND donl.DONL_CompanyID=c.Company_ID
			 AND tdrtoonld.TDRTOONLD_UserID=u.User_ID
			 AND a.A_ApproverID='$_SESSION[User_ID]'
			 AND a.A_Status='2'
			 AND a.A_TransactionCode=tdrtoonld.TDRTOONLD_ReturnCode
			 AND tdrtoonld.TDRTOONLD_Status=drs.DRS_Name
			  ORDER BY IDKategori, ID

			  "; //Arief F - 29082018
	$sql = mysql_query($query);
	$ext= mysql_num_rows($sql);

	// Jika memiliki ada dokumen yang menunggu persetujuan
	if ($ext>0) {
$MainContent .="
		<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th width='20%'>Kode Transaksi</th>
				<th width='15%'>Tanggal Transaksi</th>
				<th width='15%'>User</th>
				<th width='25%'>Perusahaan</th>
				<th width='10%'>Kategori</th>
				<th width='15%'>Status Transaksi</th>
			</tr>
";

		while ($arr = mysql_fetch_array($sql)){
			$TanggalTransaksi=date("j M Y", strtotime($arr['TanggalTransaksi']));
			if($arr['Kategori'] == "Registrasi"){
				$detailLink = "act=".$decrp->encrypt('approve')."&id=".$decrp->encrypt($arr['ID']);
			}elseif($arr['Kategori'] == "Pengembalian"){
				$detailLink = "act=detail&do=approve&id=".$arr['KodeTransaksi'];
			}else{
				$detailLink = "act=approve&id=$arr[ID]";
			}
			// $detailLink=($arr['Kategori']=="Registrasi")?"act=".$decrp->encrypt('approve')."&id=".$decrp->encrypt($arr[ID])."":"act=approve&id=$arr[ID]";
$MainContent .="
			<tr>
				<td class='center'>
					<a href='$arr[Link]?$detailLink' class='underline'>$arr[KodeTransaksi]</a>
				</td>
				<td class='center'>$TanggalTransaksi</td>
				<td class='center'>$arr[User]</td>
				<td class='center'>$arr[Perusahaan]</td>
				<td class='center'>$arr[Kategori]</td>
				<td class='center'>$arr[StatusTransaksi]</td>
			</tr>
";
 		}
$MainContent .="
		</table>";
	} // Akhir daftar persetujuan
}

/* ---------------------------- */
/* Daftar Transaksi Outstanding */
/* ---------------------------- */
$query = "SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_RegistrationCode KodeTransaksi, throld.THROLD_RegistrationDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '1' IDKategori, 'Registrasi' Kategori, 'detail-of-registration-document.php' Link
		  FROM TH_RegistrationOfLegalDocument throld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THROLD_Delete_Time is NULL
		  AND throld.THROLD_CompanyID=c.Company_ID
		  AND throld.THROLD_UserID='$_SESSION[User_ID]'
		  AND throld.THROLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throld.THRGOLAD_ID ID, throld.THRGOLAD_RegistrationCode KodeTransaksi, throld.THRGOLAD_RegistrationDate TanggalTransaksi,
						  c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi,
						  '1' IDKategori, 'Registrasi' Kategori,'detail-of-registration-land-acquisition-document.php' Link
		  FROM TH_RegistrationOfLandAcquisitionDocument throld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THRGOLAD_Delete_Time is NULL
		  AND throld.THRGOLAD_CompanyID=c.Company_ID
		  AND throld.THRGOLAD_UserID='$_SESSION[User_ID]'
		  AND throld.THRGOLAD_RegStatus=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throaod.THROAOD_ID ID, throaod.THROAOD_RegistrationCode KodeTransaksi, throaod.THROAOD_RegistrationDate TanggalTransaksi,
						  c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi,
						  '1' IDKategori, 'Registrasi' Kategori,'detail-of-registration-asset-ownership-document.php' Link
		  FROM TH_RegistrationOfAssetOwnershipDocument throaod, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throaod.THROAOD_Delete_Time is NULL
		  AND throaod.THROAOD_CompanyID=c.Company_ID
		  AND throaod.THROAOD_UserID='$_SESSION[User_ID]'
		  AND throaod.THROAOD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throold.THROOLD_ID ID, throold.THROOLD_RegistrationCode KodeTransaksi, throold.THROOLD_RegistrationDate TanggalTransaksi,
						  c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi,
						  '1' IDKategori, 'Registrasi' Kategori,'detail-of-registration-other-legal-documents.php' Link
		  FROM TH_RegistrationOfOtherLegalDocuments throold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throold.THROOLD_Delete_Time is NULL
		  AND throold.THROOLD_CompanyID=c.Company_ID
		  AND throold.THROOLD_UserID='$_SESSION[User_ID]'
		  AND throold.THROOLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throonld.THROONLD_ID ID, throonld.THROONLD_RegistrationCode KodeTransaksi, throonld.THROONLD_RegistrationDate TanggalTransaksi,
						  c.Company_Name Perusahaan,  drs.DRS_Description StatusTransaksi,
						  '1' IDKategori, 'Registrasi' Kategori,'detail-of-registration-other-non-legal-documents.php' Link
		  FROM TH_RegistrationOfOtherNonLegalDocuments throonld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throonld.THROONLD_Delete_Time is NULL
		  AND throonld.THROONLD_CompanyID=c.Company_ID
		  AND throonld.THROONLD_UserID='$_SESSION[User_ID]'
		  AND throonld.THROONLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thlold.THLOLD_ID ID, thlold.THLOLD_LoanCode KodeTransaksi, thlold.THLOLD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-document.php' Link
		  FROM TH_LoanOfLegalDocument thlold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thlold.THLOLD_Delete_Time is NULL
		  AND thlold.THLOLD_CompanyID=c.Company_ID
		  AND thlold.THLOLD_UserID='$_SESSION[User_ID]'
		  AND thlold.THLOLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thlolad.THLOLAD_ID ID, thlolad.THLOLAD_LoanCode KodeTransaksi, thlolad.THLOLAD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-land-acquisition-document.php' Link
		  FROM TH_LoanOfLandAcquisitionDocument thlolad, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thlolad.THLOLAD_Delete_Time is NULL
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID
		  AND thlolad.THLOLAD_UserID='$_SESSION[User_ID]'
		  AND thlolad.THLOLAD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thloaod.THLOAOD_ID ID, thloaod.THLOAOD_LoanCode KodeTransaksi, thloaod.THLOAOD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-asset-ownership-document.php' Link
		  FROM TH_LoanOfAssetOwnershipDocument thloaod, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thloaod.THLOAOD_Delete_Time is NULL
		  AND thloaod.THLOAOD_CompanyID=c.Company_ID
		  AND thloaod.THLOAOD_UserID='$_SESSION[User_ID]'
		  AND thloaod.THLOAOD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thloold.THLOOLD_ID ID, thloold.THLOOLD_LoanCode KodeTransaksi, thloold.THLOOLD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-other-legal-documents.php' Link
		  FROM TH_LoanOfOtherLegalDocuments thloold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thloold.THLOOLD_Delete_Time is NULL
		  AND thloold.THLOOLD_CompanyID=c.Company_ID
		  AND thloold.THLOOLD_UserID='$_SESSION[User_ID]'
		  AND thloold.THLOOLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thloonld.THLOONLD_ID ID, thloonld.THLOONLD_LoanCode KodeTransaksi, thloonld.THLOONLD_LoanDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '2' IDKategori, 'Permintaan' Kategori,'detail-of-loan-other-non-legal-documents.php' Link
		  FROM TH_LoanOfOtherNonLegalDocuments thloonld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thloonld.THLOONLD_Delete_Time is NULL
		  AND thloonld.THLOONLD_CompanyID=c.Company_ID
		  AND thloonld.THLOONLD_UserID='$_SESSION[User_ID]'
		  AND thloonld.THLOONLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT throld.THROLD_ID ID, throld.THROLD_ReleaseCode KodeTransaksi, throld.THROLD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-document.php' Link
		  FROM TH_ReleaseOfLegalDocument throld, TH_LoanOfLegalDocument thlold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE throld.THROLD_Delete_Time is NULL
		  AND thlold.THLOLD_LoanCode=throld.THROLD_THLOLD_Code
		  AND thlold.THLOLD_CompanyID=c.Company_ID
		  AND throld.THROLD_UserID='$_SESSION[User_ID]'
		  AND throld.THROLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thrlolad.THRLOLAD_ID ID, thrlolad.THRLOLAD_ReleaseCode KodeTransaksi, thrlolad.THRLOLAD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-land-acquisition-document.php' Link
		  FROM TH_ReleaseOfLandAcquisitionDocument thrlolad, TH_LoanOfLandAcquisitionDocument thlolad, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thrlolad.THRLOLAD_Delete_Time is NULL
		  AND thlolad.THLOLAD_LoanCode=thrlolad.THRLOLAD_THLOLAD_Code
		  AND thlolad.THLOLAD_CompanyID=c.Company_ID
		  AND thrlolad.THRLOLAD_UserID='$_SESSION[User_ID]'
		  AND thrlolad.THRLOLAD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thrloaod.THROAOD_ID ID, thrloaod.THROAOD_ReleaseCode KodeTransaksi, thrloaod.THROAOD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-asset-ownership-document.php' Link
		  FROM TH_ReleaseOfAssetOwnershipDocument thrloaod, TH_LoanOfAssetOwnershipDocument thloaod, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thrloaod.THROAOD_Delete_Time is NULL
		  AND thloaod.THLOAOD_LoanCode=thrloaod.THROAOD_THLOAOD_Code
		  AND thloaod.THLOAOD_CompanyID=c.Company_ID
		  AND thrloaod.THROAOD_UserID='$_SESSION[User_ID]'
		  AND thrloaod.THROAOD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thrloold.THROOLD_ID ID, thrloold.THROOLD_ReleaseCode KodeTransaksi, thrloold.THROOLD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-other-legal-documents.php' Link
		  FROM TH_ReleaseOfOtherLegalDocuments thrloold, TH_LoanOfOtherLegalDocuments thloold, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thrloold.THROOLD_Delete_Time is NULL
		  AND thloold.THLOOLD_LoanCode=thrloold.THROOLD_THLOOLD_Code
		  AND thloold.THLOOLD_CompanyID=c.Company_ID
		  AND thrloold.THROOLD_UserID='$_SESSION[User_ID]'
		  AND thrloold.THROOLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT thrloonld.THROONLD_ID ID, thrloonld.THROONLD_ReleaseCode KodeTransaksi, thrloonld.THROONLD_ReleaseDate TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '3' IDKategori, 'Pengeluaran' Kategori,'detail-of-release-other-non-legal-documents.php' Link
		  FROM TH_ReleaseOfOtherNonLegalDocuments thrloonld, TH_LoanOfOtherNonLegalDocuments thloonld, M_Company c, M_DocumentRegistrationStatus drs
		  WHERE thrloonld.THROONLD_Delete_Time is NULL
		  AND thloonld.THLOONLD_LoanCode=thrloonld.THROONLD_THLOONLD_Code
		  AND thloonld.THLOONLD_CompanyID=c.Company_ID
		  AND thrloonld.THROONLD_UserID='$_SESSION[User_ID]'
		  AND thrloonld.THROONLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT tdrtold.TDRTOLD_ID ID, tdrtold.TDRTOLD_ReturnCode KodeTransaksi, tdrtold.TDRTOLD_ReturnTime TanggalTransaksi,
						  c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi,
						  '4' IDKategori, 'Pengembalian' Kategori, 'return-of-document.php' Link
		  FROM TD_ReturnOfLegalDocument tdrtold, M_Company c, M_DocumentRegistrationStatus drs, M_DocumentLegal dl
		  WHERE tdrtold.TDRTOLD_Delete_Time is NULL
		  AND dl.DL_DocCode=tdrtold.TDRTOLD_DocCode
		  AND c.Company_ID=dl.DL_CompanyID
		  AND tdrtold.TDRTOLD_UserID='$_SESSION[User_ID]'
		  AND tdrtold.TDRTOLD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		  SELECT DISTINCT tdrtolad.TDRTOLAD_ID ID, tdrtolad.TDRTOLAD_ReturnCode KodeTransaksi, tdrtolad.TDRTOLAD_ReturnTime TanggalTransaksi,
						 c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
						 'return-of-land-acquisition-document.php' Link
		  FROM TD_ReturnOfLandAcquisitionDocument tdrtolad, M_Company c, M_Approval a,
		  		M_DocumentRegistrationStatus drs, M_DocumentLandAcquisition dla
		  WHERE tdrtolad.TDRTOLAD_Delete_Time is NULL
   		  AND dla.DLA_Code=tdrtolad.TDRTOLAD_DocCode
   		  AND dla.DLA_CompanyID=c.Company_ID
		  AND tdrtolad.TDRTOLAD_UserID='$_SESSION[User_ID]'
		  AND tdrtolad.TDRTOLAD_Status=drs.DRS_Name
		  AND drs.DRS_ID='2'
		  UNION
		 SELECT DISTINCT tdrtoaod.TDRTOAOD_ID ID, tdrtoaod.TDRTOAOD_ReturnCode KodeTransaksi, tdrtoaod.TDRTOAOD_ReturnTime TanggalTransaksi,
						 c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
						 'return-of-asset-ownership-document.php' Link
		 FROM TD_ReturnOfAssetOwnershipDocument tdrtoaod, M_Company c, M_Approval a,
			  M_DocumentRegistrationStatus drs, M_DocumentAssetOwnership dao
		 WHERE tdrtoaod.TDRTOAOD_Delete_Time is NULL
		 AND dao.DAO_DocCode=tdrtoaod.TDRTOAOD_DocCode
		 AND dao.DAO_CompanyID=c.Company_ID
		 AND tdrtoaod.TDRTOAOD_UserID='$_SESSION[User_ID]'
		 AND tdrtoaod.TDRTOAOD_Status=drs.DRS_Name
		 AND drs.DRS_ID='2'
		 UNION
		 SELECT DISTINCT tdrtoold.TDRTOOLD_ID ID, tdrtoold.TDRTOOLD_ReturnCode KodeTransaksi, tdrtoold.TDRTOOLD_ReturnTime TanggalTransaksi,
						 c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
						 'return-of-other-legal-documents.php' Link
		 FROM TD_ReturnOfOtherLegalDocuments tdrtoold, M_Company c, M_Approval a,
			  M_DocumentRegistrationStatus drs, M_DocumentsOtherLegal dol
		 WHERE tdrtoold.TDRTOOLD_Delete_Time is NULL
		 AND dol.DOL_DocCode=tdrtoold.TDRTOOLD_DocCode
		 AND dol.DOL_CompanyID=c.Company_ID
		 AND tdrtoold.TDRTOOLD_UserID='$_SESSION[User_ID]'
		 AND tdrtoold.TDRTOOLD_Status=drs.DRS_Name
		 AND drs.DRS_ID='2'
		 UNION
		 SELECT DISTINCT tdrtoonld.TDRTOONLD_ID ID, tdrtoonld.TDRTOONLD_ReturnCode KodeTransaksi, tdrtoonld.TDRTOONLD_ReturnTime TanggalTransaksi,
						 c.Company_Name Perusahaan, drs.DRS_Description StatusTransaksi, '4' IDKategori, 'Pengembalian' Kategori,
						 'return-of-other-non-legal-documents.php' Link
		 FROM TD_ReturnOfOtherNonLegalDocuments tdrtoonld, M_Company c, M_Approval a,
			  M_DocumentRegistrationStatus drs, M_DocumentsOtherNonLegal donl
		 WHERE tdrtoonld.TDRTOONLD_Delete_Time is NULL
		 AND donl.DONL_DocCode=tdrtoonld.TDRTOONLD_DocCode
		 AND donl.DONL_CompanyID=c.Company_ID
		 AND tdrtoonld.TDRTOONLD_UserID='$_SESSION[User_ID]'
		 AND tdrtoonld.TDRTOONLD_Status=drs.DRS_Name
		 AND drs.DRS_ID='2'
		  ORDER BY IDKategori, ID";
$sql = mysql_query($query);
$ext= mysql_num_rows($sql);

// Jika memiliki ada transaksi outstanding
if ($ext>0) {
$MainContent .="<div class='home-title'>Transaksi Anda Yang Masih Diproses</div>";
$MainContent .="
	<table width='100%' border='1' class='stripeMe'>
		<tr>
			<th width='30%'>Kode Transaksi</th>
			<th width='15%'>Tanggal Transaksi</th>
			<th width='25%'>Perusahaan</th>
			<th width='15%'>Kategori</th>
			<th width='15%'>Status Transaksi</th>
		</tr>
";

	while ($arr = mysql_fetch_array($sql)){
		$TanggalTransaksi=date("j M Y", strtotime($arr['TanggalTransaksi']));
		// $detailLink=($arr['Kategori']=="Registrasi")?"id=".$decrp->encrypt($arr[ID])."":"id=$arr[ID]";
		if($arr['Kategori'] == "Registrasi"){
			$detailLink = "act=".$decrp->encrypt('approve')."&id=".$decrp->encrypt($arr['ID']);
		}elseif($arr['Kategori'] == "Pengembalian"){
			$detailLink = "act=detail&id=".$arr['KodeTransaksi'];
		}else{
			$detailLink = "act=approve&id=$arr[ID]";
		}
$MainContent .="
		<tr>
			<td class='center'>
				<a href='$arr[Link]?$detailLink' class='underline'>$arr[KodeTransaksi]</a>
			</td>
			<td class='center'>$TanggalTransaksi</td>
			<td class='center'>$arr[Perusahaan]</td>
			<td class='center'>$arr[Kategori]</td>
			<td class='center'>$arr[StatusTransaksi]</td>
		</tr>
";
	}
$MainContent .="
	</table>";
} // Akhir daftar outstanding transaksi


// Cek apakah Administrator atau bukan.
		// Administrator memiliki hak untuk upload softcopy & edit dokumen.
		$query = "SELECT *
				  FROM M_UserRole
				  WHERE MUR_RoleID='1'
				  AND MUR_UserID='$_SESSION[User_ID]'
				  AND MUR_Delete_Time IS NULL";
		$sql = mysql_query($query);
		$admin = mysql_num_rows($sql);

if ($admin=="1") {
	/* ------------------------------------ */
	/* Daftar User Yang Menunggu Verifikasi */
	/* ------------------------------------ */
	$query="SELECT u.User_ID,u.User_FullName,u.User_Name,u.User_Email,d.Division_Name,dp.Department_Name,p.Position_Name, r.Role_Name
			FROM M_User u
			LEFT JOIN M_DivisionDepartmentPosition ddp ON ddp.DDP_UserID=u.User_ID
			LEFT JOIN M_Division d ON d.Division_ID=ddp.DDP_DivID
			LEFT JOIN M_Department dp ON dp.Department_ID=ddp.DDP_DeptID
			LEFT JOIN M_Position p ON p.Position_ID=ddp.DDP_PosID
			LEFT JOIN M_UserRole ur ON ur.MUR_UserID=u.User_ID
			LEFT JOIN M_Role r ON r.Role_ID=ur.MUR_RoleID
			WHERE u.User_Delete_Time is NULL
			AND (u.User_Name IS NULL
			OR u.User_Email IS NULL
			OR u.User_FullName IS NULL
			OR u.User_SPV IS NULL
			OR d.Division_Name IS NULL
			OR dp.Department_Name IS NULL
			OR p.Position_Name IS NULL
			OR r.Role_Name IS NULL)
			ORDER BY u.User_ID ";
	$sql = mysql_query($query);
	$ext= mysql_num_rows($sql);

	// Jika memiliki ada user yang harus diverifikasi
	if ($ext>0) {
	$MainContent .="<div class='home-title'>User Yang Harus Anda Autorisasi</div>";
	$MainContent .="
		<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th width='10%'>User ID</th>
				<th width='20%'>User Name</th>
				<th width='30%'>Nama Lengkap</th>
				<th width='30%'>Email</th>
				<th width='10%'></th>
			</tr>
	";

		while ($arr = mysql_fetch_array($sql)){
	$MainContent .="
			<tr>
				<td class='center'>$arr[User_ID]</td>
				<td class='center'>$arr[User_Name]</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[User_Email]</td>
				<td class='center'>
					<b>
					<a href='user.php?act=edit&id=$arr[User_ID]'><img title='Ubah' src='./images/icon-edit1.png' width='20'></a>
					<a href='user.php?act=delete&id=$arr[User_ID]'><img title='Hapus' src='./images/icon-delete1.png' width='20'></a>
					</b>
				</td>
			</tr>
	";
		}
	$MainContent .="
		</table>";
	} // Akhir daftar verifikasi user



	/* ---------------- */
	/* MISSING APPROVAL */
	/* ---------------- */
	$query="SELECT app.A_TransactionCode, users.User_FullName, dept.Department_Name, division.Division_Name
			FROM M_Approval app
			LEFT JOIN M_User users
			ON app.A_Insert_UserID=users.User_ID
			LEFT JOIN M_DivisionDepartmentPosition ddp
			ON ddp.DDP_UserID = users.User_ID
			LEFT JOIN M_Department dept
			ON dept.Department_ID = ddp.DDP_DeptID
			LEFT JOIN M_Division division
			ON division.Division_ID = ddp.DDP_DivID
			WHERE A_ApproverID=''
			ORDER BY app.A_Insert_Time";
	$sql = mysql_query($query);
	$ext= mysql_num_rows($sql);

	// Jika ada missing approval
	if ($ext>0) {
	$MainContent .="<div class='home-title'>Transaksi Yang Harus Anda Autorisasi</div>";
	$MainContent .="
		<table width='100%' border='1' class='stripeMe'>
			<tr>
				<th width='10%'>Kode Transaksi</th>
				<th width='20%'>Requester</th>
				<th width='30%'>Departemen</th>
				<th width='30%'>Divisi</th>
			</tr>
	";

		while ($arr = mysql_fetch_array($sql)){
	$MainContent .="
			<tr>
				<td class='center'>$arr[A_TransactionCode]</td>
				<td class='center'>$arr[User_FullName]</td>
				<td class='center'>$arr[Department_Name]</td>
				<td class='center'>$arr[Division_Name]</td>
			</tr>
	";
		}
	$MainContent .="
		</table>";
	} // Akhir daftar missing approval
}

$page->Content($MainContent);
$page->ShowWTopMenu();
}
?>
