<?php
include "connect_db.php";

$id=null;
$naziv="";
$adresa="";
$grad="";
$centar=0;

	connect_db($conn);

	$quary = $conn->prepare("SELECT id, naziv, adresa, grad, IF(centar=1, 'checked', '') as centar  FROM cevapi.poslovnice where id=?");
							
	$quary->bind_param('i',$_GET['id']);
	$quary->execute();
	$result	= $quary->get_result();
	
if ($result->num_rows > 0) 
{
	while($row = $result->fetch_assoc()) {
			$id=$row["id"];
			$naziv=$row["naziv"];
			$adresa=$row["adresa"];
			$grad=$row["grad"];
			$centar=$row["centar"];
	}
}
$conn->close();

?>

		<form method="post" action="spremi_restoran.php">
		
	<input type="hidden" name="id"  value="<?php echo $id;?>" />
		
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
			<span class="input-group-text" id="basic-addon3">Naziv restorana</span>
		  </div>
		  <input type="text" name="naziv" class="form-control" id="naziv_restorana" aria-describedby="basic-addon3" value="<?php echo $naziv ?>">
		</div>
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
			<span class="input-group-text" id="basic-addon3">Adresa</span>
		  </div>
		  <input type="text" name="adresa" class="form-control"  aria-describedby="basic-addon3" value="<?php echo $adresa;?>">
		</div>
		<div class="input-group mb-3">
		  <div class="input-group-prepend">
			<span class="input-group-text" id="basic-addon3">Grad</span>
		  </div>
		  <input type="text" name="grad" class="form-control"  aria-describedby="basic-addon3" value="<?php echo $grad;?>">
		</div>
		
		<div class="custom-control custom-checkbox">
		  <input name="cb_centar" value='0' type="checkbox" class="custom-control-input" id="cb_centar" <?php echo $centar;?> >
		  
		  <label class="custom-control-label" for="cb_centar">Restoran u centru</label>
		</div>
		<br />
		<button type="button" class="btn btn-outline-primary" onclick="hideFormModal();">Odustani</button>
		<input class="btn btn-primary" type="button" value="Spremi restoran" onclick="post_restoran(this);">
		</form>