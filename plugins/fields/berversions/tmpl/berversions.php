<?php
/*
 * Copyright (c) 2021-2024 Bearsampp
 * License:  GNU General Public License version 3 or later; see LICENSE.txt
 * Author: bear
 * Website: https://bearsampp.com
 * Github: https://github.com/Bearsampp
 */

defined('_JEXEC') or die;

if (!isset($rows) || !is_array($rows)) {
  $rows = [];
}
usort($rows, function ($a, $b) {
  return version_compare($b['version'], $a['version']);
});
?>

<table class="table table-striped">
  <thead>
  <tr>
    <th>Version</th>
    <th>Release date</th>
    <th>Download</th>
    <th>Verification</th>
  </tr>
  </thead>
  <tbody>
  <?php foreach ($rows as $row): ?>
    <?php
    $url = $row['url'];
    $icons = explode(',', $row['icon']);
    $iconHtml = '';

    if (in_array('Full', $icons)) {
      $iconHtml .= '<i class="fas fa-star fa-lg" aria-hidden="true"></i>';
    }
    if (in_array('Beta', $icons)) {
      $iconHtml .= '<i class="fas fa-star-half-o fa-lg" aria-hidden="true"></i>';
    }
    if (in_array('Archive', $icons)) {
      $iconHtml .= '<i class="far fa-file-archive fa-lg" aria-hidden="true"></i>';
    }
    if (in_array('Security', $icons)) {
      $iconHtml .= '<i class="fas fa-shield-alt fa-lg" aria-hidden="true"></i>';
    }

    $field = stristr($url, ".7z", true) ?: stristr($url, ".zip", true);
    $md5 = $field . ".md5";
    $sha1 = $field . ".sha1";
    $sha256 = $field . ".sha256";
    $sha512 = $field . ".sha512";

    $date = stristr(substr(stristr(stristr($field, "download/", false), "/"), 1), "/", true);
    $name = substr($url, strrpos($url, "bearsampp-"));
    $version = stristr(ltrim(stristr(ltrim(stristr($name, "-"), "-"), "-"), "-"), "-", true);

    $md5 = '<a href="' . $md5 . '" aria-label="MD5 File"><em class="highlight black">MD5</em></a>';
    $sha1 = '<a href="' . $sha1 . '" aria-label="SHA-1 File"><em class="highlight black">SHA1</em></a>';
    $sha256 = '<a href="' . $sha256 . '" aria-label="SHA-256 File"><em class="highlight black">SHA256</em></a>';
    $sha512 = '<a href="' . $sha512 . '" aria-label="SHA-512 File"><em class="highlight black">SHA512</em></a>';
    ?>
    <tr>
      <td><strong><?php echo $version; ?></strong><?php echo $iconHtml; ?></td>
      <td><?php echo $date; ?></td>
      <td><a href="<?php echo $url; ?>" aria-labelledby="File link"><?php echo $name; ?></a></td>
      <td><?php echo $md5 . ' ' . $sha1 . ' ' . $sha256 . ' ' . $sha512; ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
