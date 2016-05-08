<?php

/*
	 _   _   ______    _____
	| \ | |  | ___ \  /  ___|
	|  \| |  | |_/ /  \  `-. 
	| . \`|  |    /    `--, \
	| |\  |  | |\ \   /\__/ /
	\_| \_/[]\_| \_\[]\____/ []

	~._.~._.~._.~._.~._.~._.~._
	....Network ROM Server.....
	_.~._.~._.~._.~._.~._.~._.~

	Version: 0.1 alpha
	Developer: Dal1980 (James Dalgarno)
	Email: james@zombiesbyte.com
	License: MIT
*/

//please read!

//Since this in in alpha (and a work in progress) there's
//not much functionality been created so far. The core class
//holds the the actions method which will probably be the best
//place to start modifying this for your own needs

// don't forget to check the /includes/setup.php for environment
// variables and check the /log/nrslog.txt for all entries

//To start, launch this in cli in dos on windows via "php nrs.php"
//If you have another computer you can telnet to this server here's
//an example of a linux command: (note: 5000 is the port number}

//pipes are used to seperate the cmd:val pairs, you can send as many as you like within limits of the cli input length
//echo "cmd:val|cmd:val..." | nc {network card} {ip} {port}
//echo "auth:MyKeyGoesHere|os:linux" | nc {internal ip of the windows pc that is running this script} 5000

//full documentation will be eventually produced however it's too early in the project to write

// NRSVER: (string) :: Network ROM Server Version
// the current version of Network ROM Server software
// this should be set somewhere else in the program!
define("NRSVER",					"0.1 (alpha)");

//lets require some files
require('\includes\setup.php');
require('\includes\init.php');
require('\includes\core.php');
require('\includes\render.php');

set_time_limit (0);

//instantiate our class
$nrs = new Nrs;

class Nrs{

	public $screenLog = array();
	public $masterLog = array();
	public $toDie = false; //safe exit flag
	public $status = "";

	public $masterSocket = ""; //socket resource
	public $clientSocket = array(); //client sockets
	public $socketPool = array(); //pool of current sockets

	public function __construct()
	{
		//lets gather some information to display	
		$checks = array(
			'(N)etwork (R)OM (S)erver' => NRSVER,
			
			'Break01' => 'break', //breaking space
			
			'Initial Program Checks' => '',
			'Operating system' => OPSYS,
			'UNAME' => php_uname(),
			
			'Break02' => 'break', //breaking space

			'Maximum screen logging entries' => MAXLOGS,
			'Maximum screen character length' => MAXSTRLEN,
			'Program tick time (seconds)' => TICKTOCK / 1000000,
			'Maximum allowed client connections' => MAXCLIENTS,
			'Master Socket Port number' => MASTERSOCKETPORT

			//add more
		);
		
		//build our socket arrays with null
		for($n = 0; $n < MAXCLIENTS; $n++){
			$this->clientSocket[$n] = null;
			$this->socketPool[$n] = null;
		}

		if(!Init::checkSum($checks)) return false;
		else{
			
			for($n = 0; $n < MAXLOGS; $n++){				
				$emptyRecord = "";
				for($i = 0; $i < MAXSTRLEN; $i++) $emptyRecord .= LOGPADCHAR;
				$this->writeLog(array('logs', $emptyRecord), 'screen');
			}
			
			Render::buildTickerFrames(); //setup the ticker animation array

			Render::clearBuffer(); //clear the output buffer
			Render::bufferPass( Core::ec('CLI_CLS') );
			Render::bufferPass( Core::ec('CLI_RCP') );
			Render::bufferPass( Render::buildHeader() );
			Render::bufferPass("%STATUS%"); //placeholder for program status
			Render::bufferPass("%TICKER%"); //placeholder for ticker animation
			Render::bufferPass("%LOGS%"); //placeholder for logs
			Render::bufferPass( Render::buildFooter() );

			//One 'socket' to rule them all
			$this->openMasterSocket();
			$this->updateHUD();
			sleep(1);
			
			//One socket to 'bind' them
			$this->bindMasterSocketPort();
			$this->updateHUD();
			sleep(1);

			//One socket to 'listen' to them all
			$this->listenOnMasterSocket();
			$this->updateHUD();
			sleep(1);

			//..and in the darkness 'find' them...			
			//...off we go on our main ticker loop
			//..without a single hobbit in sight!
			$this->ticker();
		}
	}

