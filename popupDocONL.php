<?PHP include ("./config/config_db.php"); ?>
<HTML>
<HEAD>
<TITLE>Daftar Dokumen</TITLE>
<SCRIPT LANGUAGE="JavaScript">
<!--
function pick(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOONLD_DocumentCode<?= $_GET['row'] ?>.value = symbol;
	window.opener.document.adddetaildoc.docCode.value = window.opener.document.adddetaildoc.docCode.value + "\'" + symbol + "\'" +",";
	window.close();
	}
}
function pickla(symbol,row) {
	if (window.opener && !window.opener.closed) {
    window.opener.document.adddetaildoc.txtTDLOONLD_DocCode<?= $_GET['row'] ?>.value = symbol;
	window.opener.document.adddetaildoc.docCode.value = window.opener.document.adddetaildoc.docCode.value + "\'" + symbol + "\'" +",";
	window.close();
	}
}
// -->
</SCRIPT>
<link href="./css/style.css" rel="stylesheet" type="text/css">
</HEAD>
<BODY>
<?PHP
$batas = 10;
$pg = isset( $_GET['pg'] ) ? $_GET['pg'] : "";

if ( empty( $pg ) ) {
	$posisi = 0;
	$pg = 1;
} else {
	$posisi = ( $pg - 1 ) * $batas;
}

$txtTHLOLD_CompanyID=$_GET['cID'];
$txtTHLOLD_DocumentGroupID=$_GET['gID'];

IF ($txtTHLOLD_DocumentGroupID<>"3"){
	if ($_GET['recentCode']) $filter =" AND donl.DONL_DocCode NOT IN (".substr($_GET[recentCode],0, -1).")";

	$query="SELECT DISTINCT c.Company_Name, dg.DocumentGroup_Name,
				donl.DONL_RegTime, donl.DONL_DocCode,
				donl.DONL_NamaDokumen, c2.Company_Name as Nama_PT,
       	        donl.DONL_TahunDokumen, donl.DONL_NoDokumen,
				donl.DONL_Dept_Code, dept.Department_Name
			FROM M_DocumentsOtherNonLegal donl
			LEFT JOIN M_Company c
				ON donl.DONL_CompanyID=c.Company_ID
            LEFT JOIN M_Company c2
                ON donl.DONL_PT_ID=c2.Company_ID
			LEFT JOIN M_DocumentGroup dg
				ON donl.DONL_GroupDocID=dg.DocumentGroup_ID
			LEFT JOIN db_master.M_Department dept
				ON dept.Department_Code=donl.DONL_Dept_Code
			AND donl.DONL_CompanyID='$txtTHLOLD_CompanyID'
			AND donl.DONL_GroupDocID='$txtTHLOLD_DocumentGroupID'
			AND donl.DONL_Status = '1'
			AND donl.DONL_Delete_Time IS NULL
			$filter
			ORDER BY donl.DONL_RegTime DESC";
	$limit = " LIMIT $posisi, $batas";
	$no = 1+$posisi;
	$lastQuery = $query.$limit;

	$sql = mysql_query($lastQuery);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		echo "<div class=title><b>$h_arr[Company_Name] - $h_arr[DocumentGroup_Name]</b></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
            <tr>
                <th>No.</th>
            	<th>PT</th>
            	<th>No. Dokumen</th>
            	<th>Nama Dokumen</th>
            	<th>Tahun Dokumen</th>
            	<th>Departemen</th>
            </tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT dt.DocumentType_Name, donl.DONL_RegTime, donl.DONL_DocCode, donl.DONL_NoDoc, donl.DONL_Instance, donl.DONL_PubDate, donl.DONL_ExpDate,
							   di1.DocumentInformation1_Name
						FROM M_DocumentsOtherNonLegal donl, M_DocumentType dt
						WHERE donl.DONL_Status ='1'
						AND donl.DONL_CompanyID='$txtTHLOLD_CompanyID'
						AND donl.DONL_GroupDocID='$txtTHLOLD_DocumentGroupID'
						AND donl.DONL_CategoryDocID='$optTDLOLD_DocumentCategoryID'
						AND donl.DONL_Delete_Time IS NULL
						AND donl.DONL_Status = '1'
						AND donl.DONL_TypeDocID=dt.DocumentType_ID
						$filter
						AND (
							donl.DONL_DocCode LIKE '%$search%'
							OR donl.DONL_TypeDocID LIKE '%$search%'
							OR dt.DocumentType_Name LIKE '%$search%'
							OR donl.DONL_Information1 LIKE '%$search%'
							OR di1.DocumentInformation1_Name LIKE '%$search%'
							OR donl.DONL_Information2 LIKE '%$search%'
							OR donl.DONL_Information3 LIKE '%$search%'
							OR donl.DONL_Instance LIKE '%$search%'
							OR donl.DONL_NoDoc LIKE '%$search%'
							OR donl.DONL_PubDate LIKE '%$search%'
							OR donl.DONL_ExpDate LIKE '%$search%'
						)
						ORDER BY donl.DONL_RegTime DESC ";
			$limit = " LIMIT $posisi, $batas";
			$sql = mysql_query($query.$limit);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			?>
			<tr>
				<td align='center'><u><a href="javascript:pick('<?= $arr['DONL_DocCode'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DONL_DocCode'] ?></a></u></td>
				<td align='center'><?= $arr['Nama_PT'] ?></td>
				<td align='center'><?= $arr['DONL_NoDokumen'] ?></td>
				<td align='center'><?= $arr['DONL_NamaDokumen'] ?></td>
                <td align='center'><?= $arr['DONL_TahunDokumen'] ?></td>
                <td align='center'><?= $arr['Department_Name'] ?></td>
			</tr>
			<?PHP
		}
	}
}

