<?php

class Freak_EventDispatcher_View extends Zend_View {
    /**
     * Processes a view script and returns the output.
     *
     * @param string $name The script name to process.
     * @return string The script output.
     */
    public function render($name)
    {
        return include $this->_script($name);
    }
}