	public function ticker()
	{
		while(true){

			$this->readSockets();
			$this->callSocketPool();
			$this->checkNewConnections();
			$this->checkCurrentConnections();

			$this->updateHUD();
			sleep(1);
			
			//The ticker animation is used as a timer to check for death
			if(Render::$tickerIndex == 0  || $this->toDie){
				$this->writeLogFile();
				$this->checkExit();
			}
		}
	}

	public function updateHUD()
	{
		//we don't want to write to the oBuffer directly as we want to keep
		//this template in original form otherwise we will need to regenerate
		//it every time. We use a copy to replace our placeholders with the
		//variable information at each tick.
		$updatedHUDBuffer = Render::$oBuffer;
		$updatedHUDBuffer = str_replace("%STATUS%", Render::setTickerStatus($this->status), $updatedHUDBuffer );
		$updatedHUDBuffer = str_replace("%TICKER%", Render::getTickerFrame(), $updatedHUDBuffer );
		$updatedHUDBuffer = str_replace("%LOGS%", Render::buildScreenLogs($this->screenLog), $updatedHUDBuffer );
		
		echo $updatedHUDBuffer; //send it to the terminal interface
	}

	public function openMasterSocket()
	{
		if(!($this->masterSocket = socket_create(AF_INET, SOCK_STREAM, 0)))
		{
			$errorcode = socket_last_error();
			$errormsg = str_replace("\r\n", "", socket_strerror($errorcode));
			
			$this->writeLog(array('notice', "Master socket fail! [{$errorcode}] {$errormsg}"));
			$this->toDie = true;
		}
		else{
			$this->writeLog(array('logs', "Master socket created!"));
			$this->status = "opening";
		}
	}

	public function bindMasterSocketPort()
	{
		// Bind the source address
		if(!socket_bind($this->masterSocket, $address , MASTERSOCKETPORT) )
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);

