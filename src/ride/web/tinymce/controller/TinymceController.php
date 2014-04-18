<?php

namespace ride\web\tinymce\controller;

use ride\library\event\EventManager;

use ride\web\mvc\controller\AbstractController;
use ride\web\tinymce\view\TinymceListView;

use \Exception;
use \TinyMCE_Compressor;

/**
 * Controller for dynamic image and link list
 */
class TinyMCEController extends AbstractController {

    /**
     * Event to generate the image list
     * @var string
     */
    const EVENT_PRE_IMAGE_LIST = 'tinymce.list.image.pre';

    /**
     * Event to generate the link list
     * @var string
     */
    const EVENT_PRE_LINK_LIST = 'tinymce.list.link.pre';

    /**
     * Values for the images variable
     * @var array
     */
    private $images = array();

    /**
     * Values for the links variable
     * @var array
     */
    private $links = array();

    /**
     * Default action for the compressor
     * @return string
     */
    public function indexAction() {
        // Handle incoming request if it's a script call
        if (TinyMCE_Compressor::getParam("js")) {
            // Default settings
            $tinyMCECompressor = new TinyMCE_Compressor(array(
            /*
             * Add any site-specific defaults here that you may wish to implement. For example:
             *
             *  "languages" => "en",
             *  "cache_dir" => realpath(dirname(__FILE__) . "/../../_cache"),
             *  "files"     => "somescript,anotherscript",
             *  "expires"   => "1m",
             */
            ));

            // Handle request, compress and stream to client
            $tinyMCECompressor->handleRequest();
        }
    }

    /**
     * Action to set the dynamic images list to the view
     *
     * Before setting the view, the event EVENT_PRE_IMAGE_LIST will be executed with this controller as argument.
     * Use this event to attach images to the dynamic images list.
     * @return null
     */
    public function imagesAction(EventManager $eventManager) {
        $eventManager->triggerEvent(self::EVENT_PRE_IMAGE_LIST, array('controller' => $this));

        $view = new TinymceListView('tinyMCEImageList', $this->getImages());

        $this->response->setView($view);
    }

    /**
     * Action to set the dynamic links list to the view
     *
     * Before setting the view, the event EVENT_PRE_LINK_LIST will be executed with this controller as argument.
     * Use this event to attach links to the dynamic links list.
     * @return null
     */
    public function linksAction(EventManager $eventManager) {
        $eventManager->triggerEvent(self::EVENT_PRE_LINK_LIST, array('controller' => $this));

        $view = new TinymceListView('tinyMCELinkList', $this->getLinks());

        $this->response->setView($view);
    }

    /**
     * Adds an image to the dynamic images list
     * @param string $image URL to the image
     * @param string $label label for the image
     * @return null
     */
    public function addImage($image, $label = null) {
        if (!is_string($image) || !$image) {
            throw new Exception('Could not add the image: provided image is empty or not a string');
        }

        if (!$label) {
            $label = $image;
        }

        $this->images[$image] = $label;
    }

    /**
     * Gets all the images of the dynamic images list
     * @return array Array with the URL as key and the label as value
     */
    public function getImages() {
        return $this->images;
    }

    /**
     * Adds a link to the dynamic links list
     * @param string $link URL of the link
     * @param string $label label for the link
     * @return null
     */
    public function addLink($link, $label = null) {
        if (!is_string($link) || !$link) {
            throw new Exception('Could not add the link: provided link is empty or not a string');
        }

        if (!$label) {
            $label = $link;
        }

        $this->links[$link] = $label;
    }

    /**
     * Gets all the links of the dynamic links list
     * @return array Array with the URL as key and the label as value
     */
    public function getLinks() {
        return $this->links;
    }

}
