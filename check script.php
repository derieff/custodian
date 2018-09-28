<!--
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Sep 2018																						=
= Update Terakhir	: 																									=
= Revisi			:																									=
========================================================================================================================
-->

<link href="./css/mobile.css" rel="stylesheet" type="text/css">
<?PHP
include ("./config/config_db.php");
include ("./include/function.mail.retdoc.php");
include ("./include/function.mail.retdocla.php");
include ("./include/function.mail.retdoc.php");
include ("./include/function.mail.retdoc.php");
include ("./include/function.mail.retdoc.php");

$queryAssetOwnership = "SELECT tdroaod.TDROAOD_ID,FLOOR(DATEDIFF(tdroaod.TDROAOD_LeadTime,tdroaod.TDROAOD_Insert_Time)/7) ReminderLevel,
					tdroaod.TDROAOD_Insert_UserID UserID,tdloaod.TDLOAOD_DocCode DocCode,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROAOD_Insert_UserID) SupervisorID
				FROM TD_ReleaseOfAssetOwnershipDocument tdroaod
				LEFT JOIN TD_LoanOfAssetOwnershipDocument tdloaod ON tdroaod.TDROAOD_TDLOAOD_ID=tdloaod.TDLOAOD_ID
				  AND tdloaod.TDLOAOD_Delete_Time IS NULL
				LEFT JOIN TH_LoanOfAssetOwnershipDocument thloaod ON tdloaod.TDLOAOD_THLOAOD_ID=thloaod.THLOAOD_ID
				  AND thloaod.THLOAOD_Delete_Time IS NULL
				LEFT JOIN TD_ReturnOfAssetOwnershipDocument tdrtoaod ON tdloaod.TDLOAOD_DocCode=tdrtoaod.TDRTOAOD_DocCode
				  AND tdrtoaod.TDRTOAOD_Delete_Time IS NULL
				WHERE TDROAOD_Delete_Time IS NULL
				AND tdrtoaod.TDRTOAOD_ID IS NULL
				AND thloaod.THLOAOD_LoanCategoryID=1
				AND TDROAOD_LeadTime<=CURDATE()";
$sqlAssetOwnership = mysql_query($queryAssetOwnership);
$queryLandAcquisition = "SELECT tdrolad.TDRLOLAD_ID,FLOOR(DATEDIFF(tdrolad.TDRLOLAD_LeadTime,tdrolad.TDRLOLAD_Insert_Time)/7) ReminderLevel,
					tdrolad.TDRLOLAD_Insert_UserID UserID,tdlolad.TDLOLAD_DocCode DocCode,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDRLOLAD_Insert_UserID) SupervisorID
				FROM TD_ReleaseOfLandAcquisitionDocument tdrolad
				LEFT JOIN TD_LoanOfLandAcquisitionDocument tdlolad ON tdrolad.TDRLOLAD_TDLOLAD_ID=tdlolad.TDLOLAD_ID
				  AND tdlolad.TDLOLAD_Delete_Time IS NULL
				LEFT JOIN TH_LoanOfLandAcquisitionDocument thlolad ON tdlolad.TDLOLAD_THLOLAD_ID=thlolad.THLOLAD_ID
				  AND thlolad.THLOLAD_Delete_Time IS NULL
				LEFT JOIN TD_ReturnOfLandAcquisitionDocument tdrtolad ON tdlolad.TDLOLAD_DocCode=tdrtolad.TDRTOLAD_DocCode
				  AND tdrtolad.TDRTOLAD_Delete_Time IS NULL
				WHERE TDRLOLAD_Delete_Time IS NULL
				AND tdrtolad.TDRTOLAD_ID IS NULL
				AND thlolad.THLOLAD_LoanCategoryID=1
				AND TDRLOLAD_LeadTime<=CURDATE()";
$sqlLandAcquisition = mysql_query($queryLandAcquisition);
$queryLegal = "SELECT tdrold.TDROLD_ID,FLOOR(DATEDIFF(tdrold.TDROLD_LeadTime,tdrold.TDROLD_Insert_Time)/7) ReminderLevel,
					tdrold.TDROLD_Insert_UserID UserID,tdlold.TDLOLD_DocCode DocCode,
					COALESCE(mu.User_SPV2,mu.User_SPV1,TDROLD_Insert_UserID) SupervisorID
			FROM TD_ReleaseOfLegalDocument tdrold
			LEFT JOIN TD_LoanOfLegalDocument tdlold ON tdrold.TDROLD_TDLOLD_ID=tdlold.TDLOLD_ID
			  AND tdlold.TDLOLD_Delete_Time IS NULL
			LEFT JOIN TH_LoanOfLegalDocument thlold ON tdlold.TDLOLD_THLOLD_ID=thlold.THLOLD_ID
			  AND thlold.THLOLD_Delete_Time IS NULL
			LEFT JOIN TD_ReturnOfLegalDocument tdrtold ON tdlold.TDLOLD_DocCode=tdrtold.TDRTOLD_DocCode
			  AND tdrtold.TDRTOLD_Delete_Time IS NULL
			WHERE TDROLD_Delete_Time IS NULL
			AND tdrtold.TDRTOLD_ID IS NULL
			AND thlold.THLOLD_LoanCategoryID=1
			AND TDROLD_LeadTime<=CURDATE()";
