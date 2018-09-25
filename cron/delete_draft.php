<?PHP 
/* 
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Sabrina Ingrid Davita																				=
= Dibuat Tanggal	: 27 Sep 2012																						=
= Update Terakhir	: 27 Sep 2012																						=
= Revisi			:	
=========================================================================================================================
*/
?>
<HTML>
<HEAD>
	<title>Custodian System | Delete Draft Transaction</title>
</HEAD>
<BODY>
<?PHP 
include_once ("./config/config_db.php");
//Header Reg Dok
$query="DELETE FROM TH_RegistrationOfLegalDocument
		WHERE THROLD_Status='0'
		AND THROLD_Delete_Time IS NULL";
$sql = mysql_query($query);

//Header Reg GRL
$query="DELETE FROM TH_RegistrationOfLandAcquisitionDocument
		WHERE THRGOLAD_RegStatus='0'
		AND THRGOLAD_Delete_Time IS NULL";
$sql = mysql_query($query);

//Header Loan Dok
$query="DELETE FROM TH_LoanOfLegalDocument
		WHERE THLOLD_Status='0'
		AND THLOLD_Delete_Time IS NULL";
$sql = mysql_query($query);

//Header Loan GRL
$query="DELETE FROM TH_LoanOfLandAcquisitionDocument
		WHERE THLOLAD_Status='0'
		AND THLOLAD_Delete_Time IS NULL";
$sql = mysql_query($query);

//Header Rel Dok
$query="DELETE FROM TH_ReleaseOfLegalDocument
		WHERE THROLD_Status='0'
		AND THROLD_Delete_Time IS NULL";
$sql = mysql_query($query);

//Header Rel GRL
$query="DELETE FROM TH_ReleaseOfLandAcquisitionDocument
		WHERE THRLOLAD_Status='0'
		AND THRLOLAD_Delete_Time IS NULL";
$sql = mysql_query($query);
?>
</BODY>
</HTML>