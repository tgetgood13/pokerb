<?php

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


function db_select($query)
{
        $result=mysql_query($query);
        return($result);
}

function db_insert($query)
{
        mysql_query($query);
        return mysql_insert_id();
}

function db_update($query)
{
        mysql_query($query);
        return true;
}

function db_delete($query)
{
        mysql_query($query);
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
