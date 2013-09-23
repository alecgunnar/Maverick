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
        $this->addField('Input_Submit', 'submit')
            ->value('Submit');
    }
    
    public function validate() { }
    public function submit() { }
}