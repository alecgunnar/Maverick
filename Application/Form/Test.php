<?php

namespace Application\Form;

class Test extends \Maverick\Lib\Form {
    public function build() {
        $this->setName('testForm');

        $this->addField('Input', 'test_text')
            ->label('Text');
        $this->addField('Input_Email', 'test_email')
            ->label('Email Address')
            ->validate('IsEmail', 'That was not a valid email address!');
        $this->addField('TextArea', 'test_textarea')
            ->label('Text Area')
            ->required('This cannot be left empty');
        $this->addField('Input_Submit', 'submit')
            ->value('Submit');
    }
    
    public function validate() { }
    public function submit() { }
}