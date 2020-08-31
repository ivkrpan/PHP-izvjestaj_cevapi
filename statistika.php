<?php
include "connect_db.php";


	function dohvati_parametre(&$velikih_cevapa,&$malih_cevap,&$velika_porcija,&$mala_porcija,&$posto,&$dostava,&$centar)
	{
		connect_db($conn);
		
		$sql = "SELECT * FROM cevapi.parametri where id=0;";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while($row = $result->fetch_assoc()) 
			{
				$posto=$row["porast_vikend_posto"];
				
				$dostava=$row["dostava_posto"];
				$centar=$row["centar_posto"];
				
				$velikih_cevapa=$row["cevapa_u_velikoj"];
				$malih_cevap=$row["cevapa_u_maloj"];
				
				$velika_porcija=$row["prodano_velikih_porcija"];
				$mala_porcija=$row["prodano_malih_porcija"];
			}
		} else {
			echo "Ne postoji konfiguracija!";
			return false;
		}
		$conn->close();
		return true;
	}
	
	function generiraj_podatke($naziv, $godina)
	{
		
		
		dohvati_parametre($cevapa_v,$cevapa_m,$v_porcija,$m_porcija,$posto,$dostava,$centar);

		$u_centru=false;

		connect_db($conn);
	
		$stmt = $conn->prepare("INSERT into cevapi.izvjestaji (naziv, godina, cevapa_velika_porcija, cevapa_mala_porcija, dnevno_velikih, dnevno_malih, dostava_posto, vikend_posto, centar_posto) values ( ? , ? , ?, ?, ?, ?, ? , ? ,? )");
		$stmt->bind_param("siiiiiddd",$naziv,$godina,$cevapa_v,$cevapa_m, $v_porcija, $m_porcija,$dostava,$posto, $centar);

		$stmt->execute();
		
		$izvjestaj_id = $conn->insert_id;
		
		$cevapa_centar = 0;
		$cevapa_periferija=0;
		
		$cevapa_vikendom=0;
		$cevapa_radnim_danom=0;
		
		$sql = "SELECT * FROM cevapi.poslovnice;";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) 
		{
			while($row = $result->fetch_assoc()) 
			{
				$u_centru=boolval($row["centar"]); 
				$naziv=	$row["naziv"];

				$datum=date($godina."-01-01");	
				
				$uk_velikih_porcija=0;
				$uk_malih_porcija=0;
				
				$uk_vikend_malih=0;
				$uk_vikend_velikih=0;
				
				$uk_cevapa=0;
				$uk_dostavljeno_malih=0;
				$uk_dostavljeno_velikih=0;
				
				$ostatak_v=0;
				$ostatak_m=0;
				$ostatak_dv=0;
				$ostatak_dm=0;
				
				$avg_v=0;
				$avg_m=0;
				$postotak = $posto * 0.01;
				$centarposto =$centar * 0.01;
				$dostavapost = $dostava * 0.01;
				
				
				while ($datum < ($godina+1)."-01-01")
				{
					$dan_u_tjednu = date('w', strtotime($datum));
					//echo $datum;
					switch ($dan_u_tjednu)
					{
						case 0: //nedjelja
						case 6: //subota
							//echo " weekend";
							$avg_v= ($v_porcija * $postotak) + $ostatak_v;
							$avg_m= ($m_porcija * $postotak) + $ostatak_m;
							if ($u_centru)
							{
								$avg_v+= ($v_porcija * $centarposto);
								$avg_m+= ($m_porcija * $centarposto);
							}
							$avg_v+= $v_porcija ;						
							$avg_m+= $m_porcija ;
							
							$uk_vikend_velikih += (floor($avg_v));
							$uk_vikend_malih += (floor($avg_m));
						break;
						default:
							//$avg_v= ($v_porcija * $postotak);
							$avg_v= $v_porcija + $ostatak_v;
							$avg_m= $m_porcija + $ostatak_m;
							
							if ($u_centru)
							{
								$avg_v+= ($v_porcija * $centarposto);
								$avg_m+= ($m_porcija * $centarposto);
							}
						break;
					}
										
					$uk_velikih_porcija += floor($avg_v);
					$uk_malih_porcija += floor($avg_m);
					
					$uk_cevapa += $cevapa_v * (floor($avg_v));
					$ostatak_v = $avg_v - floor($avg_v);
					
					$uk_cevapa += $cevapa_m * (floor($avg_m));
					$ostatak_m = $avg_m - floor($avg_m);
					
		
					$dostava_m= (floor($avg_m) * $dostavapost) + $ostatak_dm;
					$dostava_v= (floor($avg_v) * $dostavapost) + $ostatak_dv;
									
					$ostatak_dv = $dostava_v - floor($dostava_v);
					$ostatak_dm = $dostava_m - floor($dostava_m);
			
					$uk_dostavljeno_malih+= (floor($dostava_m));
					$uk_dostavljeno_velikih+= (floor($dostava_v));
	
			
					$datum =  date('Y-m-d',strtotime($datum. ' + 1 days'));			
				}
			
				if ($u_centru)
					$cevapa_centar += $uk_cevapa;
				else
					$cevapa_periferija += $uk_cevapa;
				
				$opis=$naziv;
				
				$stmt = $conn->prepare("INSERT into cevapi.izvjestaj_redci 
				(izvjestaj_id, opis, velikih_porcija, malih_porcija, dostavljeno_velikih_porcija, dostavljeno_malih_porcija, velikih_porcija_vikend, malih_porcija_vikend, centar) 
				values ( ? , ? , ? , ? , ? , ?, ?, ?, ? );");
				$stmt->bind_param("isiiiiiii",$izvjestaj_id, $opis,$uk_velikih_porcija, $uk_malih_porcija,$uk_dostavljeno_velikih,$uk_dostavljeno_malih,$uk_vikend_velikih,$uk_vikend_malih,$row["centar"]);
				$stmt->execute();
			}
			
		}
		$conn->close();

		return $izvjestaj_id;
	}
	

