<?php
/**
 * @package   akeebabackup
 * @copyright Copyright 2006-2024 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$uri = clone Uri::getInstance();
$uri->setVar('_akeeba_backup_on_update_toggle', $this->getApplication()->getSession()->getToken());

?>
<div class="card border-danger border-2 mb-3">

	<div class="h3 card-header bg-danger text-white d-flex flex-row">
		<div class="flex-grow-1">
			<?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_TITLE') ?>
			<span class="fs-4 text-light">
				&ndash;
				<?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_DISABLED') ?>
			</span>
		</div>

		<div class="small">
			<span class="fa fa-info-circle" aria-hidden="true"
				  title="<?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_POWERED') ?>"
			></span>
			<span class="visually-hidden"><?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_POWERED') ?></span>
		</div>
	</div>

	<div class="card-body">
		<p>
			<span class="fa fa-cancel text-danger me-2" aria-hidden="true"></span>
			<?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_CONTENT_INACTIVE') ?>
		</p>
		<p>
			<a href="<?= htmlentities($uri->toString()) ?>"
			   class="btn btn-outline-success">
				<span class="fa fa-toggle-on" aria-hidden="true"></span>
				<?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_TOGGLE_ACTIVATE') ?>
			</a>
		</p>
		<p class="text-muted fst-italic small">
			<?= Text::_('PLG_SYSTEM_BACKUPONUPDATE_LBL_CONTENT_TIP') ?>
		</p>
	</div>
</div>
