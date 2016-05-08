<?php

//class used for rendering output

class Render{

	//output buffer variable
	public static $oBuffer;
	public static $tickerFrames = array(); //pre-rendered output array
	public static $tickerIndex = 0;
	public static $tickerFrameCount = 0;
	/**
	* clearBuffer: clears the static variable $oBuffer
	* @return none
	*/
	public static function clearBuffer()
	{
		self::$oBuffer = "";
	}

	/**
	* bufferPass: a passive function that records the $content in the
	* $oBuffer (output buffer) variable and returns the original content
	* @param string $content
	* @return $string (unmodified)
	*/
	public static function bufferPass($content)
	{
		self::$oBuffer .= $content;
		return $content;
	}

	/**
	* buildHeader: a method that creates and packs up the header
	* @return $string containing the header
	*/
	public static function buildHeader()
	{
		$header = "";

		//lets get our logo
		$header .= self::formatStrings(Core::ec('TICKER_LABEL_STYLES', 'logo'), GOPRE . HOPRE, HUD_LOGO, HOSUF . GOSUF, ' ', STR_PAD_BOTH);
		
		//lets get our subheading
		$header .= self::formatStrings(Core::ec('TICKER_LABEL_STYLES', 'subhead'), GOPRE . HOPRE, HUD_SUBHEAD, HOSUF . GOSUF, ' ', STR_PAD_BOTH);

		//lets get our notice
		$header .= self::formatStrings(Core::ec('TICKER_LABEL_STYLES', 'notice'), GOPRE . HOPRE, HUD_NOTICE, HOSUF . GOSUF, ' ', STR_PAD_BOTH);

		return $header;
	}

	/**
	* buildFooter: a method that creates and packs up the footer
	* @return $string containing the footer
	*/
	public static function buildFooter()
	{
		$footer = "";

		//lets get our footer
		$footer .= self::formatStrings(Core::ec('TICKER_LABEL_STYLES', 'footer'), GOPRE . FOPRE, HUD_FOOTER, FOSUF . GOSUF, ' ', null);

		return $footer;
	}

	/**
	* buildScreenLogs: a method that creates and packs up the log entries
	* @param $logArray the full array to be extracted and formatted
	* @return $string containing the log entries
	*/
	public static function buildScreenLogs($logArray)
	{
		$screenLog = "";

		//lets extract our log array
		for($n = count($logArray) -1; $n > (count($logArray) - MAXLOGS); $n--){
			$screenLog .= self::formatStrings(Core::ec('TICKER_LABEL_STYLES', $logArray[$n][0] ), GOPRE . LOPRE, $logArray[$n][1], LOSUF . GOSUF);
		}

		return $screenLog;
	}

	/**
	* buildTickerFrames: a method that creates the ticker animation and packs it
	* into an array for future use in the program
	* @return null sets the static array $tickerFrames
	*/
	public static function buildTickerFrames()
	{
		$tickerFrames = array();
		$tickerTemp = "";
		self::$tickerFrameCount = count(TICKER_SPINNER);

		$n = 0;

		foreach(TICKER_SPINNER as $tickerString){
			
			$n++;
			//pad out the ticker string so it fits our defined widths
			$tickerString = self::formatStrings('','', $tickerString,'');
			//we need to remove the spaces so that our quit label can be added as well as our [] brackets
			$tickerString = substr($tickerString, 0, - ( strlen( Core::ec('TICKER_QUIT', '0') ) + 2 ) );

			//lets break up our frame as singe characters so we can individually style them
			$tickerCharArray = str_split($tickerString, 1);

			$tickerTemp = "";
			foreach($tickerCharArray as $tChar){
				$tickerTemp .= Core::ec('TICKER_SPINNER_STYLE', $tChar);
				$tickerTemp .= $tChar;
			}

			$prefix = Core::ec('TICKER_LABEL_STYLES', 'logs') . GOPRE . TOPRE;
			$suffix = Core::ec('TICKER_LABEL_STYLES', 'logs') . TOSUF . GOSUF;
			$lftBrk = Core::ec('TICKER_LABEL_STYLES', 'plain') . "[";
			$rgtBrk = Core::ec('TICKER_LABEL_STYLES', 'plain') . "]";

			if($n < self::$tickerFrameCount) $quitLabel = Core::ec('TICKER_QUIT_STYLE', '0') . Core::ec('TICKER_QUIT', '0');
			else $quitLabel = Core::ec('TICKER_QUIT_STYLE', '1') . Core::ec('TICKER_QUIT', '1');
			//lets space the ticker out a bit and generate a new line
			$newline = self::formatStrings(Core::ec('TICKER_LABEL_STYLES', 'logs' ), GOPRE . TOPRE, '', TOSUF . GOSUF);

			//add the whole thing together and update our array
			$tickerFrames[] = $prefix . $tickerTemp . $lftBrk . $quitLabel . $rgtBrk . $suffix . $newline;
		}

		self::$tickerFrames = $tickerFrames;
	}

