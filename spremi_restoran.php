 <?php
include "connect_db.php";
 connect_db($conn);

if (//ako su poslane postavke
		isset($_POST["naziv"]) 	&& isset($_POST["adresa"]) &&
		isset($_POST["grad"])
	)
	{
		//spremanje
		$stmt = $conn->prepare("INSERT INTO cevapi.poslovnice VALUES(?, ? , ?, ?, ?) ON DUPLICATE KEY UPDATE naziv =? , adresa=?, grad=?, centar=?; ");
		$stmt->bind_param("isssisssi",$_POST["id"], $_POST["naziv"], $_POST["adresa"], $_POST["grad"],$_POST['cb_centar'],
		$_POST["naziv"], $_POST["adresa"], $_POST["grad"], $_POST['cb_centar']);

		$stmt->execute();
		
		$conn->close();
		
		echo 'OK';
	}else
	{
		echo 'ERROR  '.$_POST["naziv"];
	}
	
	
?>