$sqlLegal = mysql_query($queryLegal);
$queryOtherLegal = "SELECT tdroold.TDROOLD_ID,FLOOR(DATEDIFF(tdroold.TDROOLD_LeadTime,tdroold.TDROOLD_Insert_Time)/7) ReminderLevel,
						tdroold.TDROOLD_Insert_UserID UserID,tdloold.TDLOOLD_DocCode DocCode,
						COALESCE(mu.User_SPV2,mu.User_SPV1,TDROOLD_Insert_UserID) SupervisorID
					FROM TD_ReleaseOfOtherLegalDocuments tdroold
					LEFT JOIN TD_LoanOfOtherLegalDocuments tdloold ON tdroold.TDROOLD_TDLOOLD_ID=tdloold.TDLOOLD_ID
					  AND tdloold.TDLOOLD_Delete_Time IS NULL
					LEFT JOIN TH_LoanOfOtherLegalDocuments thloold ON tdloold.TDLOOLD_THLOOLD_ID=thloold.THLOOLD_ID
					  AND thloold.THLOOLD_Delete_Time IS NULL
					LEFT JOIN TD_ReturnOfOtherLegalDocuments tdrtoold ON tdloold.TDLOOLD_DocCode=tdrtoold.TDRTOOLD_DocCode
					  AND tdrtoold.TDRTOOLD_Delete_Time IS NULL
					WHERE TDROOLD_Delete_Time IS NULL
					AND tdrtoold.TDRTOOLD_ID IS NULL
					AND thloold.THLOOLD_LoanCategoryID=1
					AND TDROOLD_LeadTime<=CURDATE()";
$sqlOtherLegal = mysql_query($queryOtherLegal);
$queryOtherNonLegal = "SELECT tdroonld.TDROONLD_ID,FLOOR(DATEDIFF(tdroonld.TDROONLD_LeadTime,tdroonld.TDROONLD_Insert_Time)/7) ReminderLevel,
							tdroonld.TDROONLD_Insert_UserID UserID,tdloonld.TDLOONLD_DocCode DocCode,
							COALESCE(mu.User_SPV2,mu.User_SPV1,TDROONLD_Insert_UserID) SupervisorID
						FROM TD_ReleaseOfOtherNonLegalDocuments tdroonld
						LEFT JOIN TD_LoanOfOtherNonLegalDocuments tdloonld ON tdroonld.TDROONLD_TDLOONLD_ID=tdloonld.TDLOONLD_ID
						  AND tdloonld.TDLOONLD_Delete_Time IS NULL
						LEFT JOIN TH_LoanOfOtherNonLegalDocuments thloonld ON tdloonld.TDLOONLD_THLOONLD_ID=thloonld.THLOONLD_ID
						  AND thloonld.THLOONLD_Delete_Time IS NULL
						LEFT JOIN TD_ReturnOfOtherNonLegalDocuments tdrtoonld ON tdloonld.TDLOONLD_DocCode=tdrtoonld.TDRTOONLD_DocCode
						  AND tdrtoonld.TDRTOONLD_Delete_Time IS NULL
						WHERE TDROONLD_Delete_Time IS NULL
						AND tdrtoonld.TDRTOONLD_ID IS NULL
						AND thloonld.THLOONLD_LoanCategoryID=1
						AND TDROONLD_LeadTime<=CURDATE()";
$sqlOtherNonLegal = mysql_query($queryOtherNonLegal);

