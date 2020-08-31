<?php
include "connect_db.php";
 connect_db($conn);

if (//ako su poslane postavke
		isset($_POST["id"]) 
	)
	{
			$id=$_POST["id"];
		//spremanje

		$stmt = $conn->prepare("DELETE FROM cevapi.izvjestaji WHERE id=?; ");
		$stmt->bind_param("i",$id);

		// /* execute prepared statement */
		$stmt->execute();
		
		
		
		$conn->close();
		$csv='data/csv/'.$id.'.csv';
		$pdf='data/pdf/'.$id.'.pdf';
		if(file_exists($csv))
		{
			unlink($csv);
		}

		if(file_exists($pdf))
		{
			unlink($pdf);
		}

		
		// header('Location: settings.php');
			echo 'OK';
	}else
	{
		echo 'ERROR  ';
	}

?>