<?php


class Core Extends Nrs{

	public function __construct()
	{
		//nothing needed here
	}

	/**
	* cmdArray: used for core actions such as ack, file copying, querying data etc
	* The method is designed be be as open as possible to add your own functionality
	* @param array containing "cmd" => "value" pairs.
	* @param string contains the logInput gathered from the socket comms
	* @return null;
	*/
	public function actions($cmdArray, $logInput)
	{
		foreach($cmdArray as $cmd => $val){

			if($cmd == "auth"){
					if($val == NRSKEY) parent::writeLog(array('good', "[{$cmd}:{$val}] Authentication good :)" . $logInput));
					else parent::writeLog(array('bad', "[{$cmd}:{$val}] Authentication bad :(" . $logInput));
				}
			else if($cmd == "os"){
				if($val == "linux") parent::writeLog(array('good', "[{$cmd}:{$val}] Hey, they are on Linux" . $logInput));
				else if($val == "windows") parent::writeLog(array('good', "[{$cmd}:{$val}] Hey, they are on Windows" . $logInput));
				else parent::writeLog(array('good', "[{$cmd}:{$val}] Hey, I don't know what OS they're on!" . $logInput));
			}

			//more commands
		}
	}

	/**
	* alias of php's contant() function
	* c (constant) returns the value of a constant.
	* @param string $cVar a string containing the name of the constant
	* @return bool
	*/
	public static function c($cVar)
	{
		return constant($cVar);
	}

	/**
	* alias of php's contant() function with additional checks
	* ec (environment constant) variable returns the value of a constant
	* if defined otherwise tries to find the constant based on the operating
	* system using the prefix constant OS.
	* @param string $cVar a string containing the name of the constant
	* @param string $d1 a string containing the name of the constant dimension 1
	* @param string $d2 a string containing the name of the constant dimension 2
	* @return mixed (depending on variable type)
	*/
	public static function ec($cVar, $d1 = null, $d2 = null)
	{
		if(defined($cVar)){
			if($d2 != null and $d1 != null and constant($cVar)[ $d1 ][ $d2 ]) return constant($cVar)[ $d1 ][ $d2 ];
			if($d1 != null and constant($cVar)[ $d1 ]) return constant($cVar)[ $d1 ];
			else return constant($cVar);
		}
		else if(constant(OS . $cVar)){
			if($d2 != null and $d1 != null and constant(OS . $cVar)[ $d1 ][ $d2 ]) return constant(OS . $cVar)[ $d1 ][ $d2 ];
			if($d1 != null and constant(OS . $cVar)[ $d1 ]) return constant(OS . $cVar)[ $d1 ];
			else return constant(OS . $cVar);
		}
		else die("Error: unknown constant '{$cVar}' or '" . OS . "{$cVar}'");
	}

}