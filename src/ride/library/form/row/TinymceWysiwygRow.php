<?php

namespace ride\library\form\row;

use \ride\web\WebApplication;

/**
 * Wysiwyg implementation for TinyMCE
 */
class TinymceWysiwygRow extends WysiwygRow {

    /**
     * URL to retrieve the available images
     * @var string
     */
    protected $imagesUrl;

    /**
     * URL to retrieve the available links
     * @var string
     */
    protected $linksUrl;

    /**
     * Sets the instance of the web application
     * @param \ride\web\WebApplication $web Instance of the web application
     * @return null
     */
    public function setWeb(WebApplication $web) {
        $request = $web->getRequest();

        $this->setBaseUrl($request->getBaseScript());
        $this->setImagesUrl($web->getUrl('tinymce.images'));
        $this->setLinksUrl($web->getUrl('tinymce.links'));
    }

    /**
     * Sets the URL to retrieve the available images
     * @param string $imagesUrl URL to retrieve the available images
     * @return null
     */
    public function setImagesUrl($imagesUrl) {
        $this->imagesUrl = $imagesUrl;
    }

    /**
     * Sets the URL to retrieve the available links
     * @param string $linksUrl URL to retrieve the available links
     * @return null
     */
    public function setLinksUrl($linksUrl) {
        $this->linksUrl = $linksUrl;
    }

    /**
     * Gets all the javascript files which are needed for this row
     * @return array|null
     */
    public function getJavascripts() {
        return array(
            'tiny_mce/jquery.tinymce.js',
        );
    }

    /**
     * Gets all the inline javascripts which are needed for this row
     * @return array|null
    */
    public function getInlineJavascripts() {
        $properties = $this->properties;

        $properties['script_url'] = $this->baseUrl . '/tiny_mce/tiny_mce.js';
        $properties['document_base_url'] = $this->baseUrl;
        $properties['languages'] = $this->locale;

        if ($this->imagesUrl) {
            $properties['external_image_list_url'] = $this->imagesUrl;
        }

        if ($this->linksUrl) {
            $properties['external_link_list_url'] = $this->linksUrl;
        }

        $json = json_encode($properties);

        return array(
            '$("#' . $this->widget->getId() . '").tinymce(' . $json . ');',
        );
    }

}
