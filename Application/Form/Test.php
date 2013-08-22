<?php

namespace Application\Form;

class Test extends \Maverick\Lib\Form {
    /**
     * Builds the form to be rendered later
     *
     * @return null
     */
    public function build() {
        $this->name = 'test';

        $this->addField('Input_Text', 'text_input')
            ->label('Text Input')
            ->validate('IsNumber');

        $this->addField('Input_Submit', 'submit_form')
            ->value('Submit Form');
    }

    /**
     * Validates the form
     *
     * @return null
     */
    public function validate() { }
}