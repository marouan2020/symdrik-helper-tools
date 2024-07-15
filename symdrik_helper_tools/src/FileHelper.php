<?php

namespace Drupal\symdrik_helper_tools;

use Drupal\Core\Entity\EntityBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\File\Exception\InvalidStreamWrapperException;
use GuzzleHttp\ClientInterface;
use Drupal\Core\File\FileSystemInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Drupal\Core\Entity\EntityStorageException;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\file\Entity\File;


/**
 * Class EmailHelper
 *
 * @package Drupal\symdrik_helper_tools
 */
class FileHelper {

  /**
   * @var ClientInterface
   */
  private  $httpClient;

  /**
   * @var FileSystemInterface
   */
  private $fileSystem;

  /**
   * DocumentService constructor.
   * @param ClientInterface $http_client
   */
  public function __construct(ClientInterface $http_client, FileSystemInterface $file_system) {
    $this->httpClient = $http_client;
    $this->fileSystem = $file_system;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('http_client'),
      $container->get('file_system')
    );
  }

  /**
   * Create entity file by uri.
   *
   * @param string $fileUri
   *
   * @return File
   * Can be Entity file.
   */
  public function  createFileEntityFromUri(string $fileUri){
    try {
      $file = File::create([
        'uid' => \Drupal::currentUser()->id(),
        'filename' => basename($fileUri),
        'uri' => $fileUri,
        'status' => 1,
      ]);
      $file->save();
      return $file;
    }
    catch (EntityStorageException $e) {
      return null;
    }
  }

  /**
   * Download file from specific url.
   *
   * @param string $sourceFileUrl
   * @param string $destinationFolderUri
   * @param string|null $destinationFileName
   *
   * @return string|null
   */
  public function downloadDocumentByUrl(string $sourceFileUrl, string $destinationFolderUri, string $destinationFileName=null) {
    try {
      $responseToGetFile = $this->httpClient->get($sourceFileUrl,['verify'=>false]);
      if (empty($responseToGetFile->getHeaders())) {
        return null;
      }
      
      $fileName = (!empty($destinationFileName))?$destinationFileName:$this->getFileNameFromContentDisposition($responseToGetFile->getHeaders()["Content-Disposition"][0]);
      /** @var \Drupal\file\FileRepositoryInterface $fileRepository */
      $fileRepository = \Drupal::service('file.repository');

      return $fileRepository->writeData($responseToGetFile->getBody()->getContents(), $destinationFolderUri."/".$fileName, FileSystemInterface::EXISTS_RENAME);
    }
    catch (InvalidStreamWrapperException | BadResponseException | RequestException $e) {
      return null;
    }
  }

  /**
   * Format file name from header content disposition.
   *
   * @param array $headerContentDisposition
   * @return array|string|string[]|null
   */
  private function getFileNameFromContentDisposition($headerContentDisposition) {
    if (preg_match('/.*?filename="(.+?)"/', $headerContentDisposition, $matches)) {
      return preg_replace('/\s+/', '', $matches[1]);
    }
    if (preg_match('/.*?filename=([^; ]+)/', $headerContentDisposition, $matches)) {
     return rawurldecode($matches[1]);
    }
    return preg_replace('/\s+/', '', str_replace("filename=","", $headerContentDisposition));
  }

  /**
   * Get file.
   *
   * @param $fileId
   * @return EntityBase|EntityInterface|File|null
   */
  public function getFileDocumentById($fileId) {
    return File::load($fileId);
  }
}
