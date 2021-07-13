<?php

backup_tables('', '', '', '');
// backup_tables('localhost', 'root', '', 'radio17', '*');

/* backup the db OR just a table */
function backup_tables($host, $user, $pass, $name, $tables = '*', $columns = '*') {
	$connection = mysqli_connect($host, $user, $pass, $name);
	
	//get all of the tables
	if ($tables == '*') {
		$tables = [];
		$result = mysqli_query($connection, 'SHOW TABLES');
		while($row = mysqli_fetch_row($result)) {
			$tables[] = $row[0];
		}
	} else {
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}

	$return = '';
	
	//cycle through
	foreach ($tables as $table) {
		$result = mysqli_query($connection, 'SELECT ' . $columns . ' FROM '. $table);
		$num_fields = mysqli_num_fields($result);

		$return .= 'DROP TABLE ' . $table . ';';
		$row2 = mysqli_fetch_row(mysqli_query($connection, 'SHOW CREATE TABLE '.$table));
		$return .= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) {
			while ($row = mysqli_fetch_row($result)) {
				$return .= 'INSERT INTO '.$table.' VALUES(';
				for ($j = 0; $j < $num_fields; $j++) {
					$row[$j] = addslashes($row[$j]);
					$row[$j] = str_replace("\n", "\\", $row[$j]);
					if (isset($row[$j])) {
					    $return .= '"' . $row[$j] . '"' ;
					} else {
					    $return .= '""';
					}
					if ($j < ($num_fields-1)) { $return.= ','; }
				}
				$return .=  ");\n";
			}
		}
		$return .= "\n\n\n";
	}
	
	//save file
	$handle = fopen('db-backup-' . time() . '-' . (md5(implode(',', $tables))) . '.sql', 'w+');
	fwrite($handle, $return);
	fclose($handle);
}