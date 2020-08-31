<?php
include "connect_db.php";
 connect_db($conn);

if (//ako su poslane postavke
		isset($_POST["id"]) 
	)
	{	
		//spremanje
		$stmt = $conn->prepare("DELETE FROM cevapi.poslovnice WHERE id=?; ");
		$stmt->bind_param("i", $_POST["id"]);

		$stmt->execute();
		
		$conn->close();

		echo 'OK';
	}else
	{
		echo 'ERROR  ';
	}

?>