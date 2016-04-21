<html>
<body>
<?php

    //Your logon info.
    $host = 'localhost';
    $user = 'YOUR_USERNAME';
    $password = 'YOUR_PASSWORD';

    $db = 'THE_DATABASE';
    $table = 'THE_TABLE';

    //Alert if any connection issues.
    $connect = mysqli_connect($host, $user, $password, $db);
        
    if (mysqli_connect_errno())   {

        die("Connection failure.");
    }

    if (!mysqli_select_db($connect, $db))    {
    
        die("(At mysqli_select_db) Could not find database.");
    }

    //The query that selects all data in the table.
    $query = mysqli_query($connect, "SET CHARACTER SET utf8;");
    $query = mysqli_query($connect, "SELECT * FROM {$table};");

    if (!$query) {

        die("(At mysqli_query) Query failed."); 
     }


/*-------------------------------------------------------------------------------*/

//Transform the table to JSON, store it, then decode from JSON back to an array.

    //Create an array that stores the table to be turned to JSON.
    $temporaryArray = array();
 
    //For each row, fetch the row and push it onto the end of the temporary array.
    while($row = mysqli_fetch_assoc($query))   {
 
        $temporaryArray[] = $row;    //Better than array_push.
    }

    //Convert the array to JSON and store the result.
    $JSONFromArray =  json_encode($temporaryArray);

    //Notify of any encoding problems e.g. utf-8 issues. 
    if (json_last_error() != 0)   {

        echo json_last_error_msg();
    }

    //Output the JSON to a file.
    $file = fopen("/.../JSONoutput", w);
    fwrite($file, $JSONFromArray);
    fclose($file);

    echo "The full table, in JSON format, is stored in file JSONoutput.<br><br>";

    //Convert back from JSON to array.
    $arrayFromJSON = json_decode($JSONFromArray, true);

    //As an exercise, change the (possibly unknown) keynames to numerical indices.
    $keyArray = array_keys($arrayFromJSON[1]);
    $cellValue =  $arrayFromJSON[10][$keyArray[2]]; 

    //Output a cell value to demonstrate that working smoothly.
    echo "The value at position [10][2] (found by 'deconstructing' the JSON output) is $cellValue.<br><br>";

    //Send the result pointer back to the beginning to allow further work.
    mysqli_data_seek ($query, 0);


/*-------------------------------------------------------------------------------------*/


    //Output the table to the screen.

    //A table heading.
    echo "<i> The full table </i> <b>'{$table}'</b> <i> is:</i><br><br>";

    //Total number of column headings.
    $colSum = mysqli_num_fields($query);

    //Table design.
    echo "<table border='1'>";

    //Output the column headings.
    echo "<tr>";

    for($i = 0; $i < $colSum; $i++)   {

        $col = mysqli_fetch_field($query);

        echo "<td> {$col -> name} </td>";
    }

    echo "</tr>\n";
 
    //Output all the rows.
    $row = mysqli_fetch_row($query);

    while($row)   {

        echo "<tr>";

        for($i = 0; $i < count($row); $i++)   {  //Or foreach.

            echo "<td>$row[$i]</td>";
        }

        echo "</tr>\n";

        $row = mysqli_fetch_row($query);

    }
    
    //Free memory associated with $query.
    mysqli_free_result($query);

?>

</body>
</html>