ELSE{
	$phase=$_GET['pID'];
	if ($_GET[recentCode]) $filter =" AND donl.DONL_Code NOT IN (".substr($_GET[recentCode],0, -1).")";

	$query="SELECT DISTINCT c.Company_Name, dg.DocumentGroup_Name, donl.DONL_RegTime, donl.DONL_Code, donl.DONL_Phase, donl.DONL_Period,
							donl.DONL_DocDate, donl.DONL_Block, donl.DONL_Village, donl.DONL_Owner
			FROM M_DocumentsOtherNonLegal donl, M_Company c, M_DocumentGroup dg
			WHERE donl.DONL_Status ='1'
			AND donl.DONL_CompanyID='$txtTHLOLD_CompanyID'
			AND dg.DocumentGroup_ID='$txtTHLOLD_DocumentGroupID'
			AND donl.DONL_Phase='$phase'
			AND donl.DONL_Delete_Time IS NULL
			AND donl.DONL_CompanyID=c.Company_ID
			$filter
			ORDER BY donl.DONL_RegTime DESC ";
	$limit = " LIMIT $posisi, $batas";
	$sql = mysql_query($query.$limit);
	$numRow = mysql_num_rows ($sql);
	if ($numRow==0) {
		echo "
		<table width='100%' border=0 cellspacing=0 cellpadding=0 style='border:none'>
		<tr>
			<td align='center'>
				<img src='./images/error.png'><br>
				<div class='error'>Tidak Ada Dokumen Yang Tersedia</div>
			</td>
		</tr>
		</table>
		<a href='#' onclick='window.close();'><b>[Tutup]</b></a>
		";
	}
	else{
		$h_sql=mysql_query($query);
		$h_arr=mysql_fetch_array($h_sql);
		$period=date("d M Y", strtotime($h_arr[DONL_Period]));
		echo "<form name='search' method='post' action='$PHP_SELF'>
			  <div style='text-align:left; padding:10px 5px; margin-bottom :5px; background :#CCC;'>
				<b>Pencarian :</b> <input name='txtSearch' id='txtSearch' type='text' size='25%'/>
			  </div>
			  </form>";
		echo "<div class=title><b>$h_arr[Company_Name] - $h_arr[DocumentGroup_Name]</b><br><i>Tahap $h_arr[DONL_Phase] : $period</i></div>";
		?>

		<table width="100%" border="1" cellspacing="0" cellpadding="0">
		<tr>
			<th width='15%'>Kode Dokumen</th>
			<th width='10%'>Tanggal Dokumen</th>
			<th width='35%'>Blok</th>
			<th width='20%'>Desa</th>
			<th width='20%'>Pemilik</th>
		<tr>
		<?PHP
		if($_POST) {
			$search=$_POST['txtSearch'];
			$query =   "SELECT c.Company_Name, dg.DocumentGroup_Name, donl.DONL_RegTime, donl.DONL_Code, donl.DONL_Phase, donl.DONL_Period,
							   donl.DONL_DocDate, donl.DONL_Block, donl.DONL_Village, donl.DONL_Owner
						FROM M_DocumentsOtherNonLegal donl, M_Company c, M_DocumentGroup dg
						WHERE donl.DONL_Status ='1'
						AND donl.DONL_CompanyID='$txtTHLOLD_CompanyID'
						AND dg.DocumentGroup_ID='$txtTHLOLD_DocumentGroupID'
						AND donl.DONL_Phase='$phase'
						AND donl.DONL_Delete_Time IS NULL
						AND donl.DONL_CompanyID=c.Company_ID
						$filter
						AND (
							donl.DONL_Code LIKE '%$search%'
							OR donl.DONL_DocDate LIKE '%$search%'
							OR donl.DONL_Block LIKE '%$search%'
							OR donl.DONL_Village LIKE '%$search%'
							OR donl.DONL_Owner LIKE '%$search%'
						)
						ORDER BY donl.DONL_RegTime DESC ";
			$limit = " LIMIT $posisi, $batas";
			$sql = mysql_query($query.$limit);
			$numSearch=mysql_num_rows($sql);
			if ($numSearch==0){
				echo"<tr><td colspan='20' align='center'><b>Data Tidak Ditemukan</b></td></tr>";
			}
		}

		while ($arr=mysql_fetch_array($sql)){
			$docdate=date("d M Y", strtotime($arr[DONL_DocDate]));
			?>
			<tr>
				<td align='center'><u><a href="javascript:pickla('<?= $arr['DONL_Code'] ?>','<?= $_GET['row'] ?>')"><?= $arr['DONL_Code'] ?></a></u></td>
				<td align='center'><?= $docdate ?></td>
				<td align='center'><?= $arr['DONL_Block'] ?></td>
				<td align='center'><?= $arr['DONL_Village'] ?></td>
				<td align='center'><?= $arr['DONL_Owner'] ?></td>
			</tr>
			<?PHP
		}
	}
}
?>
</TABLE>
<?php
	$jml_data = mysql_num_rows(mysql_query($query));
	$JmlHalaman = ceil($jml_data/$batas);
	if ( $pg > 1 ) {
		$link = $pg-1;
		if (isset($_GET[pID])) {
			$prev = "<a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Previous </a>";
		} else {
			$prev = "<a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Previous </a>";
		}
	} else {
		$prev = "Previous ";
	}

	$nmr = '';
	for ($i = 1; $i<= $JmlHalaman; $i++) {
		if ($i == $pg) {
			$nmr .= $i . " ";
		} else {
			if (isset($_GET[pID])) {
				$nmr .= "<a href='?pg=$i&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:green;font-weight:bold;'>$i</a> ";
			} else {
				$nmr .= "<a href='?pg=$i&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:green;font-weight:bold;'>$i</a> ";
			}
		}
	}

	if ($pg < $JmlHalaman) {
		$link = $pg + 1;
		if (isset($_GET[pID])) {
			$next = " <a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&pID=$_GET[pID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Next</a>";
		} else {
			$next = " <a href='?pg=$link&row=$_GET[row]&cID=$_GET[cID]&gID=$_GET[gID]&catID=$_GET[catID]&recentCode=$_GET[recentCode]' style='color:blue;font-weight:bold;'>Next</a>";
		}
	} else {
		$next = " Next";
	}

	if ($JmlHalaman > 1) echo '<br />Halaman : ' .$prev . $nmr . $next . '<br /><br />';
?>
</BODY>
</HTML>
