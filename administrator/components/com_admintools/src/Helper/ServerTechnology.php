<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

abstract class ServerTechnology
{
	/**
	 * Does the current server support .htaccess files?
	 *
	 * @return  int  0=No, 1=Yes, 2=Maybe
	 */
	public static function isHtaccessSupported(): int
	{
		// Get the server string
		$serverString = $_SERVER['SERVER_SOFTWARE'] ?? '';

		// Not defined? Return maybe (2)
		if (empty($serverString))
		{
			return 2;
		}

		// Apache? Yes
		if (strtoupper(substr($serverString, 0, 6)) == 'APACHE')
		{
			return 1;
		}

		// NginX? No
		if (strtoupper(substr($serverString, 0, 5)) == 'NGINX')
		{
			return 0;
		}

		// IIS? No
		if (strstr($serverString, 'IIS') !== false)
		{
			return 0;
		}

		// Anything else? Maybe.
		return 2;
	}

	/**
	 * Does the current server supports NginX configuration files?
	 *
	 * @return  int  0=No, 1=Yes, 2=Maybe
	 */
	public static function isNginxSupported(): int
	{
		// Get the server string
		$serverString = $_SERVER['SERVER_SOFTWARE'] ?? '';

		// Not defined? Return maybe (2)
		if (empty($serverString))
		{
			return 2;
		}

		// NginX? Yes
		if (strtoupper(substr($serverString, 0, 5)) == 'NGINX')
		{
			return 1;
		}

		// Anything else? No.
		return 0;
	}

	/**
	 * Does the currect server support web.config files?
	 *
	 * @return  int  0=No, 1=Yes, 2=Maybe
	 */
	public static function isWebConfigSupported(): int
	{
		// Get the server string
		$serverString = $_SERVER['SERVER_SOFTWARE'] ?? '';

		// Not defined? Return maybe (2)
		if (empty($serverString))
		{
			return 2;
		}

		// Apache? No
		if (strtoupper(substr($serverString, 0, 6)) == 'APACHE')
		{
			return 0;
		}

		// NginX? No
		if (strtoupper(substr($serverString, 0, 5)) == 'NGINX')
		{
			return 0;
		}

		// IIS? Yes
		if (strstr($serverString, 'IIS') !== false)
		{
			return 1;
		}

		// Anything else? No.
		return 0;
	}

	/**
	 * Checks that the calling code is an authorised first party class.
	 *
	 * Third party developers abuse our code to SILENTLY update the user's security preferences. This ain't
	 * gonna happen no more. You have caused a lot of hacked sites, you fools! If you're reading this message just
	 * document the MANUAL steps required by your users to MANUALLY whitelist a SPECIFIC file of your extension.
	 * Better yet, rewrite your code to NOT require direct access to arbitrary .php files. Either that or you will
	 * get the very public blame & shame treatment. Also note that any attempt to modify this file will result in
	 * a security alert. Now go and learn to write some decent Joomla! extensions, you dangerously incompetent monkeys!
	 *
	 * @param   array  $allowedCallers
	 *
	 * @return  void  Dies if a 3PD developer has been a naughty b45+@Rd
	 */
	public static function checkCaller(array $allowedCallers)
	{
		if (empty($allowedCallers))
		{
			return;
		}

		$backtrace = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4);

		$securityViolation = false;

		if (count($backtrace) < 4)
		{
			$securityViolation = true;
		}

		if (!$securityViolation)
		{
			$securityViolation = true;

			// Check both last index and second to last ones
			for ($i = 2; $i <= 3; $i++)
			{
				// We found an authorized caller, no need to check further
				if (!$securityViolation)
				{
					break;
				}

				$check = $backtrace[$i]['class'] . '::' . $backtrace[2]['function'];

				foreach ($allowedCallers as $allowed)
				{
					if ($check != $allowed)
					{
						continue;
					}

					$securityViolation = false;
					break;
				}
			}
		}

		// ALERT! Someone is trying to screw up your site!
		if ($securityViolation)
		{
			self::thirdPartySettingsModification($backtrace);
		}
	}

	/**
	 * Shows an error message, pointing the finger to the extension which tried to modify Admin Tools security settings
	 * directly. Don't do that. You get an instant blame and shame and you can bet your ass that you'll be placed in
	 * our blacklist.
	 *
	 * @param   array  $backtrace  The backtrace leading to the offending call
	 *
	 * @return  void  Actually, it never returns. It just dies.
	 */
	public static function thirdPartySettingsModification($backtrace = [])
	{
		$lang1 = Text::_('COM_ADMINTOOLS_SERVERCONFIGMAKER_ERR_PROTECTION_TITLE');
		$lang2 = Text::_('COM_ADMINTOOLS_SERVERCONFIGMAKER_ERR_PROTECTION_HEAD');
		$lang3 = Text::_('COM_ADMINTOOLS_SERVERCONFIGMAKER_ERR_PROTECTION_DETECTED');
		$lang4 = Text::_('COM_ADMINTOOLS_SERVERCONFIGMAKER_ERR_PROTECTION_DETAILS');
		$lang5 = Text::_('COM_ADMINTOOLS_SERVERCONFIGMAKER_ERR_PROTECTION_RESOLUTION');

		@$message = <<< HTML
<html lang="en">
<head>
<title>$lang1</title>
<style>
	body {font-family: Helvetica, Arial, sans-serif; font-size: 11pt; background: #ffffcc; }
</style>
</head>
<body>
<h1 style="color: darkred">$lang2</h1>
<p>$lang3</p>
<p>$lang4</p>
<pre>
File     : <span style="color:green">{$backtrace[2]['file']}</span>
Line     : <span style="color:red">{$backtrace[2]['line']}</span>
Class    : <span style="font-weight:bold">{$backtrace[2]['class']}</span>
Function : <span style="color:gray">{$backtrace[2]['function']}</span>
</pre>
<p>$lang5</p>
</body>
</html>
HTML;

		@ob_end_clean();

		echo $message;

		Factory::getApplication()->close();
	}
}
