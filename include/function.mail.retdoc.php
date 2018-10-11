<?PHP
/*
=========================================================================================================================
= Nama Project		: Custodian																							=
= Versi				: 1.1.0																								=
= Disusun Oleh		: IT Support Application - PT Triputra Agro Persada													=
= Developer			: Outsource																							=
= Dibuat Tanggal	: 27 Sep 2018																						=
= Update Terakhir	: 																									=
= Revisi			: 																									=
=========================================================================================================================
*/
include_once('./phpmailer/class.phpmailer.php');
include_once('./phpmailer/class.html2text.inc.php');
include_once ("./config/db_sql.php");
include_once ("./include/class.endencrp.php");

//Config server name, php cli does not have HTTP_HOST
if(!isset($_SERVER['HTTP_HOST'])){
	$_SERVER['HTTP_HOST']='localhost/custodian';
}

function mail_ret_legal($retCode,$User_ID,$docList,$userData,$subordinateID=-1,$lastReminder=-1){
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       ='10.20.10.5';
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	$mail->Subject  ='Notifikasi Pengembalian Dokumen '.$retCode;
	$mail->AddBcc('system.administrator@tap-agri.com');
	$requester=ucwords(strtolower($userData["User_FullName"]));
	$requester_dept=ucwords(strtolower($userData["Employee_Department"]));
	$requester_div=ucwords(strtolower($userData["Employee_Division"]));
	$documentGroupName=ucwords(strtolower($userData["DocumentGroup_Name"]));
	$body="";
	$docNum = count($docList);
	for($i=0;$i<$docNum;$i++){
		$body .= '<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
					<TD align="center" valign="top">'.($i+1).'</TD>
					<TD>'.$docList[$i]["Company_Name"].'<br />
						'.$docList[$i]["DocumentCategory_Name"].'<br />
						'.$docList[$i]["DocumentType_Name"].'<br />
						No. Dokumen		: '.$docList[$i]["DL_NoDoc"].'<br />
						Tgl.Pengeluaran	: '.$docList[$i]["RelTime"].'
					</TD>
				</TR>';
	}
	$bodyHeader = '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
	</tr>
	<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
	<tbody>
	<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen '.$documentGroupName.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengembalian sebagai berikut, telah melewati batas waktu pengembalian :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
		$bodyFooter ='';
		if($subordinateID==-1){
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk melakukan pengembalian dokumen.<br /> Terima kasih.  </span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				<p align=center style="margin-bottom: 7%;">
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">

						<a target="_BLANK" href="http://'.$_SERVER['HTTP_HOST'].'/return-of-document.php?act=add'.($lastReminder!=-1?'&lastReminder=1':'').'" style="color: white;" >Sudah Diterima</a>
					</span>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: white;" >Belum Diterima</a>
					</span><br />
				</p>
				</div>';
		}
		else{
			$bodyFooter = '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Mohon kerjasamanya untuk menginformasikan '.$requester.'.<br /> Terima kasih.  </span><br />
				</p>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div>
				</div>';
		}
		$bodyFooter .= '
				</td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
	</table>';
	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);


	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
	}
}

