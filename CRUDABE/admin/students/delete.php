<?php
  session_start();
  include "../../config/database.php";

  if(!isset($_SESSION["role"]) || $_SESSION["role"] != "admin"){
    header("location: ../../index.php");
    exit;
}

$id = isset($_GET["id"]) ? intval($_GET["id"]) : 0;

//Delete Sql Command
mysqli_query($conn, "DELETE FROM users WHERE id=$id and role='student'");

header("Location: index.php");
exit;
?>
