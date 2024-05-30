<?php
defined('_JEXEC') or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;

class PlgFieldsBerversions extends FieldsPlugin
{
  protected $autoloadLanguage = true;

  public $type = 'berversions';

  public function onContentPrepareForm($form, $data)
  {
    if (!($form instanceof Form)) {
      return;
    }

    FormHelper::loadFieldClass('subform');

    // Add the extra fields to the form.
    JForm::addFormPath(__DIR__ . '/params');
    $form->loadFile('berversions', false);
  }

  public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
  {
    if (isset($article->jcfields)) {
      foreach ($article->jcfields as $field) {
        if ($field->name == 'berversions') {
          $subformData = new Registry($field->value);
          $rows = $subformData->get('berversions', []);
          $article->text .= $this->processBerversionsData($rows);
        }
      }
    }
  }

  public function onContentBeforeSave($context, $article, $isNew)
  {
    if (isset($article->jcfields)) {
      foreach ($article->jcfields as &$field) {
        if ($field->name == 'berversions') {
          $this->updateStars($field->rawvalue);
        }
      }
    }

    return true;
  }

  private function updateStars(&$fieldValue)
  {
    $data = json_decode($fieldValue, true);

    // Sort data by version in descending order to ensure the newest entry is first
    usort($data, function ($a, $b) {
      return version_compare($b['version'], $a['version']);
    });

    $hasFullStar = false;

    foreach ($data as &$entry) {
      if ($entry['icon'] === 'Full') {
        if ($hasFullStar) {
          // Change to 'none' if a full star already exists
          $entry['icon'] = 'none';
        } else {
          $hasFullStar = true; // Mark that a full star has been added
        }
      }
    }

    // Ensure the newest entry has the full star
    if (!$hasFullStar && !empty($data)) {
      $data[0]['icon'] = 'Full';
    }

    $fieldValue = json_encode($data);
  }

  private function processBerversionsData($rows)
  {
    ob_start();
    include __DIR__ . '/tmpl/default.php';
    $output = ob_get_clean();
    return $output;
  }
}
