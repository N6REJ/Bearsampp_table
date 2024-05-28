<?php
/**
 * @copyright	Copyright (C) 2011 Simplify Your Web, Inc. All rights reserved.
 * @license		GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip', '.hasTooltip');

HTMLHelper::_('stylesheet', 'syw/fonts.min.css', array('relative' => true, 'version' => 'auto'));
HTMLHelper::_('stylesheet', 'com_articledetailsprofiles/console.css', array('relative' => true, 'version' => 'auto'));

$login_url = 'https://simplifyyourweb.com/login';

$extension_url = 'https://simplifyyourweb.com/downloads/article-details-profiles';
$changelog_url = 'https://simplifyyourweb.com/downloads/article-details-profiles/file/363-article-details-profiles';
$update_instructions_url = 'https://simplifyyourweb.com/documentation/article-details/installation/updating-older-versions';
$jed_url = 'https://extensions.joomla.org/extensions/extension/news-display/content-infos/article-details-profiles';
$quickstart_url = 'https://simplifyyourweb.com/documentation/tutorials/831-setting-up-article-details-profiles';
$documentation_url = 'https://simplifyyourweb.com/documentation/article-details';
$forum_url = 'https://simplifyyourweb.com/forum/article-details-profiles';
$support_url = 'https://simplifyyourweb.com/support';
$translate_url = 'https://simplifyyourweb.com/translators';

$calendars_url = 'https://simplifyyourweb.com/downloads/article-details-profiles/calendar-styles';
$information_types_url = 'https://simplifyyourweb.com/downloads/article-details-profiles/information-types';

$license_url = 'https://www.gnu.org/licenses/gpl-3.0.html';
?>
<div class="row">
	<div class="col-md-12">
		<div class="row">
			<div class="col-md-7">
				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES'); ?></h2>
					<div class="card-body">
						<?php echo HTMLHelper::image('com_articledetailsprofiles/logo.png', 'Article Details Profiles', array('style' => 'max-width: 100%'), true); ?>
					</div>
				</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_HELP'); ?></h2>
					<div class="card-body">
	    				<div class="quick-icons">
		   					<ul class="nav">
		                   		<?php echo $this->getIcon($quickstart_url, 'SYWicon-timer', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_QUICKSTART'), '_blank'); ?>
		                   		<?php echo $this->getIcon($documentation_url, 'SYWicon-local-library', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_ONLINEDOC'), '_blank'); ?>
		                   		<?php echo $this->getIcon($forum_url, 'SYWicon-chat', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_HELPINFORUM'), '_blank'); ?>
		                   		<?php echo $this->getIcon($support_url, 'SYWicon-lifebuoy', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_SUPPORT'), '_blank'); ?>
		                   	</ul>
	    				</div>
	    			</div>
    			</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_ADDITIONALDOWNLOADS'); ?></h2>
					<div class="card-body">
	    				<div class="quick-icons">
		   					<ul class="nav">
								<?php echo $this->getIcon($calendars_url, 'SYWicon-calendar', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_CALENDARS'), '_blank'); ?>
								<?php echo $this->getIcon($information_types_url, 'SYWicon-dehaze', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_INFORMATIONTYPES'), '_blank'); ?>
							</ul>
	    				</div>
	    			</div>
    			</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_PLUGINS'); ?></h2>
<!-- 					<div class="card-body"> -->
						<table class="table">
							<tbody>
								<tr>
									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_PLG_CONTENT_ARTICLEDETAILSPROFILES'); ?></td>
									<?php if (PluginHelper::isEnabled('content', 'articledetailsprofiles')) : ?>
										<td><span class="icon-publish"></span></td>
										<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=content&filter[element]=articledetailsprofiles&filter[enabled]=1"><?php echo Text::_('JLIB_HTML_UNPUBLISH_ITEM'); ?></a></td>
									<?php else : ?>
										<td><span class="icon-unpublish"></span></td>
										<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=content&filter[element]=articledetailsprofiles&filter[enabled]=0"><?php echo Text::_('JLIB_HTML_PUBLISH_ITEM'); ?></a></td>
									<?php endif; ?>
								</tr>
								<tr>
									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_PLG_QUICKICON_ARTICLEDETAILSPROFILES'); ?></td>
									<?php if (PluginHelper::isEnabled('quickicon', 'articledetailsprofiles')) : ?>
										<td><span class="icon-publish"></span></td>
										<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=quickicon&filter[element]=articledetailsprofiles&filter[enabled]=1"><?php echo Text::_('JLIB_HTML_UNPUBLISH_ITEM'); ?></a></td>
									<?php else : ?>
										<td><span class="icon-unpublish"></span></td>
										<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=quickicon&filter[element]=articledetailsprofiles&filter[enabled]=0"><?php echo Text::_('JLIB_HTML_PUBLISH_ITEM'); ?></a></td>
									<?php endif; ?>
								</tr>
								<tr>
									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_PLG_INSTALLER_ADPINSTALLER'); ?></td>
									<?php if (PluginHelper::isEnabled('installer', 'adpinstaller')) : ?>
										<td><span class="icon-publish"></span></td>
										<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=installer&filter[element]=adpinstaller&filter[enabled]=1"><?php echo Text::_('JLIB_HTML_UNPUBLISH_ITEM'); ?></a></td>
									<?php else : ?>
										<td><span class="icon-unpublish"></span></td>
										<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=installer&filter[element]=adpinstaller&filter[enabled]=0"><?php echo Text::_('JLIB_HTML_PUBLISH_ITEM'); ?></a></td>
									<?php endif; ?>
								</tr>
								<?php if (count($this->extended_plugins) > 0) : ?>
									<?php foreach ($this->extended_plugins as $extra_plugin) : ?>
										<?php Factory::getLanguage()->load('plg_articledetails_' . $extra_plugin->name . '.sys', JPATH_ADMINISTRATOR); ?>
	        							<tr>
	        								<td><?php echo Text::_('PLG_ARTICLEDETAILS_' . strtoupper($extra_plugin->name)); ?></td>
	        								<?php if (PluginHelper::isEnabled('articledetails', $extra_plugin->name)) : ?>
	        									<td><span class="icon-publish"></span></td>
	        									<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=articledetails&filter[element]=<?php echo $extra_plugin->name; ?>&filter[enabled]=1"><?php echo Text::_('JLIB_HTML_UNPUBLISH_ITEM'); ?></a></td>
	        								<?php else : ?>
	        									<td><span class="icon-unpublish"></span></td>
	        									<td><a href="index.php?option=com_plugins&view=plugins&filter[folder]=articledetails&filter[element]=<?php echo $extra_plugin->name; ?>&filter[enabled]=0"><?php echo Text::_('JLIB_HTML_PUBLISH_ITEM'); ?></a></td>
	        								<?php endif; ?>
	        							</tr>
	        						<?php endforeach; ?>
								<?php endif; ?>
							</tbody>
						</table>
<!-- 					</div> -->
				</div>
			</div>
			<div class="col-md-5">
				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::sprintf('COM_ARTICLEDETAILSPROFILES_INFO_VERSION', $this->extension_version); ?></h2>
					
					<div class="card-body">					
						<?php if ($this->version_array !== false) : ?>
							<div class="quick-icons">
			   					<ul class="nav">
									<?php if (version_compare($this->extension_version, $this->version_array['latest'], 'lt')) : ?>
										<?php if ($this->license_is_valid) : ?>
											<?php echo $this->getIcon('index.php?option=com_installer&view=update', 'SYWicon-upload', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_INSTALLUPDATE'), '', '', 'danger'); ?>
										<?php else : ?>
											<?php echo $this->getIcon($extension_url, 'SYWicon-file-download', Text::_ ('COM_ARTICLEDETAILSPROFILES_INFO_DOWNLOADUPDATE'), '_blank', '', 'danger'); ?>
										<?php endif; ?>
									<?php else : ?>
										<?php echo $this->getIcon($extension_url, 'SYWicon-description', Text::_('COM_ARTICLEDETAILSPROFILES_INFO_UPTODATE'), '_blank', '', 'success'); ?>
									<?php endif; ?>
			    				</ul>
			    			</div>
		    			<?php endif; ?>
		    		</div>
						
					<ul class="list-group list-group-flush">
						<li class="list-group-item">
							<a href="<?php echo $changelog_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_CHANGELOG'); ?></a>
						</li>
						<li class="list-group-item">
							<a href="<?php echo $update_instructions_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_UPDATEINSTRUCTIONS'); ?></a>
						</li>
					</ul>
					
					<table class="table">
						<tbody>
							<tr>
								<td class="w-30"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LATESTVERSION'); ?></td>
								<?php if ($this->version_array === false) : ?>
									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_UNKNOWNLATESTVERSION'); ?></td>
								<?php else : ?>
									<td>
										<?php if (version_compare($this->extension_version, $this->version_array['latest'], 'lt')) : ?>
											<?php echo $this->version_array['latest']; ?>
										<?php else : ?>
											<?php echo $this->extension_version; ?>
										<?php endif; ?>
										<?php if (version_compare($this->extension_version, $this->version_array['latest'], 'lt')) : ?>
											<?php if ($this->license_is_valid) : ?>
												<br /><a href="index.php?option=com_installer&view=update"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_UPDATE'); ?></a>
											<?php else : ?>
												<br /><a href="<?php echo $extension_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_DOWNLOAD'); ?></a>
											<?php endif; ?>
										<?php endif; ?>
									</td>
								<?php endif; ?>
							</tr>
							<?php if ($this->version_array !== false && $this->version_array['relevance'] && version_compare($this->extension_version, $this->version_array['latest'], 'lt')) : ?>
    							<tr>
    								<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LATESTVERSIONNOTABLE'); ?></td>
    								<td><?php echo $this->version_array['relevance']; ?></td>
    							</tr>
    						<?php endif; ?>
						</tbody>
					</table>
				</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LICENSE'); ?></h2>
	
						<table class="table">
							<tbody>
								<tr>
									<td class="w-30"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_DOWNLOADID'); ?></td>
									<?php if (empty($this->license_array['download_id'])) : ?>
										<td>
											<a href="<?php echo $login_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_GETDOWNLOADID'); ?></a>
											<br /><a href="index.php?option=com_plugins&view=plugins&filter[folder]=installer"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_ENTERDOWNLOADID'); ?></a>
										</td>
									<?php else : ?>
										<?php if (!empty($this->license_array['expiration_date']) && strtotime('now') >= strtotime($this->license_array['expiration_date'])) : ?>
											<td>
												<span class="icon-unpublish"></span>
												<a href="index.php?option=com_plugins&view=plugins&filter[folder]=installer"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_REENTERDOWNLOADID'); ?></a>
											</td>
										<?php else : ?>
											<td><span class="icon-publish"></span></td>
										<?php endif; ?>
									<?php endif; ?>
								</tr>
								<?php if (!empty($this->license_array['download_id'])) : ?>
	    							<tr>
	    								<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LICENSEEXPIRATIONDATE'); ?></td>
	    								<?php if (!isset($this->license_array['enabled'])) : ?>
	    									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_UNKNOWNLICENSESTATUS'); ?></td>
	    								<?php elseif ($this->license_array['enabled'] == 0) : ?>
	    									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_DISABLEDLICENSE'); ?></td>
	    								<?php else : ?>
	        								<?php if (empty($this->license_array['expiration_date'])) : ?>
	        									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LICENSENEVEREXPIRES'); ?></td>
	        								<?php else : ?>
	            								<?php if (strtotime('now') >= strtotime($this->license_array['expiration_date'])) : ?>
	            									<td>
	            										<?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_EXPIREDLICENSE'); ?>
	            										<a href="<?php echo $login_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_BUYLICENSEAGAIN'); ?></a>
	            									</td>
	            								<?php else : ?>
	        										<td>
	        											<?php echo date(Text::_('DATE_FORMAT_LC3'), strtotime($this->license_array['expiration_date'])); ?>
	        											<?php if ((strtotime($this->license_array['expiration_date']) - strtotime('now')) < (60 * 60 * 24 * 120)) : ?>
	        												<a href="<?php echo $login_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_RENEWLICENSE'); ?></a>
	        											<?php endif; ?>
	        										</td>
	        									<?php endif; ?>
	        								<?php endif; ?>
	        							<?php endif; ?>
	    							</tr>
	    						<?php endif; ?>
							</tbody>
						</table>
				</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_CREDITS'); ?></h2>
<!-- 					<div class="card-body"> -->
						<table class="table">
							<tbody>
								<tr>
									<td class="w-30"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LICENSE'); ?></td>
									<td><a href="<?php echo $license_url; ?>" target="_blank">GNU General Public License v3</a></td>
								</tr>
								<tr>
									<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_AUTHOR'); ?></td>
									<td>Olivier Buisard</td>
								</tr>
								<?php if (!empty(Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LANGUAGETHANKS'))) : ?>
	    							<tr>
	    								<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_TRANSLATIONS'); ?></td>
	    								<td><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_LANGUAGETHANKS'); ?></td>
	    							</tr>
								<?php endif; ?>
							</tbody>
						</table>
<!-- 					</div> -->
				</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_CONNECT'); ?></h2>
					<div class="card-body">
						<a class="btn btn-sm btn-info hasTooltip" style="margin: 2px; background-color: #02b0e8; border-color: #02b0e8" href="https://twitter.com/simplifyyourweb" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" height="1em" viewbox="0 0 512 512" aria-hidden="true"><path fill="currentColor" d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z"></path></svg> X-Twitter</a>
						<a class="btn btn-sm btn-info hasTooltip" style="margin: 2px; background-color: #43609c; border-color: #43609c" title="simplifyyourweb" href="https://www.facebook.com/simplifyyourweb" target="_blank"><i class="SYWicon-facebook" aria-hidden="true">&nbsp;</i>Facebook</a>
						<a class="btn btn-sm btn-warning" style="margin: 2px; background-color: #ff8f00; border-color: #ff8f00" href="https://simplifyyourweb.com/latest-news?format=feed&amp;type=rss" target="_blank"><i class="SYWicon-rss" aria-hidden="true">&nbsp;</i>News feed</a>
					</div>
				</div>

				<div class="card mb-3">
					<h2 class="card-header"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_GETINVOLVED'); ?></h2>
<!-- 					<div class="card-body">						 -->
						<ul class="list-group list-group-flush">
							<li class="list-group-item">
								<a href="<?php echo $translate_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_TRANSLATE'); ?></a>
							</li>
							<li class="list-group-item">
								<a href="<?php echo $jed_url; ?>" target="_blank"><?php echo Text::_('COM_ARTICLEDETAILSPROFILES_INFO_POSTAREVIEW'); ?></a>
							</li>
						</ul>
<!-- 					</div> -->
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-md-12 copyrightblock">
				<p><?php echo Text::_('COM_ARTICLEDETAILSPROFILES'); ?> <?php echo Text::sprintf('COM_ARTICLEDETAILSPROFILES_INFO_VERSION', $this->extension_version); ?></p>
				<p class="copyright"><img src="<?php echo Uri::root(true) ?>/media/com_articledetailsprofiles/images/simplifyyourweb.png" alt="Simplify Your Web" /><span>Copyright &copy; 2011-<?php echo date("Y"); ?> <a href="https://simplifyyourweb.com" target="_blank">Simplify Your Web</a>. All rights reserved.</span></p>
			</div>
		</div>
	</div>
</div>