<?php require('include/dbcon.php'); $res = mysqli_query($con, 'SHOW TABLES'); while($row = mysqli_fetch_array($res)) { if(stripos($row[0], 'order') !== false) { echo $row[0] . "\n"; } } ?>
