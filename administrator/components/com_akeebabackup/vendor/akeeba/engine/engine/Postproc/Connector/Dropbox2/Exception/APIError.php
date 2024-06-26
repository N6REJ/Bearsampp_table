<?php
/**
 * Akeeba Engine
 *
 * @package   akeebaengine
 * @copyright Copyright (c)2006-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License version 3, or later
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, version 3.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program. If not, see
 * <https://www.gnu.org/licenses/>.
 */

namespace Akeeba\Engine\Postproc\Connector\Dropbox2\Exception;

defined('AKEEBAENGINE') || die();

use Exception;

class APIError extends Base
{
	/**
	 * APIError constructor.
	 *
	 * @param   string          $error             Short error code
	 * @param   string          $errorDescription  Long error description
	 * @param   int             $code              Numeric error ID (default: 500)
	 * @param   Exception|null  $previous          Previous exception
	 */
	public function __construct($error, $errorDescription, $code = 500, Exception $previous = null)
	{
		$message = "Error $error: $errorDescription";

		parent::__construct($message, (int) $code, $previous);
	}

}
