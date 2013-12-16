<?php

/**
 * @package Maverick Framework
 * @author  Alec Carpenter
 */

namespace Maverick\Lib;

class Builder_Tag {
    /**
     * The type of tag
     *
     * @var string $tag
     */
    private $type = '';

    /**
     * Is the tag self closing?
     *
     * @var boolean $selfClosing
     */
    private $selfClosing = false;

    /**
     * The attributes for the tag
     *
     * @var array $attributes
     */
    private $attributes = array();

    /**
     * The content of the tag
     *
     * @var string $content
     */
    private $content = '';

    /**
     * Sets up the tag
     *
     * @param  string $tagType=''
     * @return null
     */
    public function __construct($type='') {
        $this->type = $type;
    }

    /**
     * Sets the tag type
     *
     * @param  string $tagType
     * @return self
     */
    public function setType($tagType) {
        $this->type = $tagType;

        return $this;
    }

    /**
     * Gets the type of the tag
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Tells wether the tag closes its self
     *
     * @return self
     */
    public function isSelfClosing() {
        $this->selfClosing = true;

        return $this;
    }

    /**
     * Adds attributes
     *
     * @param  array $attributes=null
     * @return self
     */
    public function addAttributes(array $attributes=null) {
        if(!is_null($attributes)) {
            $this->attributes = array_merge($this->attributes, $attributes);
        }

        return $this;
    }

    /**
     * Adds an attribute
     *
     * @param  string $name
     * @param  string $value
     * @return self
     */
    public function addAttribute($name, $value) {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Gets all attributes
     *
     * @return array
     */
    public function getAttributes($name) {
        return $this->attributes;
    }

    /**
     * Gets the value of an attribute
     *
     * @param  string $name
     * @return string
     */
    public function getAttribute($name) {
        if(array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * An alias for addAttribute
     *
     * @param  string $name
     * @param  string $value
     * @return self
     */
    public function attr($name, $value) {
        $this->attributes[$name] = $value;

        return $this;
    }

    /**
     * Sets the content of the tag, overrides any that exists
     *
     * @param  string $content
     * @return self
     */
    public function setContent($content) {
        $this->content = $content;

        return $this;
    }

    /**
     * Adds content to the tag
     *
     * @param  string $content
     * @return self
     */
    public function addContent($content) {
        $this->content .= $content;

        return $this;
    }

    /**
     * Appends content
     *
     * @param  string $content
     * @return self
     */
    public function appendContent($content) {
        $this->content = $content . $this->content;

        return $this;
    }

    /**
     * An alias for addContent
     *
     * @param  string $text
     * @return self
     */
    public function text($text) {
        $this->content .= $text;

        return $this;
    }

    /**
     * Renders the attributes
     *
     * @return string
     */
    private function renderAttributes() {
        $return = '';

        if(count($this->attributes)) {
            foreach($this->attributes as $l => $v) {
                $return .= ' ' . $l . '="' . str_replace('"', '\"', $v) . '"';
            }
        }

        return $return;
    }

    /**
     * Renders the tag
     *
     * @return string
     */
    public function render() {
        $return = '';

        $attributes = $this->renderAttributes();

        if($this->selfClosing) {
            $return = '<' . $this->type . $attributes  . ' />';
        } else {
            $return = '<' . $this->type . $attributes . '>' . $this->content . '</' . $this->type . '>';
        }

        return $return;
    }
}