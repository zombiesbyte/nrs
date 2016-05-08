<?php

// Setup file
// Most of the settings here can be customised to your liking
// however the structure of the settings cannot be modified.
// Some settings have multiple entries for various operating systems
// Key
// ! = required in it's present form with static options
// ~ = additional options or further customisation offered


// FYI:
// Colour Chart (note: that this may be different depending upon your CLI settings)

// Windows
// {\033}{[}{#m} = {code escape char} {esc sequence char} {colour ref / command}
// "\033[30m" :: black txt, black bg
// "\033[31m" :: red txt, black bg
// "\033[32m" :: green txt, black bg
// "\033[33m" :: yellow txt, black bg
// "\033[34m" :: blue txt, black bg
// "\033[35m" :: purple txt, black bg
// "\033[36m" :: teal txt, black bg
// "\033[37m" :: white txt, black bg

// "\033[41m" :: white txt, red bg
// "\033[42m" :: white txt, green bg
// "\033[43m" :: white txt, yellow bg
// "\033[44m" :: white txt, blue bg
// "\033[45m" :: white txt, purple bg
// "\033[46m" :: white txt, teal bg
// "\033[47m" :: white txt, white bg


// !
// NRSKEY: (string) {A-Za-z0-9} :: Network Rom Server Key
// defines the security key to allow NRS to talk with clients
// change this key to keep it all nice and secure
define("NRSKEY",						"MyKeyGoesHere");

// !
// OPSYS: (string) {windows|linux} :: operating system
// defines the operating system which the nrs server is running on.
// difference for running commands  on windows vs linux
define("OPSYS",							"windows");

// !
// MAXCLIENTS: (int) {default 10} :: Maximum Clients
// defines the maximum allowed clients to be connected
// increase this depending on requirements
define("MAXCLIENTS",					"10");

// !
// SOCKETPORT: (int) {default 5000} :: Socket Port
// defines the port used when binding the socket
define("MASTERSOCKETPORT",				"5000");

// !
// LOGFILE: (string) {default /log/nrslog.txt} :: Log File
// log files records actions as well as errors returned by the program
define("LOGFILE",						dirname(__FILE__) . "\..\log\\nrslog.txt");

// !
// we now store a shorthand expression for the (O)pperating (s)ytem
// so that we can use particular styling as per the OS or die on error
if(OPSYS == "windows") define("OS",		"WIN_");
else if(OPSYS == "linux") define("OS",	"LNX_");
else die('Error: /includes/setup.php contains an undefined OPSYS value');

// !
// RSTYLE: (string) :: Reset Style
// reset style default
// resets the output style back to the console default which stops
// the style bleeding out over other system outputs such as at the
// point of program termination.
define("WIN_RSTYLE",					"\033[37m");
define("LNX_RSTYLE",					"");

// !
// ISLEEP: (int) :: Init sleep time
// set the delay for the init checksum report output
// this is the micro time between each check
// note: 1000000 (1,000,000) = 1 second
define("ISLEEP",						"100000");

// !
// TICKTOCK: (int) :: Tick Tock
// set the delay for the standard ticker frame
// this is the micro time between each ticker frame
// note: 1000000 (1,000,000) = 1 second
define("TICKTOCK",						"1000000");

// !
// MAXLOGS: (int) :: maximum logs
// defines the maximum log entries to show on screen
// this is for the visual logs only. all log entries are still recorded.
define("MAXLOGS",						10);

// !
// LOGPADCHAR: (char) :: Log Padding Character
// defines a single character to use to fill/pad a screen log entry
// this is for the visual logs only. Please only use a single character
define("LOGPADCHAR",					'.');

// !
// INITMAXSTRLEN: (int) :: init maximum string length
// defines the maximum string length for init checks
// this settings keeps the interface output consistent in line widths for
// the init checksum report.
define("INITMAXSTRLEN",					60);

// !
// MAXSTRLEN: (int) :: maximum string length
// defines the maximum string length before wrapping
// this settings keeps the interface output consistent in line widths and
// should match the header output width ideally.
define("MAXSTRLEN",						60);

