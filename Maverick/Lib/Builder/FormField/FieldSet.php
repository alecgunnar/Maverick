<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_FormField_FieldSet extends Builder_FormField {
    /**
     * Sets up the field
     *
     * @param  string $name
     * @return null
     */
    public function __construct($legend) {
        parent::__construct('fieldset');

        if($legend) {
            $legendTag = new \Maverick\Lib\Builder_Tag('legend');
            $legendTag->text($legend);

            $this->html($legendTag->render());
        }
    }
}