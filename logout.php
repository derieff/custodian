<?PHP
session_start();
include ("./config/config_db.php");
$ip=$_SERVER['REMOTE_ADDR'];
			
//Catat Action ke Logs
$logs_query="INSERT INTO Logs VALUES ('$_SESSION[User_ID]',sysdate(),'$ip','logout','logout')";
$mysqli->query($logs_query);
			
unset($_SESSION);
session_destroy();
echo "<meta http-equiv='refresh' content='0; url=index.php'>";
?>