	/**
	* getTickerFrame: a method that gets the current ticker frame
	* @return string $tickerFrame
	*/
	public static function getTickerFrame()
	{
		$tickerFrame = self::$tickerFrames[ self::$tickerIndex ];

		self::$tickerIndex++;
		if(self::$tickerIndex >= count(TICKER_SPINNER)) self::$tickerIndex = 0;

		return $tickerFrame;
	}
	
	/**
	* setTickerStatus: a method that gets the current ticker program status
	* 
	* @return string $tickerStatus
	*/
	public static function setTickerStatus($statusTxt)
	{
		//lets space the ticker status label out a bit and generate a new line
		$newline = self::formatStrings(Core::ec('TICKER_LABEL_STYLES', 'logs' ), GOPRE . TOPRE, '', TOSUF . GOSUF);

		//lets centre align the status label
		$statusLabel = str_pad(TICKER_STATUS[ $statusTxt ]['label'], MAXSTRLEN, ' ', STR_PAD_BOTH);

		$tickerStatus = TICKER_STATUS[ $statusTxt ]['colour'] . GOPRE . TOPRE . $statusLabel . TOSUF . GOSUF . $newline;

		return $tickerStatus;
	}
	

	/**
	* formatStrings: loops through a string array applying the styles
	* @param string $lineColour
	* @param string $prefix
	* @param array $strArray | string $strArray
	* @param string $suffix
	* @return string formatted and styled
	*/
	public static function formatStrings($lineColour, $prefix, $strArray, $suffix, $paddingChr = ' ', $padType = STR_PAD_RIGHT)
	{
		$strung = "";

		if(is_array($strArray)){
			foreach($strArray as $strLine){
				
				$strLength = strlen($strLine);

				if($strLength < MAXSTRLEN){
					$strLine = str_pad($strLine, MAXSTRLEN, $paddingChr, $padType);
					$strung .= $lineColour . $prefix . $strLine . $suffix;
				}
				else if($strLength > MAXSTRLEN){
					$strSplit = array();
					$strSplit = str_split($strLine, MAXSTRLEN);
					
					foreach($strSplit as $s){
						$s = str_pad($s, MAXSTRLEN, $paddingChr, $padType);
						$strung .= $lineColour . $prefix . $s . $suffix;
					}
				}
				else $strung .= $lineColour . $prefix . $strLine . $suffix;
			}
		}
		else{
			$strLength = strlen($strArray);

			if($strLength < MAXSTRLEN){
				$strArray = str_pad($strArray, MAXSTRLEN, $paddingChr, $padType);
				$strung .= $lineColour . $prefix . $strArray . $suffix;
			}
			else if($strLength > MAXSTRLEN){
				$strSplit = array();
				$strSplit = str_split($strArray, MAXSTRLEN);
				
				foreach($strSplit as $s){
					$s = str_pad($s, MAXSTRLEN, $paddingChr, $padType);
					$strung .= $lineColour . $prefix . $s . $suffix;
				}
			}
			else $strung .= $lineColour . $prefix . $strArray . $suffix;
		}

		return $strung;
	}


}