<?php

namespace ride\web\tinymce\view;

use ride\library\mvc\view\View;

/**
 * View to display a javascript list, used by the dynamic lists of TinyMCE
 */
class TinymceListView implements View {

    /**
     * Name of the variable
     * @var string
     */
    private $varName;

    /**
     * Values for the variable
     * @var array
     */
    private $values;

    /**
     * Constructs a new view for a dynamic list of TinyMCE
     * @param string $varName name of the variable
     * @param array $values Values for the variable
     * @return null
     */
    public function __construct($varName, array $values) {
        $this->varName = $varName;
        $this->values = $values;
    }

    /**
     * Renders this view
     * @param boolean $return true to return the rendered value, false to write it to the output
     * @return null|string
     */
    public function render($return = true) {
        if (empty($this->values)) {
            return 'var ' . $this->varName . ' = new Array();';
        }

        $script = "var " . $this->varName . " = new Array(\n";
        foreach ($this->values as $value => $label) {
            $script .= "\t[\"" . $label . '", "' . $value . "\"],\n";
        }
        $script = substr($script, 0, -2) . "\n);\n";

        if ($return) {
            return $script;
        }

        echo $script;
    }

}
