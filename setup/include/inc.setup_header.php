<?php
/*
	+-----------------------------------------------------------------------------+
	| ILIAS open source                                                           |
	+-----------------------------------------------------------------------------+
	| Copyright (c) 1998-2001 ILIAS open source, University of Cologne            |
	|                                                                             |
	| This program is free software; you can redistribute it and/or               |
	| modify it under the terms of the GNU General Public License                 |
	| as published by the Free Software Foundation; either version 2              |
	| of the License, or (at your option) any later version.                      |
	|                                                                             |
	| This program is distributed in the hope that it will be useful,             |
	| but WITHOUT ANY WARRANTY; without even the implied warranty of              |
	| MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
	| GNU General Public License for more details.                                |
	|                                                                             |
	| You should have received a copy of the GNU General Public License           |
	| along with this program; if not, write to the Free Software                 |
	| Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA. |
	+-----------------------------------------------------------------------------+
*/

/**
* header include for ilias setup
*
* @author	Sascha Hofmann <shofmann@databay.de>
* @version	$Id$
* @package	ilias-setup
*/

define("DEBUG",false);

//include files from PEAR
require_once "PEAR.php";
require_once "DB.php";

// wrapper for php 4.3.2 & higher
@include_once "HTML/ITX.php";

if (!class_exists("IntegratedTemplateExtension"))
{
	include_once "HTML/Template/ITX.php";
	include_once "../classes/class.ilTemplateHTMLITX.php";
}
else
{
	include_once "../classes/class.ilTemplateITX.php";
}

require_once "./classes/class.ilTemplate.php";	// modified class. needs to be merged with base template class 

require_once "../include/inc.check_pear.php";
require_once "./classes/class.ilLanguage.php";	// modified class. needs to be merged with base language class 
require_once "../classes/class.ilLog.php";
require_once "../classes/class.ilUtil.php";
require_once "../classes/class.ilIniFile.php";
require_once "../classes/class.perm.php";
require_once "../classes/class.ilDBx.php";
require_once "./classes/class.ilSetupGUI.php";
require_once "./classes/class.Session.php";
require_once "./classes/class.ilClientList.php";
require_once "./classes/class.ilClient.php";
require_once "../classes/class.ilFile.php";
require_once "./classes/class.ilCtrlStructureReader.php";
require_once "../classes/class.ilSaxParser.php";
require_once "../include/inc.ilias_version.php";

// include error_handling
require_once "../classes/class.ilErrorHandling.php";

$ilErr = new ilErrorHandling();
$ilErr->setErrorHandling(PEAR_ERROR_CALLBACK,array($ilErr,'errorHandler'));

// set ilias pathes
define ("ILIAS_HTTP_PATH",substr("http://".$_SERVER["HTTP_HOST"].dirname($_SERVER["REQUEST_URI"]),0,-6));

// PHP is running in CGI mode?
if (isset($_SERVER["REDIRECT_STATUS"]) && !isset($_SERVER["FCGI_ROLE"]))
{
	define ("ILIAS_ABSOLUTE_PATH",substr(dirname($_SERVER["PATH_TRANSLATED"]),0,-6));
}
else
{
	define ("ILIAS_ABSOLUTE_PATH",substr(dirname($_SERVER["SCRIPT_FILENAME"]),0,-6));
}

define ("TPLPATH","./templates/blueshadow");

// init session
$sess = new Session();

$lang = (isset($_GET["lang"])) ? $_GET["lang"] : $_SESSION["lang"];

$_SESSION["lang"] = $lang;

// init languages
$lng = new ilLanguage($lang);

// init log
$log = new ilLog(ILIAS_ABSOLUTE_PATH,"ilias.log","SETUP",false);
$ilLog =& $log;

// init template - in the main program please use ILIAS Template class
// instantiate main template
$tpl = new ilTemplate("./templates");
$tpl->loadTemplatefile("tpl.main.html", true, true);

// make instance of structure reader
$ilCtrlStructureReader = new ilCtrlStructureReader();
$ilCtrlStructureReader->setErrorObject($ilErr);

require_once "../classes/class.ilBenchmark.php";
$ilBench =& new ilBenchmark();
$GLOBALS['ilBench'] =& $ilBench;


?>