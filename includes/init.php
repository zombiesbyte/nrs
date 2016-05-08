<?php


class Init{

	/**
	* checkSum runs through some basic checking and outputs some
	* program configurations
	* @param $array (associative string array) "{title}" => "{value}"
	* @return bool
	*/
	public static function checkSum($checks)
	{
		Render::clearBuffer(); //clear the output buffer

		echo Render::bufferPass( Core::ec('CLI_CLS') );
		echo Render::bufferPass( Core::ec('CLI_RCP') );

		foreach($checks as $title => $value){
			
			if($value != "break"){
				echo Render::bufferPass( Core::ec('TICKER_LABEL_STYLES','logs') );
				echo Render::bufferPass( $title );
				echo Render::bufferPass( Core::ec('TICKER_LABEL_STYLES','plain') );

				for($n = 0; $n < (INITMAXSTRLEN - strlen($title) - strlen($value) - 2); $n++){
					usleep(5000);
					echo Render::bufferPass( '.' );
				}

				if($value != ""){
					echo Render::bufferPass( Core::ec('TICKER_LABEL_STYLES','plain') . "[" );
					echo Render::bufferPass( Core::ec('TICKER_LABEL_STYLES','title') );
					echo Render::bufferPass( $value );
					echo Render::bufferPass( Core::ec('TICKER_LABEL_STYLES','plain') . "]\n" );
				}
				else echo Render::bufferPass("..\n");

				usleep(ISLEEP);
			}
			else{
				echo Render::bufferPass("\n");
			}
		}

		echo Core::ec('TICKER_LABEL_STYLES','plain');
		echo "\n\n[(C)ontinue], (Q)uit or (W)ait (auto continues in 5 seconds)\n";
		$choice = system('CHOICE /C CQW /N /T 5 /D C');
		
		if($choice == 'Q') return false;
		if($choice == 'W'){
			echo Render::$oBuffer;
			echo "Press any key to continue\n";
			system('PAUSE');
		}
		else{
			echo Render::bufferPass( Core::ec('TICKER_LABEL_STYLES','title') );
			for($i = 0; $i < 3; $i++){
				for($n = 0; $n < count(TICKER_SSPINNER); $n++){
					usleep(50000);
					echo Render::$oBuffer;
					echo TICKER_SSPINNER[$n];
				}
			}
		}		

		return true;
	}

}