<?php

function mysql_result($res, $row=0, $col=0) { 
        $numrows = mysqli_num_rows($res); 
        if ($numrows && $row <= ($numrows-1) && $row >=0) {
                mysqli_data_seek($res,$row);
                $resrow = (is_numeric($col)) ? mysqli_fetch_row($res) : mysqli_fetch_assoc($res);
                if (isset($resrow[$col])) {
                        return $resrow[$col];
                }
        }
        return false;
}

function db_checkString($data)
{
        return "'".addSlashes($data)."'";
}

function db_checkStringForLIKE($data)
{
        return db_checkString("%".str_replace(array('%','_'), array('\\%','\\_'), $data)."%");
}

function db_checkInt($data)
{
        return intVal($data);
}

function db_checkDouble($data)
{
        return doubleVal($data);
}


function db_select($db, $query)
{
        $result=mysqli_query($db, $query);
        return($result);
}

function db_insert($db, $query)
{
        mysqli_query($db, $query);
        return mysqli_insert_id($db);
}

function db_update($db, $query)
{
        mysqli_query($query);
        return true;
}

function db_delete($db, $query)
{
        mysqli_query($db, $query);
        return true;
}

function post_checkInt($v)
{
	if(isset($_POST[$v])) return $_POST[$v];	
	return -1;
}

function post_checkDouble($v)
{
	if(isset($_POST[$v])) return $_POST[$v];	
	return -1;
}

function post_checkString($v)
{
	if(isset($_POST[$v])) return $_POST[$v];	
	return "";
}
?>
