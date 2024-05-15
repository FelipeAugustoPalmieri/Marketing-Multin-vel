<?php
// Show all information, defaults to INFO_ALL
phpinfo();

// Show just the module information.
// phpinfo(8) yields identical results.
phpinfo(INFO_MODULES);
$conn = pg_connect("host=pgsql.tbest.com.br port=5432 dbname=tbest user=admin_db password=admintuningwil");
if (!$conn) {
 echo "An error occurred conn.\n";
 exit;
} else{
echo " base conectado \n "; 
var_dump($conn);
}
$result = pg_query($conn, "SELECT table_name FROM information_schema.tables WHERE table_schema='public' AND table_type='BASE TABLE'");
if (!$result) {
 echo "An error occurred.\n";
 exit;
}
// while ($row = pg_fetch_row($result)) {
//  echo "value1: $row[0]  value2: $row[1]";
//  echo "<br />\n";
// }
$row = pg_fetch_row($result);
var_dump($row);
?>