<?php

/**
 * @package         Advanced Custom Fields
 * @version         2.8.1-RC2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2023 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;

if (defined('nrJ4'))
{
	$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
	$wa->registerAndUseStyle('acf_articles_style', 'plg_fields_acfarticles/style.css');
}
else
{
	HTMLHelper::stylesheet('plg_fields_acfarticles/style.css', ['relative' => true, 'version' => 'auto']);
}

foreach ($articles as $article)
{
	$image = '';
	if (isset($article['images']) && $image = json_decode($article['images']))
	{
		$image = $image->image_intro ?: $image->image_fulltext;
	}

	$html .= '<div class="acfarticle-item">';
	
	if ($image)
	{
		$html .= '<img src="' . $image . '" class="acfarticle-item--image" alt="' . $article['title'] . '" loading="lazy" />';
	}

	$html .= '<a href="' . Route::_($routerHelper::getArticleRoute($article['id'], $article['catid'], $article['language'])) . '" class="acfarticle-item--content--title">' . $article['title'] . '</a>';

	$html .= '</div>';
}