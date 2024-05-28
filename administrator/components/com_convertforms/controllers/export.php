<?php

/**
 * @package         Convert Forms
 * @version         4.4.1 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2023 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');
 
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Filesystem\File;

/**
 * Export controller class.
 */
class ConvertFormsControllerExport extends FormController
{
	 /**
	  * Used by the export form to submit the data
	  *
	  * @return void
	  */
	public function export()
	{
		Session::checkToken('request') or die(Text::_('JINVALID_TOKEN'));

		$app = Factory::getApplication();
		$input = $app->input;

		$tz   = new \DateTimeZone($app->getCfg('offset', 'GMT'));
		$date = Factory::getDate()->setTimezone($tz)->format('YmdHis', true);
		$filename = 'convertforms_submissions_' . $date . '.' . $input->get('export_type');

		$options = $input->getArray();
		$options['filter_search'] = $input->get('filter_search', null, 'RAW'); // Allow commas and special characters.
		$options['filename'] = $input->get('filename', $filename);

		$app->redirect('index.php?option=com_convertforms&view=export&layout=progress&' . http_build_query($options));
	}

	/**
	 * Force download of the exported file
	 *
	 * @return void
	 */
	public function download()
	{
		if (!$filename = Factory::getApplication()->input->get('filename', '', 'RAW'))
		{
			throw new Exception('Invalid filename');
		}

		$filename = NRFramework\File::getTempFolder() . $filename;

		if (!is_file($filename))
		{
			throw new Exception('Invalid filename');
		}

		error_reporting(0);

		// Send the appropriate headers to force the download in the browser
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
		header('Expires: 0');
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public", false);
		header('Pragma: public');
		header('Content-Length: ' . @filesize($filename));

		// Read exported file to buffer
		readfile($filename);

		// Don't leave any clues on the server. Delete the file.
		File::delete($filename);

		jexit();
	}
}