// !
// GOPRE: (string) :: Global Output Prefix
// global output prefix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// outputs within the cli interface (start of line).
define("GOPRE",							"  ");

// !
// GOSUF: (string) :: Global Output Suffix
// global output suffix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// outputs within the cli interface (end of line).
define("GOSUF",							"  \n");

// !
// HOPRE: (string) :: Header Output Prefix
// header output prefix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// header outputs within the cli interface (start of line).
define("HOPRE",							"  ");

// !
// HOSUF: (string) :: Header Output Suffix
// header output suffix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// header outputs within the cli interface (end of line).
define("HOSUF",							"  ");

// !
// LOPRE: (string) :: Header Output Prefix
// log output prefix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// log outputs within the cli interface (start of line).
define("LOPRE",							"> ");

// !
// LOSUF: (string) :: Log Output Suffix
// log output suffix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// log outputs within the cli interface (end of line).
define("LOSUF",							"  ");

// !
// TOPRE: (string) :: Ticker Output Prefix
// ticker output prefix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// ticker outputs within the cli interface (start of line).
define("TOPRE",							"  ");

// !
// TOSUF: (string) :: Ticker Output Suffix
// ticker output suffix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// ticker outputs within the cli interface (end of line).
define("TOSUF",							"  ");

// !
// FOPRE: (string) :: Footer Output Prefix
// footer output prefix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// footer outputs within the cli interface (start of line).
define("FOPRE",							"  ");

// !
// FOSUF: (string) :: Footer Output Suffix
// footer output suffix is always applied to output in the interface
// setting this will allow for consistent styling to be applied across all
// footer outputs within the cli interface (end of line).
define("FOSUF",							"  ");

// ~
// TICKER_SPINNER: (string array) :: Ticker Spinner
// spinner animation that changes at each tick
// this should always start 1 frame forward so that the animation pauses at
// its completed frame. the number of frames also determines the time at which
// the quit option becomes active for program termination
const TICKER_SPINNER = array(
	'[.o...] ','[..o..] ','[...o.] ','[....o] ','[...o.] ','[..o..] ','[.o...] ','[o....] '
);

// ~
// TICKER_SSPINNER: (string array) :: Ticker Simple Spinner
// spinner animation for simple animations (no styling)
const TICKER_SSPINNER = array(
	' ...        ','  ...       ','   ...      ','    ...     ','     ...    ','      ...   ','       ...  ','        ... ','         ...','.         ..','..         .','...         '
);

// ~
// TICKER_SPINNER_STYLE: (char => string associated array) :: Ticker Spinner Style
// spinner animation character styling
// for each frame that is drawn the characters contained within the string are given
// a style. each character used should always have a style available otherwise it will
// not be displayed.
const WIN_TICKER_SPINNER_STYLE = array(
	'o' => "\033[35m", '.' => WIN_RSTYLE, '[' => WIN_RSTYLE, ']' => WIN_RSTYLE, ' ' => WIN_RSTYLE
);
const LNX_TICKER_SPINNER_STYLE = array(
	'o' => "\033[35m", '.' => LNX_RSTYLE, '[' => LNX_RSTYLE, ']' => LNX_RSTYLE, ' ' => LNX_RSTYLE
);

// ~
// TICKER_STATUS: (associated string array) :: Ticker Status
// the status label(s) for the ticker
// shows the colour and label text for the current status of the program
const TICKER_STATUS = array(
	'opening' => array('colour' => "\033[33m", 'label' => "...Opening Socket..."),
	'binding' => array('colour' => "\033[33m", 'label' => "[Binding:" . MASTERSOCKETPORT . "]"),
	'listening' => array('colour' => "\033[32m", 'label' => "}> Listening... <{"),
	'connecting' => array('colour' => "\033[35m", 'label' => "Connecting..."),
	'authenticating' => array('colour' => "\033[31m", 'label' => "Authenticating O>-m [@]"),
	'responding' => array('colour' => "\033[37m", 'label' => "(Responding)")
);

