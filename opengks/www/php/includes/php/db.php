<?php

define("DB_NAME","opengks");
define("DB_LOGIN","opengks");
define("DB_PASSWORD",'gksadmin');
mysql_connect("localhost",DB_LOGIN, DB_PASSWORD);

class database
{
	var $Result;

	/*
	* Initialize
	*/
	function database(){

	}
	function db_num_rows()
	{
        return mysql_num_rows($this->rs);
    }
    
    function db_fetch_array()
    {
    	return mysql_fetch_array($this->rs);
    }
        
    function db_free_result()
    {
    	return mysql_free_result($this->rs);
    }    
	
	function runQuery($query)
	{

		mysql_select_db(DB_NAME) or exit(mysql_error());
		$q_result = mysql_query($query);

		return $q_result;
	}

	function freeResult()
	{

		return mysql_freeResult($this->Result);
	}

	function getResultVector($sql)
	{

		$ReturnArr = array();

		$this->Result = $this->runQuery($sql);

		if ($this->Result && mysql_num_rows($this->Result)!=0)
		{
			while($Row = mysql_fetch_array($this->Result))
			{
				$ReturnArr[] = $Row[0];
			}
		}
		$this->freeResult();

		return $ReturnArr;

	}

	

}

?>
