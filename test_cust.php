<?php require('include/dbcon.php'); $res = mysqli_query($con, 'DESCRIBE customer'); while($row = mysqli_fetch_array($res)) { echo $row[0] . ' '; } echo "\n"; ?>