assetOwnershipIDs = "";
landAcquisitionIDs = "";
legalIDs = "";
otherLegalIDs = "";
otherNonLegalIDs = "";
while ($dataAssetOwnership = mysql_fetch_array($sqlAssetOwnership)) {
	assetOwnershipIDs.=$dataAssetOwnership['TDROAOD_ID'].",";
	mail_ret_asset_ownership($dataAssetOwnership['DocCode'],$dataAssetOwnership['UserID']);
	if($dataAssetOwnership['ReminderLevel']>1&&$dataAssetOwnership['SupervisorID']!=$dataAssetOwnership['UserID']){
		mail_ret_asset_ownership($dataAssetOwnership['DocCode'],$dataAssetOwnership['SupervisorID'],1);
	}
}
while ($dataLandAcquisition = mysql_fetch_array($sqlLandAcquisition)) {
	landAcquisitionIDs.=$dataLandAcquisition['TDRLOLAD_ID'].",";
	mail_ret_land_acquisition($dataLandAcquisition['DocCode'],$dataLandAcquisition['UserID']);
	if($dataAssetOwnership['ReminderLevel']>1&&$dataAssetOwnership['SupervisorID']!=$dataAssetOwnership['UserID']){
		mail_ret_land_acquisition($dataAssetOwnership['DocCode'],$dataAssetOwnership['SupervisorID'],1);
	}
}
while ($dataLegal = mysql_fetch_array($sqlLegal)) {
	legalIDs.=$dataLandAcquisition['TDROLD_ID'];
	mail_ret_legal($dataLandAcquisition['DocCode'],$dataLandAcquisition['UserID']);
	if($dataAssetOwnership['ReminderLevel']>1&&$dataAssetOwnership['SupervisorID']!=$dataAssetOwnership['UserID']){
		mail_ret_legal($dataAssetOwnership['DocCode'],$dataAssetOwnership['SupervisorID'],1);
	}
}
while ($dataOtherLegal = mysql_fetch_array($sqlOtherLegal)) {
	otherLegalIDs.=$dataOtherLegal['TDROOLD_ID'];
	mail_ret_other_legal($dataOtherLegal['DocCode'],$dataOtherLegal['UserID']);
	if($dataAssetOwnership['ReminderLevel']>1&&$dataAssetOwnership['SupervisorID']!=$dataAssetOwnership['UserID']){
		mail_ret_other_legal($dataAssetOwnership['DocCode'],$dataAssetOwnership['SupervisorID'],1);
	}
}
while ($dataOtherNonLegal = mysql_fetch_array($sqlOtherNonLegal)) {
	otherNonLegalIDs.=$dataOtherNonLegal['TDROONLD_ID'];
	mail_ret_other_non_legal($dataOtherNonLegal['DocCode'],$dataOtherNonLegal['UserID']);
	if($dataAssetOwnership['ReminderLevel']>1&&$dataAssetOwnership['SupervisorID']!=$dataAssetOwnership['UserID']){
		mail_ret_other_non_legal($dataAssetOwnership['DocCode'],$dataAssetOwnership['SupervisorID'],1);
	}
}
assetOwnershipIDs = rtrim($assetOwnershipIDs,",");
landAcquisitionIDs = rtrim($landAcquisitionIDs,",");
legalIDs = rtrim($legalIDs,",");
otherLegalIDs = rtrim($otherLegalIDs,",");
otherNonLegalIDs = rtrim($otherNonLegalIDs,",");

$updateUserID='cust0002';
$queryUpdateAssetOwnership = "UPDATE TD_ReleaseOfAssetOwnershipDocument SET TDROAOD_LeadTime=DATE_ADD(TDROAOD_LeadTime,INTERVAL 7 DAY),
								TDROAOD_Update_Time=NOW(),TDROAOD_Update_UserID='$updateUserID'
								WHERE TDROAOD_ID IN (".$assetOwnershipIDs.")";
$sqlUpdateAssetOwnership = mysql_query($queryUpdateAssetOwnership);
$queryUpdateLandAcquisition = "UPDATE TD_ReleaseOfLandAcquisitionDocument SET TDRLOLAD_LeadTime=DATE_ADD(TDRLOLAD_LeadTime,INTERVAL 7 DAY),
								TDRLOLAD_Update_Time=NOW(),TDRLOLAD_Update_UserID='$updateUserID'
								WHERE TDRLOLAD_ID IN (".$landAcquisitionIDs.")";
$sqlUpdateLandAcquisition = mysql_query($queryUpdateLandAcquisition);
$queryUpdateLandAcquisition = "UPDATE TD_ReleaseOfLegalDocument SET TDROLD_LeadTime=DATE_ADD(TDROLD_LeadTime,INTERVAL 7 DAY),
								TDROLD_Update_Time=NOW(),TDROLD_Update_UserID='$updateUserID'
								WHERE TDROLD_ID IN (".$legalIDs.")";
$sqlUpdateLandAcquisition = mysql_query($queryUpdateLandAcquisition);
$queryUpdateOtherLegal = "UPDATE TD_ReleaseOfOtherLegalDocuments SET TDROOLD_LeadTime=DATE_ADD(TDROOLD_LeadTime,INTERVAL 7 DAY),
								TDROOLD_Update_Time=NOW(),TDROOLD_Update_UserID='$updateUserID'
								WHERE TDROOLD_ID IN (".$otherLegalIDs.")";
$sqlUpdateOtherLegal = mysql_query($queryUpdateOtherLegal);
$queryUpdateOtherNonLegal = "UPDATE TD_ReleaseOfOtherNonLegalDocuments SET TDROONLD_LeadTime=DATE_ADD(TDROONLD_LeadTime,INTERVAL 7 DAY),
								TDROONLD_Update_Time=NOW(),TDROONLD_Update_UserID='$updateUserID'
								WHERE TDROONLD_ID IN (".$otherNonLegalIDs.")";
$sqlUpdateOtherNonLegal = mysql_query($queryUpdateOtherNonLegal);
?>