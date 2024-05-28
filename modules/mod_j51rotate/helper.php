<?php
/**
* J51_Rotate
* Created by	: Joomla51
* Email			: info@joomla51.com
* URL			: www.joomla51.com
* Licensed under the GPL v2&
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\Input\Json;

abstract class modj51Rotate {

	public static function json_validate($string) {
	    $result = json_decode($string);

	    // Check for possible JSON errors
	    switch (json_last_error()) {
	      case JSON_ERROR_NONE:
	        $error = ''; // JSON is valid, no error has occurred
	        break;
	      case JSON_ERROR_DEPTH:
	        $error = 'The maximum stack depth has been exceeded.';
	        break;
	      case JSON_ERROR_STATE_MISMATCH:
	        $error = 'Invalid or malformed JSON.';
	        break;
	      case JSON_ERROR_CTRL_CHAR:
	        $error = 'Control character error, possibly incorrectly encoded.';
	        break;
	      case JSON_ERROR_SYNTAX:
	        $error = 'Syntax error, malformed JSON.';
	        break;
	      case JSON_ERROR_UTF8:
	        $error = 'Malformed UTF-8 characters, possibly incorrectly encoded.';
	        break;
	      case JSON_ERROR_RECURSION:
	        $error = 'One or more recursive references in the value to be encoded.';
	        break;
	      case JSON_ERROR_INF_OR_NAN:
	        $error = 'One or more NAN or INF values in the value to be encoded.';
	        break;
	      case JSON_ERROR_UNSUPPORTED_TYPE:
	        $error = 'A value of a type that cannot be encoded was given.';
	        break;
	      default:
	        $error = 'Unknown JSON error occured.';
	        break;
	    }

	    return array(
	      'status' => $error === '' ? true : false,
	      'result' => $result
	    );
  	}
}