// !
// TICKER_QUIT: (associated string array) {'<inactive>','<active>'} :: Ticker Quit
// the quit label for the ticker. Please keep both at the same character count
// this only has two states, inactive and active, which show the retrospective output
const TICKER_QUIT = array(
	'(Q)uit', '(Q)uit'
);

// !
// TICKER_QUIT_STYLE: (string array) {'<inactive>','<active>'} :: Ticker Quit Style
// the quit label styling for the ticker
// this is the colour settings for the two states of the quit label
const WIN_TICKER_QUIT_STYLE = array(
	"\033[31m", "\033[32m"
);
const LNX_TICKER_QUIT_STYLE = array(
	"\033[31m", "\033[32m"
);

// ~
// TICKER_LABEL_STYLES: (associated string array) :: Ticker Label Styles
// the various ticker label styles
// each type of ticker category is given a style (including TICKER_STATUS's)
const WIN_TICKER_LABEL_STYLES =	array(
	'listening' => "\033[33m", //red txt, black bg

	'status'    => "\033[33m", //yellow txt, black bg
	'logo'		=> "\033[35m", //purple txt, black bg
	'subhead'	=> "\033[33m", //yellow txt, black bg
	'title'		=> "\033[32m", //green txt, black bg
	'notice'	=> "\033[31m", //red txt, black bg
	'logs'		=> "\033[36m", //teal txt, black bg
	'plain'		=> "\033[37m", //white txt, black bg
	'footer'	=> "\033[36m", //teal txt, black bg
	'good'		=> "\033[32m", //green txt, black bg
	'warning'	=> "\033[33m", //yellow txt, black bg
	'bad'		=> "\033[31m" //red txt, black bg
);
const LNX_TICKER_LABEL_STYLES = array(
	'listening' => "\033[33m",

	'status'    => "\033[33m",
	'logo'		=> "\033[35m",
	'subhead'	=> "\033[35m",
	'title'		=> "\033[32m",
	'notice'	=> "\033[31m",
	'logs'		=> "\033[36m",
	'plain'		=> "\033[38m",
	'footer'	=> "\033[36m", //teal txt, black bg
	'good'		=> "\033[32m", //green txt, black bg
	'warning'	=> "\033[33m", //yellow txt, black bg
	'bad'		=> "\033[31m" //red txt, black bg
);

// !
// {OP}_CLI_CLS:: command line interface: CLear Screen
define("WIN_CLI_CLS",					"\033[2J");
define("LNX_CLI_CLS",					"clear");

// !
// {OP}_CLI_RP:: command line interface: Reset Cursor Position
define("WIN_CLI_RCP",					"\033[1;1H");
define("LNX_CLI_RCP",					"");

// ~
// HUD_LOGO: (string array) :: HUD Logo
// the ascii art logo
// this is a line by line array for the logo
const HUD_LOGO = array(
	" _   _   ______    _____   ",
	"| \ | |  | ___ \  /  ___|  ",
	"|  \| |  | |_/ /  \  `-.   ",
	"| . \`|  |    /    `--, \\  ",
	"| |\  |  | |\ \   /\__/ /  ",
	"\_| \_/[]\_| \_\[]\____/ []",
	"                           "
);

// ~
// HUD_SUBHEAD: (string array) :: HUD Subheading
// the ascii art subheading
// this is a line by line array for the subheading
const HUD_SUBHEAD = array(
	"~._.~._.~._.~._.~._.~._.~._",
	"....Network ROM Server.....",
	"_.~._.~._.~._.~._.~._.~._.~"
);

// ~
// HUD_NOTICE: (string array) :: HUD Notice
// the ascii art notice
// this is a line by line array for the notice
const HUD_NOTICE = array(
	"                           ",
	"Hold 'q' to quit when green",
	"                           "
);

// ~
// HUD_NOTICE: (string array) :: HUD Notice
// the ascii art notice
// this is a line by line array for the notice
const HUD_FOOTER = array(
	"                           ",
	"Ver " . NRSVER
);





