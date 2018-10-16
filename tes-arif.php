<?php
// Fungsi header dengan mengirimkan raw data excel
header("Content-type: application/vnd-ms-excel");

// Mendefinisikan nama file ekspor "hasil-export.xls"
header("Content-Disposition: attachment; filename=contoh-convert-sql-ke-excel.xls");

include ("./config/config_db.php");
$query = "SELECT donl.DONL_ID, c.Company_Name, c2.Company_Name, donl.DONL_NoDokumen, donl.DONL_NamaDokumen, donl.DONL_TahunDokumen, m_d.Department_Name, lds.LDS_Name, donl.DONL_DocCode FROM M_DocumentsOtherNonLegal donl, M_Company c, M_User u, M_LoanDetailStatus lds, M_Company c2, db_master.M_Department m_d WHERE c.Company_ID=donl.DONL_CompanyID AND donl.DONL_Delete_Time IS NULL AND donl.DONL_Status=lds.LDS_ID AND donl.DONL_RegUserID=u.User_ID AND c2.Company_ID=donl.DONL_PT_ID AND m_d.Department_Code=donl.DONL_Dept_Code";

$sql = mysql_query($query);

$MainContent = "<table width='100%' border='1' class='stripeMe'>
<tr>
	<th colspan='9' align='center'>Daftar Dokumen Lainnya (Di Luar Legal)</th>
</tr>
<tr>
	<th>ID</th>
	<th>Kode Dokumen</th>
	<th>Perusahaan</th>
	<th>Nama PT (Dokumen)</th>
	<th>No. Dokumen</th>
	<th>Nama Dokumen</th>
	<th>Tahun Dokumen</th>
	<th>Departemen</th>
	<th>Status</th>
</tr>
";

while ($field = mysql_fetch_array($sql)) {
	$regdate=strtotime($field[3]);
	$fregdate=date("j M Y", $regdate);
$MainContent .="
<tr>
	<td class='center'>$field[DONL_ID]</td>
	<td class='center'>
		<a href='$PHP_SELF?act=detailONL&id=$field[DONL_DocCode]' class='underline'>$field[DONL_DocCode]</a></td>
	<td class='center'>$field[1]</td>
	<td class='center'>$field[2]</td>
	<td class='center'>$field[3]</td>
	<td class='center'>$field[4]</td>
	<td class='center'>$field[5]</td>
	<td class='center'>$field[6]</td>
	<td class='center'>$field[7]</td>
</tr>
";
$no=$no+1;
}
$MainContent .="</table>";

echo $MainContent;
 ?>
