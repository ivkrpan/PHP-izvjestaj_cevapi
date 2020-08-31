<?php

include "connect_db.php";

echo '<h1 class="cover-heading left">Izvještaji</h1>
	<div class="izvjestaji left">';

echo '<table  style="width:100%">';

connect_db($conn);
	
$quary = $conn->prepare("SELECT id, naziv, godina,  DATE_FORMAT(created, '%d.%m.%Y. %H:%i:%s') as created  FROM cevapi.izvjestaji order by created desc");

$quary->execute();

$result	= $quary->get_result();

if ($result->num_rows > 0) 
{
	while($row = $result->fetch_assoc()) 
	{
		 echo ' <tr>
		 <td>'. $row["id"].'</td>
		<td><b><a href="#" onclick="showModal('.$row["id"].');" >'. $row["naziv"].'</a></b></td>
		<td>'. $row["godina"].'</td>
		<td>'. $row["created"].'</td>
		<td><a href="#" onclick="showModal('. $row["id"].');" ><img src="img/html.png" alt="HTML"  width="24" height="24"></a>
		<a href="data/csv/'.$row["id"].'.csv" download="izvjestaj_'.$row["godina"].'.csv" ><img src="img/csv.png" alt="CSV"  width="24" height="24"></a>
		<a href="data/pdf/'. $row["id"].'.pdf" download="izvjestaj_'.$row["godina"].'.pdf" ><img src="img/pdf.png" alt="PDF"  width="24" height="24"></a>
		</td>
		<td><a href="#" onclick="obrisi_izvjestaj('. $row["id"].');"><img src="img/delete.png" alt="Obriši"  width="24" height="24"></a></td>
		</tr>';
	}
}

$conn->close();

echo '</table>';
echo '</div>
	<p class="lead">
	<a id="myBtn" href="#" onclick="novi_izvjestaj();" class="btn btn-secondary right">Novi izvještaj</a>
	</p>';
	
?>