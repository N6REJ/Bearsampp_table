<?php

/**
 * @package         Smile Pack
 * @version         1.1.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use NRFramework\Extension;

$download_key = \NRFramework\Functions::getDownloadKey();


NRFramework\HTML::renderProOnlyModal();


function tabSetStart($active)
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.startTabSet', 'sections', ['active' => $active, 'recall' => true, 'orientation' => 'horizontal']) : HTMLHelper::_('bootstrap.startTabSet', 'sections', ['active' => $active]);;
}

function tabSetEnd()
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.endTabSet') : HTMLHelper::_('bootstrap.endTabSet');;
}

function tabStart($name, $title)
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.addTab', 'sections', $name, Text::_($title)) : HTMLHelper::_('bootstrap.addTab', 'sections', $name, Text::_($title));
}

function tabEnd()
{
    echo defined('nrJ4') ? HTMLHelper::_('uitab.endTab') : HTMLHelper::_('bootstrap.endTab');
}

HTMLHelper::stylesheet('com_smilepack/smilepack.sys.css', ['relative' => true, 'version' => 'auto']);
if (!defined('nrJ4'))
{
	HTMLHelper::stylesheet('com_smilepack/smilepack-j3.sys.css', ['relative' => true, 'version' => 'auto']);
}
?>
<div class="smilepack-dashboard <?php echo defined('nrJ4') ? 'row' : 'row-fluid' ?>">
	<?php
	// Display extension notices
	\NRFramework\Notices\Notices::getInstance([
		'ext_element' => 'com_smilepack',
		'ext_xml' => 'com_smilepack',
		'exclude' => [
			'Geolocation'
		]
	])->show();
	?>

	<div class="smilepack-dashboard--content span9 col-md-9">
		<div class="smilepack-dashboard--content--header">
			<h2 class="title"><?php echo Text::_('COM_SMILEPACK_WELCOME_TITLE'); ?></h2>
			<div class="description"><?php echo Text::_('COM_SMILEPACK_WELCOME_DESC'); ?></div>
		</div>
		<div class="smilepack-dashboard--content--body">
			<?php
			tabSetStart('modules');

			tabStart('modules', Text::_('COM_SMILEPACK_MODULES'));
				require __DIR__ . '/pages/modules.php';
			tabEnd();

			tabStart('smart_tags', Text::_('COM_SMILEPACK_SMART_TAGS'));
				require __DIR__ . '/pages/smarttags.php';
			tabEnd();

			tabStart('widgets', Text::_('COM_SMILEPACK_WIDGETS'));
				require __DIR__ . '/pages/widgets.php';
			tabEnd();

			tabStart('headers_footers', Text::_('COM_SMILEPACK_HEADERS_FOOTERS'));
				require __DIR__ . '/pages/headers_footers.php';
			tabEnd();

			tabStart('snippets', Text::_('COM_SMILEPACK_REUSABLE_SNIPPETS'));
				require __DIR__ . '/pages/snippets.php';
			tabEnd();
			
			tabSetEnd();
			?>
		</div>
	</div>
	<div class="smilepack-dashboard--sidebar span3 col-md-3">
		<div class="smilepack-dashboard--sidebar--item">
			<h4 class="smilepack-dashboard--sidebar--item--title"><?php echo Text::_('COM_SMILEPACK_STATUS'); ?></h4>
			<div class="smilepack-dashboard--sidebar--item--content">
				<div><?php echo Text::_('COM_SMILEPACK'); ?> <?php echo NRFramework\Functions::getExtensionVersion('com_smilepack', true); ?></div>
				<?php
				if (!$download_key)
				{
					$tassosFrameworkExtensionID = NRFramework\Functions::getExtensionID('nrframework', 'system');
					$tassosFrameworkPluginURL  = 'index.php?option=com_plugins&task=plugin.edit&extension_id=' . $tassosFrameworkExtensionID . '#jform_params_key';
					?>
					<div class="red-text"><?php echo Text::_('NR_DOWNLOAD_KEY_MISSING'); ?></div>
					<div><a href="<?php echo Uri::base() . $tassosFrameworkPluginURL; ?>" target="_blank" class="setup_key">Setup Download Key</a></div>
					<?php
				}
				?>
			</div>
			<div class="actions">
				<a href="https://www.tassos.gr/joomla-extensions/smile-pack/changelog" target="_blank"><?php echo Text::_('NR_CHANGELOG'); ?></a>
				<a href="https://www.tassos.gr/joomla-extensions/smile-pack/roadmap" target="_blank"><?php echo Text::_('COM_SMILEPACK_ROADMAP'); ?></a>
			</div>
		</div>
		
		<div class="smilepack-dashboard--sidebar--item">
			<h4 class="smilepack-dashboard--sidebar--item--title"><?php echo Text::_('NR_UPGRADE_TO_PRO'); ?></h4>
			<div class="smilepack-dashboard--sidebar--item--content">
				<div><?php echo Text::_('COM_SMILEPACK_FREE_VERSION_DESC'); ?></div>
			</div>
			<div class="actions">
				<a href="#" class="upgradeToPro" data-pro-only>
					<svg width="17" height="17" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg"><mask id="mask0_143_146" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0" width="17" height="17"><rect width="17" height="17" fill="#D9D9D9"/></mask><g mask="url(#mask0_143_146)"><path d="M4.25006 5.66655H10.6251V4.24989C10.6251 3.65961 10.4185 3.15787 10.0053 2.74468C9.59207 2.33149 9.09033 2.12489 8.50005 2.12489C7.90978 2.12489 7.40804 2.33149 6.99485 2.74468C6.58165 3.15787 6.37506 3.65961 6.37506 4.24989H4.95839C4.95839 3.27003 5.3037 2.43478 5.99433 1.74416C6.68495 1.05353 7.52019 0.708221 8.50005 0.708221C9.47992 0.708221 10.3152 1.05353 11.0058 1.74416C11.6964 2.43478 12.0417 3.27003 12.0417 4.24989V5.66655H12.7501C13.1396 5.66655 13.4731 5.80527 13.7506 6.0827C14.028 6.36013 14.1667 6.69364 14.1667 7.08322V14.1666C14.1667 14.5561 14.028 14.8896 13.7506 15.1671C13.4731 15.4445 13.1396 15.5832 12.7501 15.5832H4.25006C3.86047 15.5832 3.52697 15.4445 3.24954 15.1671C2.9721 14.8896 2.83339 14.5561 2.83339 14.1666V7.08322C2.83339 6.69364 2.9721 6.36013 3.24954 6.0827C3.52697 5.80527 3.86047 5.66655 4.25006 5.66655ZM4.25006 14.1666H12.7501V7.08322H4.25006V14.1666ZM8.50005 12.0416C8.88964 12.0416 9.22315 11.9028 9.50058 11.6254C9.77801 11.348 9.91672 11.0145 9.91672 10.6249C9.91672 10.2353 9.77801 9.9018 9.50058 9.62437C9.22315 9.34694 8.88964 9.20822 8.50005 9.20822C8.11047 9.20822 7.77696 9.34694 7.49953 9.62437C7.2221 9.9018 7.08339 10.2353 7.08339 10.6249C7.08339 11.0145 7.2221 11.348 7.49953 11.6254C7.77696 11.9028 8.11047 12.0416 8.50005 12.0416Z" fill="white"/></g></svg>
					<?php echo Text::_('NR_UPGRADE_TO_PRO'); ?>
				</a>
			</div>
		</div>
		
		<div class="smilepack-dashboard--sidebar--item">
			<h4 class="smilepack-dashboard--sidebar--item--title"><?php echo Text::_('COM_SMILEPACK_KNOWLEDGE_BASE'); ?></h4>
			<div class="smilepack-dashboard--sidebar--item--content">
				<ul>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/getting-started-smile-pack"><?php echo Text::_('COM_SMILEPACK_KB_GETTING_STARTED'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/video-module"><?php echo Text::_('COM_SMILEPACK_KB_USING_VIDEO_MODULE'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/map-module"><?php echo Text::_('COM_SMILEPACK_KB_USING_MAP_MODULE'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/accordion-module"><?php echo Text::_('COM_SMILEPACK_KB_USING_ACCORDION_MODULE'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/gallery-module"><?php echo Text::_('COM_SMILEPACK_KB_USING_GALLERY_MODULE'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/paypal-module"><?php echo Text::_('COM_SMILEPACK_KB_USING_PAYPAL_MODULE'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/smart-tags-sp"><?php echo Text::_('COM_SMILEPACK_KB_USING_SMART_TAGS'); ?></a>
					</li>
					<li>
						<a href="https://www.tassos.gr/joomla-extensions/smile-pack/docs/frequently-asked-questions-smile-pack"><?php echo Text::_('COM_SMILEPACK_KB_FAQ'); ?></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="smilepack-dashboard--sidebar--item">
			<h4 class="smilepack-dashboard--sidebar--item--title"><?php echo Text::_('COM_SMILEPACK_SPREAD_THE_LOVE'); ?></h4>
			<div class="smilepack-dashboard--sidebar--item--content">
				<?php echo Text::sprintf('COM_SMILEPACK_WRITE_REVIEW', Extension::getExtensionJEDURL('com_smilepack')); ?>
			</div>
		</div>
		<div class="smilepack-dashboard--sidebar--item">
			<h4 class="smilepack-dashboard--sidebar--item--title"><?php echo Text::_('NR_HELP_WITH_TRANSLATIONS'); ?></h4>
			<div class="smilepack-dashboard--sidebar--item--content">
				<?php echo Text::sprintf('NR_TRANSLATE_INTEREST', Text::_('COM_SMILEPACK')); ?> <a href="https://www.transifex.com/tassosgr/smile-pack/" target="_blank" class="underline-link"><?php echo Text::_('COM_SMILEPACK_GET_STARTED'); ?></a>.
			</div>
		</div>
	</div>
	<?php include_once(JPATH_COMPONENT_ADMINISTRATOR."/layouts/footer.php"); ?>
</div>