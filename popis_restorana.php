<?php

include "connect_db.php";

	echo '<h1 class="cover-heading left">Restorani</h1>
		<div class="izvjestaji left">';


	echo '<table  style="width:100%">';


connect_db($conn);
	
$quary = $conn->prepare("SELECT id, naziv, adresa, grad, IF(centar=1, 'CENTAR', 'PERIFERIJA') as centar FROM cevapi.poslovnice");

$quary->execute();

$result	= $quary->get_result();

if ($result->num_rows > 0) 
{
	while($row = $result->fetch_assoc()) 
	{
		 echo ' <tr>
	
		<td><b><a href="#" onclick="showForm('. $row["id"].');">'. $row["naziv"].'</a></b></td>
		<td>'. $row["adresa"].'</td>
		<td>'. $row["grad"].'</td>
		<td>'. $row["centar"].'</td>
		<td><a href="#" onclick="showForm('. $row["id"].');" ><img src="img/edit.png" alt="Uredi"  width="24" height="24"></a>
		<a href="#" onclick="obrisi_restoran('. $row["id"].');"><img src="img/delete.png" alt="Obriši"  width="24" height="24"></a></td>
		</tr>';
	}
}


$conn->close();


	echo '</table>';
	echo '</div>
		<p class="lead">
		<a id="btnNoviRestoran" href="#" onclick="showForm();" class="btn btn-secondary right">Dodaj restoran</a>
		</p>';
?>