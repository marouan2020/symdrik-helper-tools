<?php
namespace Drupal\symdrik_helper_tools;

use Drupal\media\Entity\Media;

/**
 * Class MediaHelper
 *
 * @package Drupal\symdrik_helper_tools
 */
class MediaHelper {

  /**
   * Create a media image from path.
   *
   * @param string $imagePath
   *   Path of a media.
   * @param string $newName
   *   Name of a media.
   *
   * @return \Drupal\media\Entity\Media|null
   *   Result can be a entity media or null.
   */
  public function importMediaImageFromPath($imagePath, $newName) {
    if (!file_exists($imagePath)) {
      return;
    }
    $file = $this->createFileFromPath($imagePath, $newName);
    if(!empty($file)) {
      $drupalMedia = Media::create([
        'bundle' => 'image',
        'uid' => \Drupal::currentUser()->id(),
        'status' => true,
        'field_media_image' => $file
      ]);
      return $drupalMedia;
    }
    return NULL;
  }

  /**
   * Create a file by path.
   *
   * @param $imagePath
   *   Media path.
   *
   * @param $newName
   *   Name of a media.
   *
   * @return \Drupal\Core\Entity\EntityInterface|null
   *   Result can be Entity file or null.
   */
  public function createFileFromPath($imagePath, $newName) {
    if (!file_exists($imagePath)) {
      return;
    }
    $imageData = file_get_contents($imagePath);
    if(!empty($imageData)) {
      $file = file_save_data($imageData, "public://" . str_replace(' ', '-' , $newName));
      return $file;
    }
    return NULL;
  }
}