function napravi_izvjestaje($id, $generiraj_dokumente=false)
{
	
	require_once __DIR__ . '/mpdf/vendor/autoload.php';
	
	connect_db($conn);
	if ($generiraj_dokumente==true)	
		$mpdf = new \Mpdf\Mpdf(['setAutoTopMargin' => 'stretch']);
		
	$quary = $conn->prepare("SELECT * FROM cevapi.izvjestaji i where i.id=?");
							
	$quary->bind_param('i',$id);
	$quary->execute();
	
	$result	= $quary->get_result();
	$naziv_izvjestaja="";
	if ($result->num_rows > 0) 
	{
		while($row = $result->fetch_assoc()) 
		{
			$naziv_izvjestaja='Izvještaj za ' . $row["godina"] . '. godinu';
			if ($generiraj_dokumente==true)		
			{
				$mpdf->SetHTMLHeader('<br /><h1 style="text-align: center; font-weight: bold; ">'.$naziv_izvjestaja.'</h1>');
				$mpdf->SetHTMLFooter('<div style="text-align: center; font-style: italic; font-size: 9px;">Dnevno malih porcija: ' . $row["dnevno_malih"] .
				'; Dnevno velikih porcija:  ' . $row["dnevno_velikih"] . '; Mala porcija ćevapa:  ' . $row["cevapa_mala_porcija"] .'; Velika porcija ćevapa:  ' . $row["cevapa_velika_porcija"] .
				'; <br \> Porast vikend:  ' . $row["vikend_posto"] .'%; Porast centar:  ' . $row["centar_posto"] .'%; Dostava postotak:  ' . $row["dostava_posto"] .'%;</div>');
			}
		}
	}
	else 
	{
		echo "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();


	$csv[0] = array('Tip izvještaja', 'Opis', 'Velikih porcija', 'Malih porcija', 'Ćevapa');
	
	//$html ="<html><head><style>
	//		table, th, td { border: 1px solid black; font-size: 12px; }
	//		table { border: 1px solid black; border-collapse: collapse; }
	//		</style></head><body>";
	$html = "";		
	$html = $html. izvjestaj_po_restoranima($id,$csv);
	$html = $html. '<br />';
	$html = $html. izvjestaj_po_periferiji($id, $csv);
	$html = $html. '<br />';
	$html = $html. izvjestaj_prodaje_vikendom($id, $csv);
	$html = $html. '<br />';
	$html = $html. izvjestaj_prodaje_radnim_danom($id, $csv);
	$html = $html. '<br />';
	$html = $html. izvjestaj_prodaje_dostavom($id, $csv);
	$html = $html. '<br />';
	$html = $html. izvjestaj_prodaje_u_restoranu($id, $csv);
	$html = $html. '<br />';
	$html = $html. izvjestaj_prodaje_po_porcijama($id, $csv);
	$html = $html. '<br />';
	//$html = $html. '</body></html>';
	
	if ($generiraj_dokumente==true)	
	{
		$pdfName   = $id.'.pdf';
		if(file_exists($pdfName))
		{
			unlink($pdfName);
		}
		
		$mpdf->WriteHTML($html);
		$mpdf->Output('data/pdf/'.$pdfName, \Mpdf\Output\Destination::FILE);

		$csvName   = $id.'.csv';
		$filePath   = 'data/csv/'.$csvName; 
		
		if(file_exists($filePath))
		{
			unlink($filePath);
		}

		$fp = fopen($filePath, 'w+');
		
		# Now UTF-8 - Add byte order mark 
		fwrite($fp, pack("CCC",0xef,0xbb,0xbf));

		foreach ($csv as $l) {
			fputcsv($fp, $l, ';');
		}

		fclose($fp);
	}
	$html = '<h1 style="text-align: center; font-weight: bold; ">'.$naziv_izvjestaja.'</h1>'.$html;
	return $html;
}

function izvjestaj_prodaje_po_porcijama($id, &$csv=[])
{
	connect_db($conn);
	$html = '<center>PO PORCIJAMA </center>';
	$quary = $conn->prepare("SELECT 'Velikih porcija' as 'opis', sum(r.velikih_porcija) as 'velikih_porcija', 0 as malih_porcija, (r.velikih_porcija ) as 'porcija', sum(r.velikih_porcija * i.cevapa_velika_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union
							SELECT 'Malih porcija' as 'opis', 0 as velikih_porcija, sum(r.malih_porcija) as 'malih_porcija', (r.malih_porcija ) as 'porcija' , sum(r.malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union                   			
							SELECT 'UKUPNO' as 'opis', sum(r.velikih_porcija) as 'velikih_porcija', sum(r.malih_porcija) as 'malih_porcija', sum(r.velikih_porcija + r.malih_porcija ) as 'porcija' ,sum(r.velikih_porcija * i.cevapa_velika_porcija) + sum(r.malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?;");
							
	$quary->bind_param('iii', $id,$id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html. "<center><table style='width:100%;'>";
		$html = $html."<tr><th>Opis</th><th>Porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			
			$linija = array('RADNIM DANOM',  $row["opis"],   $row["velikih_porcija"], $row["malih_porcija"],  $row["cevapa"]);
			array_push($csv, $linija);
			
			
			$html = $html.  "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["porcija"] . "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html. "</table></center>";
	} 
	else 
	{
		echo "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}

function izvjestaj_prodaje_u_restoranu($id, &$csv=[])
{
	
	$html =  '<center>U RESTORANU </center>';
	connect_db($conn);
	
	$quary = $conn->prepare("SELECT r.opis, r.velikih_porcija -  r.dostavljeno_velikih_porcija as velikih_porcija, r.malih_porcija -  r.dostavljeno_malih_porcija  as malih_porcija, ((r.velikih_porcija -  r.dostavljeno_velikih_porcija) * i.cevapa_velika_porcija) + ((r.malih_porcija - r.dostavljeno_malih_porcija) * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union
							SELECT 'UKUPNO', sum(r.velikih_porcija -  r.dostavljeno_velikih_porcija) as velikih_porcija, sum(r.malih_porcija -  r.dostavljeno_malih_porcija) as malih_porcija ,sum(((r.velikih_porcija -  r.dostavljeno_velikih_porcija) * i.cevapa_velika_porcija) + ((r.malih_porcija - r.dostavljeno_malih_porcija) * i.cevapa_mala_porcija)) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?");
							
	$quary->bind_param('ii', $id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html. "<center><table style='width:100%'>";
		$html = $html. "<tr><th>Restoran</th><th>Velikih porcija</th><th>Malih porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			$linija = array('U RESTORANU',  $row["opis"],   $row["velikih_porcija"], $row["malih_porcija"],  $row["cevapa"]);
			array_push($csv, $linija);
			
			$html = $html. "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["velikih_porcija"] ."</td><td>" . $row["malih_porcija"]. "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html. "</table></center>";
	} 
	else 
	{
		echo "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}


function izvjestaj_prodaje_dostavom($id, &$csv=[])
{
	
	$html =  '<center>DOSTAVA </center>';
	connect_db($conn);
	
	$quary = $conn->prepare("SELECT r.opis, r.dostavljeno_velikih_porcija , r.dostavljeno_malih_porcija , (r.dostavljeno_velikih_porcija * i.cevapa_velika_porcija) + (r.dostavljeno_malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union
							SELECT 'UKUPNO', sum(r.dostavljeno_velikih_porcija ), sum(r.dostavljeno_malih_porcija ),sum(r.dostavljeno_velikih_porcija * i.cevapa_velika_porcija) + sum(r.dostavljeno_malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?");
							
	$quary->bind_param('ii', $id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html. "<center><table style='width:100%'>";
		$html = $html. "<tr><th>Restoran</th><th>Velikih porcija</th><th>Malih porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			$linija = array('DOSTAVA',  $row["opis"],   $row["dostavljeno_velikih_porcija"], $row["dostavljeno_malih_porcija"],  $row["cevapa"]);
			array_push($csv, $linija);
			
			$html = $html. "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["dostavljeno_velikih_porcija"] ."</td><td>" . $row["dostavljeno_malih_porcija"]. "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html. "</table></center>";
	} 
	else 
	{
		echo "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}

function izvjestaj_prodaje_radnim_danom($id, &$csv=[])
{
	connect_db($conn);
	$html =  '<center>RADNIM DANOM </center>';
	
	$quary = $conn->prepare("SELECT r.opis,  r.velikih_porcija - r.velikih_porcija_vikend as velikih_porcija, r.malih_porcija - r.malih_porcija_vikend as malih_porcija, ((r.velikih_porcija - r.velikih_porcija_vikend) * i.cevapa_velika_porcija) + ((r.malih_porcija - r.malih_porcija_vikend) * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union
							SELECT 'UKUPNO', sum(r.velikih_porcija -r.velikih_porcija_vikend ), sum(r.malih_porcija -r.malih_porcija_vikend ),sum((r.velikih_porcija - r.velikih_porcija_vikend) * i.cevapa_velika_porcija) + sum((r.malih_porcija - r.malih_porcija_vikend) * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?");
							
	$quary->bind_param('ii', $id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html. "<center><table style='width:100%'>";
		$html = $html. "<tr><th>Restoran</th><th>Velikih porcija</th><th>Malih porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			$linija = array('RADNIM DANOM',  $row["opis"],   $row["velikih_porcija"], $row["malih_porcija"],  $row["cevapa"]);
			array_push($csv, $linija);
			
			$html = $html. "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["velikih_porcija"] ."</td><td>" . $row["malih_porcija"]. "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html. "</table></center>";
	} 
	else 
	{
		echo  "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}

function izvjestaj_prodaje_vikendom($id, &$csv=[])
{
	connect_db($conn);
	$html = '<center>VIKENDOM </center>';
	
	$quary = $conn->prepare("SELECT r.opis, r.velikih_porcija_vikend , r.malih_porcija_vikend , (r.velikih_porcija_vikend * i.cevapa_velika_porcija) + (r.malih_porcija_vikend * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union
							SELECT 'UKUPNO', sum(r.velikih_porcija_vikend ), sum(r.malih_porcija_vikend ),sum(r.velikih_porcija_vikend * i.cevapa_velika_porcija) + sum(r.malih_porcija_vikend * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?");
							
	$quary->bind_param('ii', $id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html. "<center><table style='width:100%'>";
		$html = $html.  "<tr><th>Restoran</th><th>Velikih porcija</th><th>Malih porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			$linija = array('VIKENDOM',  $row["opis"],   $row["velikih_porcija_vikend"], $row["malih_porcija_vikend"],  $row["cevapa"]);
			array_push($csv, $linija);
			$html = $html. "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["velikih_porcija_vikend"] ."</td><td>" . $row["malih_porcija_vikend"]. "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html. "</table></center>";
	} 
	else 
	{
		echo  "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}

function izvjestaj_po_periferiji($id, &$csv=[])
{
	connect_db($conn);
	$html =  '<center>PO PERIFERIJI </center>';
	$quary = $conn->prepare("SELECT IF(r.centar=1, 'Centar', 'Periferija') as 'opis', sum(r.velikih_porcija) as 'velikih_porcija' , sum(r.malih_porcija) as 'malih_porcija' , sum((r.velikih_porcija * i.cevapa_velika_porcija) + (r.malih_porcija * i.cevapa_mala_porcija)) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							group by r.centar
							union
							SELECT 'UKUPNO' as 'opis', sum(r.velikih_porcija ) as 'velikih_porcija' , sum(r.malih_porcija ) as 'malih_porcija' ,sum(r.velikih_porcija * i.cevapa_velika_porcija) + sum(r.malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?");
							
	$quary->bind_param('ii', $id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html.  "<center><table style='width:100%'>";
		$html = $html. "<tr><th>Opis</th><th>Velikih porcija</th><th>Malih porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			
			$linija = array('PO PERIFERIJI',  $row["opis"],   $row["velikih_porcija"], $row["malih_porcija"],  $row["cevapa"]);
			array_push($csv, $linija);
			
			$html = $html. "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["velikih_porcija"] ."</td><td>" . $row["malih_porcija"]. "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html.  "</table></center>";
	} 
	else 
	{
		echo "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}

function izvjestaj_po_restoranima($id, &$csv=[])
{
	connect_db($conn);
	$html = '<center>PO RESTORANIMA</center>';
	$quary = $conn->prepare("SELECT r.opis, r.velikih_porcija , r.malih_porcija , (r.velikih_porcija * i.cevapa_velika_porcija) + (r.malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?
							union
							SELECT 'UKUPNO', sum(r.velikih_porcija ), sum(r.malih_porcija ),sum(r.velikih_porcija * i.cevapa_velika_porcija) + sum(r.malih_porcija * i.cevapa_mala_porcija) as 'cevapa'
							FROM cevapi.izvjestaji i join cevapi.izvjestaj_redci r on r.izvjestaj_id = i.id where i.id=?;");
	$quary->bind_param('ii', $id,$id);
	$quary->execute();
	
	$result	= $quary->get_result();

	if ($result->num_rows > 1) 
	{
		$html = $html.  "<center><table style='width:100%'>";
		$html = $html. "<tr><th>Restoran</th><th>Velikih porcija</th><th>Malih porcija</th><th>Ćevapa</th></tr>";
		while($row = $result->fetch_assoc()) 
		{
			$linija = array('PO RESTORANIMA',  $row["opis"],   $row["velikih_porcija"], $row["malih_porcija"],  $row["cevapa"]);
			array_push($csv, $linija);
			
			$html = $html. "<tr><td><b>" . $row["opis"]. "</b></td><td>" . $row["velikih_porcija"] ."</td><td>" . $row["malih_porcija"]. "</td><td>" .  $row["cevapa"] . "</td></tr>";
		}
		$html = $html. "</table></center>";
	} 
	else 
	{
		echo "Ne postoji konfiguracija!";
		return false;
	}

	$conn->close();
	return $html;
}
?>
	