			$this->writeLog(array('notice', "Could not bind master socket! [{$errorcode}] {$errormsg}"));
			$this->toDie = true;
		}
		else{
			$this->writeLog(array('logs', "Master socket bound to port " . MASTERSOCKETPORT . "!"));
			$this->status = "binding";
		}
	}

	public function listenOnMasterSocket()
	{
		if(!socket_listen($this->masterSocket, 10))
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);

			$this->writeLog(array('notice', "Could not listen on master socket! [{$errorcode}] {$errormsg}"));
			$this->toDie = true;
		}
		else{
			$this->writeLog(array('logs', "Now listening on master socket!"));
			$this->status = "listening";
		}
	}

	public function readSockets()
	{
		$this->status = "listening";
		
		//prepare array of readable client sockets
		$this->socketPool = array();
 
 		//first socket is the master socket
		$this->socketPool[0] = $this->masterSocket;
		 
		//now add the existing client sockets
		for($i = 0; $i < MAXCLIENTS; $i++){
			if($this->clientSocket[$i] != null){
				$this->socketPool[ $i + 1 ] = $this->clientSocket[$i];
			}
		}
	}

	public function callSocketPool()
	{
		//now call select - blocking call
		//if(socket_select($this->socketPool , $write, $except , null) === false) //null doesn't allow for tick!
		if(socket_select($this->socketPool , $write, $except , 0) === false)
		{
			$errorcode = socket_last_error();
			$errormsg = socket_strerror($errorcode);

			$this->writeLog(array('notice', "Could not listen on socket pool! [{$errorcode}] {$errormsg}"));
			$this->toDie = true;
		}
	}

	public function checkNewConnections()
	{
		//if ready contains the master socket, then a new connection has come in
		if(in_array($this->masterSocket, $this->socketPool)){
			for($i = 0; $i < MAXCLIENTS; $i++){
				
				if($this->clientSocket[$i] == null){
					
					$this->clientSocket[$i] = socket_accept($this->masterSocket);
					 
					//display information about the client who is connected
					if(socket_getpeername($this->clientSocket[$i], $address, $port)){
						$this->writeLog(array('good', "Client {$address} : {$port} is now connected"));
						$this->status = "connecting";
					}
					 
					//Send Welcome message to client
					$message = "Shaking Hands...\n";
					socket_write($this->clientSocket[$i] , $message);
					break;
				}
			}
		}
	}

	public function checkCurrentConnections()
	{
		//check each client if they send any data
		for($i = 0; $i < MAXCLIENTS; $i++){
			
			if(in_array($this->clientSocket[$i] , $this->socketPool)){

				$input = socket_read($this->clientSocket[$i] , 1024);
				 
				if($input == "" or $input == null){
					//zero length string meaning disconnected, remove and close the socket
					socket_close($this->clientSocket[$i]);
					$this->clientSocket[$i] = null;
				}
	 			else{

	 				$logInput = trim($input);

	 				$cmdArray = $this->unpackSocketComms($input);
	 				
	 				$this->core = new Core;
	 				$this->core->actions($cmdArray, $logInput);
					 
					$this->status = "responding";
					$this->writeLog(array('good', "Sending confirm to client: [OK] " . date('Y-m-d H:i:s')));
					
					//send response to client
					if(!empty($this->clientSocket[$i])) socket_write($this->clientSocket[$i] , $output);
				}
			}
		}
	}

	public function unpackSocketComms($socketInput)
	{
		
		$cmdArray = array();
		$cmdValsArray = explode('|', $socketInput);

		foreach($cmdValsArray as $cmdVal){
	 		$explodedCmdVal = explode(':', $cmdVal);
			
	 		for($n = 0; $n < count($explodedCmdVal); $n+=2){
	 			$explodedCmdVal[$n] = trim($explodedCmdVal[$n]);
	 			$explodedCmdVal[$n + 1] = trim($explodedCmdVal[$n + 1]);
				$cmdArray[ $explodedCmdVal[$n] ] = $explodedCmdVal[$n + 1];
			}
		}
		return $cmdArray;
	}

	public function writeLog($logEntry, $dest = 'both')
	{
		if($dest == 'both'){
			$this->screenLog[] = $logEntry;

			$logEntry[1] = "[" . date("Y-m-d H:i:s") . "] " . $logEntry[1];
			$this->masterLog[] = $logEntry;
		}
		else if($dest == 'screen'){
			$this->screenLog[] = $logEntry;
		}
		else if($dest == 'master'){
			$logEntry[1] = "[" . date("Y-m-d H:i:s") . "] " . $logEntry[1];
			$this->masterLog[] = $logEntry;
		}
	}

	public function writeLogFile()
	{
		$filename = LOGFILE;
		$somecontent = "Add this to the file\n";

	    // In our example we're opening $filename in append mode.
	    // The file pointer is at the bottom of the file hence
	    // that's where $somecontent will go when we fwrite() it.
	    if (!$handle = fopen($filename, 'a')){
	    	$this->writeLog(array('notice', "Could not open log file {$filename}"));
			$this->toDie = true;
	    }

	    // Write to our opened file.
	    foreach($this->masterLog as $entry){
	    	if (fwrite($handle, $entry[1] . "\n") === FALSE){
	        	$this->writeLog(array('notice', "Could not write to log file {$filename}"));
				$this->toDie = true;
	        }
	    }

	    $this->masterLog = array(); //reset the masterLog array
	    fclose($handle);

	}

	public function checkExit()
	{
		if(!$this->toDie){
			$choice = system('CHOICE /C CQ /N /T 1 /D C');
			if($choice == 'Q') die();
			else{
				echo Core::ec('CLI_CLS');
				echo Core::ec('CLI_RCP');				
			}
		}
		else die(Core::ec('RSTYLE') . "Error (see above or in the logs)");

		return true;
	}

	public function __destruct()
	{
		echo Core::ec('RSTYLE');
		echo "\nProgram exiting!\n";
	}


}

