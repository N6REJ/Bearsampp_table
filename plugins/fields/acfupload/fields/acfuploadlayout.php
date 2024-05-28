<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2019 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;

extract($displayData);

$wrapper_class = '';
if ($disabled)
{
	$wrapper_class .= ' disabled';
}
?>

<?php if ($required) { ?>
	<!-- Make Joomla client-side form validator happy by adding a fake hidden input field when the File Upload field is required. -->
	<input type="hidden" required class="required" id="<?php echo str_replace('[]', '', $id); ?>"/>
<?php } ?>

<div class="cfup-tmpl" style="display:none;">
	<div class="cfup-file">
		<div class="cfup-status"></div>
		<div class="cfup-details">
			<div class="cfup-name" data-dz-name></div>
			<div class="cfup-error"><div data-dz-errormessage></div></div>
			<div class="cfup-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
		</div>
		<div class="cfup-right">
			<span class="cfup-size" data-dz-size></span>
			<span class="cfup-controls">
				<a href="#" class="acfupload-edit-item" data-bs-toggle="modal" data-bs-target="#acfUploadItemEditModal" data-toggle="modal" data-target="#acfUploadItemEditModal">
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20"><path d="M9 39h2.2l22.15-22.15-2.2-2.2L9 36.8Zm30.7-24.3-6.4-6.4 2.1-2.1q.85-.85 2.1-.85t2.1.85l2.2 2.2q.85.85.85 2.1t-.85 2.1Zm-2.1 2.1L12.4 42H6v-6.4l25.2-25.2Zm-5.35-1.05-1.1-1.1 2.2 2.2Z"/></svg>
				</a>
				<?php if ($show_download_links) { ?>
					<a href="#" title="<?php echo Text::_('ACF_UPLOAD_VIEW_FILE') ?>" class="upload-link" target="_blank">
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20"><path d="M22.5 34H14q-4.25 0-7.125-2.875T4 24q0-4.25 2.875-7.125T14 14h8.5v3H14q-3 0-5 2t-2 5q0 3 2 5t5 2h8.5Zm-6.25-8.5v-3h15.5v3ZM25.5 34v-3H34q3 0 5-2t2-5q0-3-2-5t-5-2h-8.5v-3H34q4.25 0 7.125 2.875T44 24q0 4.25-2.875 7.125T34 34Z"/></svg>
					</a>
					<a href="#" title="<?php echo Text::_('ACF_UPLOAD_DOWNLOAD_FILE') ?>" class="upload-link" download>
						<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20"><path d="M11 40q-1.2 0-2.1-.9Q8 38.2 8 37v-7.15h3V37h26v-7.15h3V37q0 1.2-.9 2.1-.9.9-2.1.9Zm13-7.65-9.65-9.65 2.15-2.15 6 6V8h3v18.55l6-6 2.15 2.15Z"/></svg>
					</a>
				<?php } ?>
				<a href="#" class="acf_upload_delete" title="<?php echo Text::_('ACF_UPLOAD_DELETE_FILE') ?>" data-dz-remove>
					<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" width="20"><path d="M13.05 42q-1.2 0-2.1-.9-.9-.9-.9-2.1V10.5H8v-3h9.4V6h13.2v1.5H40v3h-2.05V39q0 1.2-.9 2.1-.9.9-2.1.9Zm21.9-31.5h-21.9V39h21.9Zm-16.6 24.2h3V14.75h-3Zm8.3 0h3V14.75h-3Zm-13.6-24.2V39Z"/></svg>
				</a>
			</span>
		</div>
		<input type="hidden" value="" name="" class="cfup-custom-title" />
		<input type="hidden" value="" name="" class="cfup-custom-description" />
	</div>
</div>
<div data-id="<?php echo $field_id ?>"
	data-inputname="<?php echo $input_name ?>"
	<?php if ($multiple) { echo 'data-multiple'; } ?>
	data-maxfilesize="<?php echo $max_file_size ?>"
	data-maxfiles="<?php echo $limit_files ?>"
	data-acceptedfiles="<?php echo $upload_types ?>"
	data-value='<?php echo ($value) ? htmlspecialchars(json_encode($value), ENT_QUOTES, 'UTF-8') : '' ?>'
	data-baseurl='<?php echo $base_url ?>'
	<?php if ($disabled): ?>
	data-disabled='<?php echo $disabled ?>'
	<?php endif; ?>
	class="acfupload<?php echo $wrapper_class; ?>">
	<div class="dz-message">
		<span><?php echo Text::_('ACF_UPLOAD_DRAG_AND_DROP_FILES') ?></span>
		<span class="acfupload-browse"><?php echo Text::_('ACF_UPLOAD_BROWSE') ?></span>
	</div>
	<div class="acfupload-items"></div>
</div>