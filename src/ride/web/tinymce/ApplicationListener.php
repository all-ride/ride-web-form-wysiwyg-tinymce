<?php

namespace ride\web\tinymce;

use ride\library\event\Event;
use ride\library\image\io\ImageFactory;
use ride\library\mvc\Request;
use ride\library\system\file\browser\FileBrowser;
use ride\library\system\file\File;

use ride\web\tinymce\controller\TinymceController;

/**
 * Application listener for TinyMCE
 */
class ApplicationListener {

    /**
     * Generates a link list from the files in the public directory
     * @param \ride\library\event\Event $event
     * @param \ride\library\system\file\browser\FileBrowser $fileBrowser
     * @return null
     */
    public function prepareLinkList(Event $event, FileBrowser $fileBrowser) {
        $controller = $event->getArgument('controller');
        $public = $fileBrowser->getPublicDirectory();

        $directory = $public->getChild('upload');
        $public = $public->getAbsolutePath();

        $this->addDirectoryToLinkList($directory, $controller, $public);
    }

    /**
     * Adds the provided directory to the TinyMCE editor
     * @param ride\library\system\files\File $directory Directory to add
     * @param ride\web\tinymce\controller\TinymceController $controller Instance of the TinyMCE controller
     * @param string $absolutePath Absolute path where the files should reside
     * @return null
     */
    protected function addDirectoryToLinkList(File $directory, TinymceController $controller, $absolutePath) {
        if (!$directory->exists() || !$directory->isDirectory()) {
            return;
        }

        $files = $directory->read();
        foreach ($files as $file) {
            if ($file->isDirectory()) {
                $this->addDirectoryToLinkList($file, $controller, $absolutePath);

                continue;
            }

            $file = $file->getAbsolutePath();
            if (strpos($file, $absolutePath) !== 0) {
                continue;
            }

            $file = str_replace($absolutePath . '/', '', $file);

            $controller->addLink($file, $file);
        }
    }

    /**
     * Generates a link list from the files in the public directory
     * @param \ride\library\event\Event $event
     * @param \ride\library\system\file\browser\FileBrowser $fileBrowser
     * @return null
     */
    public function prepareImageList(Event $event, FileBrowser $fileBrowser, ImageFactory $imageFactory, Request $request) {
        $controller = $event->getArgument('controller');
        $public = $fileBrowser->getPublicDirectory();
        $baseUrl = $request->getBaseUrl();
        $extensions = $imageFactory->getExtensions();

        $directory = $public->getChild('upload');
        $filePath = $directory->getAbsolutePath();
        $public = $public->getAbsolutePath();

        $this->addDirectoryToImageList($directory, $controller, $filePath, $public, $baseUrl, $extensions);
    }

    /**
     * Adds the images from a directory to the image list
     * @param zibo\library\filesystem\File $directory Directory to add
     * @param zibo\tinymce\controller\TinyMCEController $controller Instance of
     * the TinyMCE controller
     * @param string $filePath Path of the file on the server
     * @param string $urlPath Path of the file in the URL
     * @param string $baseUrl Base URL
     * @param array $extensions Extensions to read
     * @return null
     */
    protected function addDirectoryToImageList(File $directory, TinyMCEController $controller, $filePath, $urlPath, $baseUrl, array $extensions) {
        $files = $directory->read();
        foreach ($files as $file) {
            if ($file->isDirectory()) {
                $this->addImageDirectory($file, $controller, $filePath, $urlPath, $baseUrl, $extensions);

                continue;
            }

            if (!in_array($file->getExtension(), $extensions)) {
                continue;
            }

            $path = $file->getAbsolutePath();

            $controller->addImage(str_replace($urlPath, $baseUrl, $path), str_replace($filePath, '', $path));
        }
    }

}