function mail_return_doc($retDoc, $reminder=0){
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='TESTING';
	$body = "";
	$bodyHeader = "";
	$bodyFooter = "";

	$e_query="	SELECT  User_ID,User_FullName,User_Email,DocumentGroup_Name,A_TransactionCode,
						ARC_AID,ARC_RandomCode
				FROM TD_ReturnOfLegalDocument
				LEFT JOIN M_DocumentLegal
					ON TDRTOLD_DocCode=DL_DocCode
				LEFT JOIN M_Approval
					ON TDRTOLD_ReturnCode=A_TransactionCode
					AND A_Delete_Time IS NULL
					AND A_Status='2'
				LEFT JOIN M_DocumentGroup
					ON DocumentGroup_ID=DL_GroupDocID
				LEFT JOIN M_User
					ON A_ApproverID=User_ID
				LEFT JOIN L_ApprovalRandomCode
					ON ARC_AID=A_ID
				WHERE TDRTOLD_ReturnCode='$retDoc'
				AND TDRTOLD_Delete_Time IS NULL";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       ='10.20.10.5';
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	if ($reminder){
		$mail->Subject  ='[REMINDER] '.$testing.' Persetujuan Pengembalian Dokumen '.$retDoc.'';
	}else{
		$mail->Subject  =''.$testing.' Persetujuan Pengembalian Dokumen '.$retDoc.'';
	}
	$mail->AddBcc('system.administrator@tap-agri.com');

		$ed_query="	SELECT DISTINCT Company_Name,DocumentCategory_Name,DocumentType_Name,
									DL_NoDoc, TDRTOLD_UserID,User_FullName,
									db_master.M_Employee.Employee_Department,
									db_master.M_Employee.Employee_Division
					FROM TD_ReturnOfLegalDocument
					LEFT JOIN M_DocumentLegal
						ON DL_DocCode=TDRTOLD_DocCode
					LEFT JOIN M_Company
						ON Company_ID=DL_CompanyID
					LEFT JOIN M_DocumentCategory
						ON DocumentCategory_ID=DL_CategoryDocID
					LEFT JOIN M_DocumentType
						ON DocumentType_ID=DL_TypeDocID
					LEFT JOIN M_User
						ON TDRTOLD_UserID=User_ID
					LEFT JOIN db_master.M_Employee
						ON M_User.User_ID = db_master.M_Employee.Employee_NIK
					WHERE TDRTOLD_ReturnCode='$retDoc'
					AND TDRTOLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								'.$ed_arr->DocumentType_Name.'<br />
								No. Dokumen : '.$ed_arr->DL_NoDoc.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$requester=ucwords(strtolower($ed_arr->User_FullName));
			$requester_dept=ucwords(strtolower($ed_arr->Employee_Department));
			$requester_div=ucwords(strtolower($ed_arr->Employee_Division));
		}
		$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
	<tbody>
	<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
	</tr>
	<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
	<tbody>
	<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa pengembalian dokumen '.$row->DocumentGroup_Name.' (berdasarkan permintaan <b>'.$requester.' / Dept : '.$requester_dept.' / Divisi : '.$requester_div.'</b>) dengan detail pengembalian sebagai berikut, membutuhkan persetujuan Bapak/Ibu :</span></p>
	<p>
		<TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';

		$bodyFooter .= '
					</TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Untuk itu dimohon Bapak/Ibu dapat memberikan persetujuan pengembalian dokumen di atas. Terima kasih.  </span><br />
				</p>
				<p align=center>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.retdoc.php?cfm='.$decrp->encrypt('accept').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Setuju</a>
					</span>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.retdoc.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($row->ARC_AID).'&rdm='.$decrp->encrypt($row->ARC_RandomCode).'">Tolak</a>
					</span><br />
				</p>
				</div>
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;margin-top:7%">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
	</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);

	/*
	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		//echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}*/

	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
	}
}

