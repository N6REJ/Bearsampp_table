<?php
defined( '_JEXEC' ) or die;

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Registry\Registry;

class PlgFieldsBerversions extends CMSPlugin
{
  protected $autoloadLanguage = true;

  public function onContentPrepareForm($form, $data)
  {
    if ( !($form instanceof Form) ) {
      return;
    }

    FormHelper::loadFieldClass( 'subform' );

    // Add the extra fields to the form.
    JForm::addFormPath( __DIR__ . '/params' );
    $form->loadFile( 'berversions', false );
  }

  public function onContentBeforeDisplay($context, &$article, &$params, $limitstart = 0)
  {
    if ( isset( $article->jcfields ) ) {
      foreach ( $article->jcfields as $field ) {
        if ( $field->name == 'berversions' ) {
          $subformData   = new Registry( $field->value );
          $rows          = $subformData->get( 'berversions', [] );
          $article->text .= $this->processBerversionsData( $rows );
        }
      }
    }
  }

  public function onContentBeforeSave($context, $article, $isNew)
  {
    if ( isset( $article->jcfields ) ) {
      foreach ( $article->jcfields as &$field ) {
        if ( $field->name == 'berversions' ) {
          $this->updateStars( $field->rawvalue );
        }
      }
    }

    return true;
  }

  private function updateStars(&$fieldValue)
  {
    $data        = json_decode( $fieldValue, true );
    $hasFullStar = false;

    foreach ( $data as &$entry ) {
      if ( $entry['icon'] === 'full' ) {
        if ( $hasFullStar ) {
          // Change to 'none' if a full star already exists
          $entry['icon'] = 'none';
        }
        else {
          $hasFullStar = true; // Mark that a full star has been added
        }
      }
    }

    $fieldValue = json_encode( $data );
  }

  private function processBerversionsData($rows)
  {
    // Sort rows by version in descending order
    usort( $rows, function ($a, $b) {
      return version_compare( $b['version'], $a['version'] );
    } );

    $output = '<table class="table table-striped">';
    $output .= '<thead>';
    $output .= '<tr>';
    $output .= '<th>Version</th>';
    $output .= '<th>Release date</th>';
    $output .= '<th>Download</th>';
    $output .= '<th>Verification</th>';
    $output .= '</tr>';
    $output .= '</thead>';
    $output .= '<tbody>';

    foreach ( $rows as $row ) {
      $url    = $row['url'];
      $icon   = $row['icon'];
      $shield = $row['shield'];

      // Process icon
      if ( $icon === "full" ) {
        $icon = '<i class="fa fa-star fa-lg" aria-hidden="true"></i>';
      }
      elseif ( $icon == "half" ) {
        $icon = '<i class="fa fa-star-half-o fa-lg" aria-hidden="true"></i>';
      }
      else {
        $icon = "";
      }

      // Add shield icon if needed.
      if ( $shield === "yes" ) {
        $shieldIcon = '<i class="fa fa-shield-alt fa-lg" aria-hidden="true"></i>';
        $icon       = $icon . $shieldIcon;
      }

      // Extract file base for checksum links
      $field  = stristr( $url, ".7z", true ) ?: stristr( $url, ".zip", true );
      $md5    = $field . ".md5";
      $sha1   = $field . ".sha1";
      $sha256 = $field . ".sha256";
      $sha512 = $field . ".sha512";

      // Extract date and version
      $date    = stristr( substr( stristr( stristr( $field, "download/", false ), "/" ), 1 ), "/", true );
      $name    = substr( $url, strrpos( $url, "bearsampp-" ) );
      $version = stristr( ltrim( stristr( ltrim( stristr( $name, "-" ), "-" ), "-" ), "-" ), "-", true );

      // Create links
      $md5    = '<a href="' . $md5 . '" aria-label="MD5 File"><em class="highlight black">MD5</em></a>';
      $sha1   = '<a href="' . $sha1 . '" aria-label="SHA-1 File"><em class="highlight black">SHA1</em></a>';
      $sha256 = '<a href="' . $sha256 . '" aria-label="SHA-256 File"><em class="highlight black">SHA256</em></a>';
      $sha512 = '<a href="' . $sha512 . '" aria-label="SHA-512 File"><em class="highlight black">SHA512</em></a>';

      $output .= "<tr>";
      $output .= "<td><strong>$version</strong>$icon</td>";
      $output .= "<td>$date</td>";
      $output .= '<td><a href="' . $url . '" aria-labelledby="File link">' . $name . '</a></td>';
      $output .= "<td>$md5 $sha1 $sha256 $sha512</td>";
      $output .= "</tr>";
    }

    $output .= '</tbody>';
    $output .= '</table>';

    return $output;
  }
}
