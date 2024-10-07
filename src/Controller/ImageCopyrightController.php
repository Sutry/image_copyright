<?php

namespace Drupal\image_copyright\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\file\Entity\File;
use Drupal\Core\Link;
use Drupal\Core\Url;


/**
 * Provides a controller for displaying the copyright text for an image.
 */
class ImageCopyrightController extends ControllerBase {

  /**
   * Displays the copyright text for an image.
   *
   * @param int $fid
   *   The file ID.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The response object.
   */
  public function display($fid) {
    // Load the copyright text for the image with the given file ID.
    $copyright = \Drupal::database()->query('SELECT copyright FROM {image_copyright} WHERE fid = :fid', [
      ':fid' => $fid,
    ])->fetchField();

    // Create a response with the copyright text.
    $content = 'Copyright for image fid=' . $fid;

    //  $response = new Response($content);
    $url = Url::fromRoute('copyright.form', ['fid' => $fid]);
    $url->setOptions([
      'attributes' => [
        'class' => ['button'],
      ],
    ]);

    $file = File::load($fid);

    if ($file) {
      // Отримати URL зображення.
      $file_url = $file->createFileUrl();
      $filename = $file->getFilename('x-default');
      // Побудувати HTML-код для відображення зображення.
      $image = '<img src="' . $file_url . '" alt="' . $filename . '" width="300">';
    } else {
      $image = '';
    }

    $editLink = Link::fromTextAndUrl(t('Edit'), $url)->toString();

    // Якщо текст копірайту відсутній, виводимо "Empty".
    if (empty($copyright)) {
      if ($file) {
        $copyright = t('Empty copyright for image with fid=') . $fid;
      } else {
        $copyright = t('Not existent image with fid=') . $fid;
      }
    }

    // $response = ['#markup' => '<div>' . $content . '<p>' . $copyright . '</p>' . $editLink . '</div>' . $image];
    $div = [
      '#type' => 'container',
      '#attributes' => [
        'style' => 'float: left; margin-right: 60px',
      ],
      '#markup' => '<p>' . $copyright . '</p>' . $editLink,
    ];

    $response = [
      'div' => $div,
      'image' => [
        '#markup' => $image,
      ],
    ];

    return $response;
  }

}
