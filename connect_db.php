 <?php
function connect_db(&$conn)
{ 
	$servername = "localhost";
	$username = "root";
	$password = "";

	$conn = new mysqli($servername, $username, $password);

	if ($conn->connect_error) {
	  die("Connection failed: " . $conn->connect_error);
	  return false;
	}

	return true;
}

?> 