function mail_notif_return_doc($retCode, $User_ID, $status){
	$mail = new PHPMailer();
	$decrp = new custodian_encryp;
	$testing='TESTING';
	$body='';
    $bodyHeader='';
    $bodyFooter='';

	$e_query="SELECT User_ID, User_FullName, User_Email
			  FROM M_User
			  WHERE User_ID='$User_ID'";
	$handle = mysql_query($e_query);
	$row = mysql_fetch_object($handle);

	//setting email header
	$mail->IsSMTP();  // telling the class to use SMTP
	$mail->SMTPDebug  = 1;
	$mail->SMTPAuth   = false;
	$mail->Port       = 25;
	//$mail->Host       ='smtp.skyline.net.id';
	$mail->Host       ='10.20.10.5';
	//$mail->Username   = '@tap-agri.com';
	//$mail->Password   = '';
	$mail->AddReplyTo('no-reply@tap-agri.com','Custodian');
	$mail->From       = 'no-reply@tap-agri.com';
	$mail->FromName   = 'Custodian System';
	if ($status=='3'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Pengembalian Dokumen '.$retCode;
	}
	if ($status=='4'){
		$mail->Subject  =''.$testing.' Notifikasi Proses Pengembalian Dokumen '.$retCode;
	}
	$mail->AddBcc('system.administrator@tap-agri.com');
	//$mail->AddAttachment("images/icon_addrow.png", "icon_addrow.png");  // optional name

		$ed_query="	SELECT DISTINCT Company_Name,DocumentCategory_Name,DocumentType_Name,
									DL_NoDoc,THROLD_Reason,THROLD_Information,
									THLOLD_UserID,THROLD_ID,User_FullName
					FROM TH_ReleaseOfLegalDocument
					LEFT JOIN TD_ReleaseOfLegalDocument
						ON TDROLD_THROLD_ID=THROLD_ID
					LEFT JOIN TH_LoanOfLegalDocument
						ON THLOLD_LoanCode=THROLD_THLOLD_Code
					LEFT JOIN TD_LoanOfLegalDocument
						ON TDROLD_TDLOLD_ID=TDLOLD_ID
					LEFT JOIN M_DocumentLegal
						ON DL_DocCode=TDLOLD_DocCode
					LEFT JOIN M_Company
						ON Company_ID=DL_CompanyID
					LEFT JOIN M_DocumentCategory
						ON DocumentCategory_ID=DL_CategoryDocID
					LEFT JOIN M_DocumentType
						ON DocumentType_ID=DL_TypeDocID
					LEFT JOIN M_User
						ON THLOLD_UserID=User_ID
					WHERE THROLD_ReleaseCode='$retCode'
					AND THROLD_Delete_Time IS NULL";
		$ed_handle = mysql_query($ed_query);
		$edNum=1;
		while ($ed_arr = mysql_fetch_object($ed_handle)) {

			$body .= '
						<TR  style=" font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
							<TD align="center" valign="top">'.$edNum.'</TD>
							<TD>'.$ed_arr->Company_Name.'<br />
								'.$ed_arr->DocumentCategory_Name.'<br />
								'.$ed_arr->DocumentType_Name.'<br />
								No. Dokumen : '.$ed_arr->DL_NoDoc.'
							</TD>
						</TR>';
			$edNum=$edNum+1;
			$info=$ed_arr->THROLD_Information;
			$docID=$ed_arr->THROLD_ID;
			$reason=$ed_arr->THROLD_Reason;
			$regUser=$ed_arr->THLOLD_UserID;
			$requester=$ed_arr->User_FullName;
		}
		$bodyHeader .= '
	<table width="497" border="0" align="center" cellpadding="0" cellspacing="0">
<tbody>
<tr>
	<td style="padding: 4px 8px; background: #093 none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; color: #ffffff; font-weight: bolder; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif; vertical-align: middle; font-size: 16px; letter-spacing: -0.03em; text-align: left;width:100%">Custodian System</td>
</tr>
<tr>
	<td valign="top" style="border-left: 1px solid #cccccc; border-right: 1px solid #cccccc; border-bottom: 1px solid #3b5998; padding: 15px; background-color: #ffffff; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif"><table border="0">
<tbody>
<tr>
	<td width="458" align="justify" valign="top" style="font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;"><div style="margin-bottom: 15px; font-size: 13px">Yth '.$row->User_FullName.',</div>
	<div style="margin-bottom: 15px">
	<p><span style="margin-bottom: 15px; font-size: 13px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Bersama ini disampaikan bahwa dokumen (berdasarkan permintaan '.$requester.' untuk tujuan '.$info.') dengan detail permintaan dokumen sebagai berikut :</span></p>
	<p>
        <TABLE  width="458" >
		<TR align="center"  style="border: 1px solid #ffe222; padding: 10px; background-color: #c4df9b; color: #333333; font-size: 12px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">
			<TD width="10%"  style="font-size: 13px"><strong>No.</strong></TD>
			<TD width="90%"  style="font-size: 13px"><strong>Keterangan Dokumen</strong></TD>
		</TR>';
	if (($status=='3')&&($row->User_ID<>$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. User yang bersangkutan akan mengambil dokumen di atas ke Departemen Custodian. Terima kasih. </span><br />
				</p>
				</div>';
	}
	if (($status=='3')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Disetujui. Untuk itu Bapak/Ibu dapat mengambil dokumen di atas ke Departemen Custodian dengan membawa <u>Form Permintaan Dokumen</u>. Terima kasih.  </span><br />
				</p>
				<p align=center style="margin-bottom: 7%;">
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: left;margin-left: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.reldoc.php?act='.$decrp->encrypt('confirm').'&user='.$decrp->encrypt($regUser).'&doc='.$decrp->encrypt($docID).'&rel='.$decrp->encrypt($retCode).'">Sudah Diterima</a>
					</span>
					<span style="border: 1px solid green;padding: 5px;margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;background-color: rgb(196, 223, 155);color: white;float: right;margin-right: 15%;width: 20%;border-radius: 10px;">
						<a target="_BLANK" style="color: white;" href="http://'.$_SERVER['HTTP_HOST'].'/custodian/act.mail.reldoc.php?act='.$decrp->encrypt('reject').'&ati='.$decrp->encrypt($accept_row->ARC_AID).'&rdm='.$decrp->encrypt($accept_row->ARC_RandomCode).'">Belum Diterima</a>
					</span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID==$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Terima kasih.  </span><br />
				</p>
				</div>';
	}
	if (($status=='4')&&($row->User_ID<>$regUser)){
		$bodyFooter .= '
                    </TABLE>
				</p>
				<p><span style="margin-bottom: 15px; font-size: 13px;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Telah Ditolak dengan alasan : '.$reason.'<br>Terima kasih.  </span><br />
				</p>
				</div>';
	}
		$bodyFooter .= '
				<div style="margin: 0pt;font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif;">Hormat Kami,<br />Departemen Custodian<br />PT Triputra Agro Persada
				</div></td>
				</tr>
			</tbody>
			</table>
			</td>
		</tr>
		<tr>
			<td style="padding: 10px; color: #999999; font-size: 11px; font-family: \'lucida grande\',tahoma,verdana,arial,sans-serif">Mohon abaikan bila dokumen tersebut telah ditindaklanjuti.<br />
			<div align="left"><font color="#888888">Powered By Custodian System </font></div></td>
		</tr>
	</tbody>
</table>';

	$emailContent=$bodyHeader.$body.$bodyFooter;
	//echo $row->user_email.$body ;
	$mail->ClearAddresses();
	$mail->AddAddress($row->User_Email,$row->User_FullName);
	//$mail->AddAddress('sabrina.davita@tap-agri.com',$row->User_FullName);
	$h2t =& new html2text($body);
	$mail->AltBody = $h2t->get_text();
	$mail->WordWrap   = 80; // set word wrap
	$mail->MsgHTML($emailContent);

	/*
	try {
	  if ( !$mail->Send() ) {
		$error = "Unable to send to: " . $to . "<br>";
		throw new phpmailerAppException($error);
	  } else {
		//echo 'Message has been sent using SMTP<br><br>';
	  }
	} catch (phpmailerAppException $e) {
	  $errorMsg[] = $e->errorMessage();
	}

	if ( count($errorMsg) > 0 ) {
	  foreach ($errorMsg as $key => $value) {
		$thisError = $key + 1;
		//echo $thisError . ': ' . $value;
	  }
	}*/

	if(!$mail->Send()){
		echo "
		<table border='0' align='center' cellpadding='0' cellspacing='0'>
		<tbody>
		<tr>
			<td class='header'>Pengiriman Email Gagal</td>
		</tr>
		<tr>
			<td>
				ERROR<br>
				Terjadi Gangguan Dalam Pengiriman Email.<br>
				Mohon maaf atas ketidaknyamanan ini.<br>
				Terima kasih.<br><br>
				Hormat Kami,<br />Departemen Custodian<br />
				PT Triputra Agro Persada
			</td>
		</tr>
		<tr>
			<td class='footer'>
			Powered By Custodian System </td>
		</tr>
		</tbody>
		</table>";
	}
}
?>
