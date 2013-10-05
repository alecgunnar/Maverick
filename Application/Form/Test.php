<?php

namespace Application\Form;

class Test extends \Maverick\Lib\Form {
    private $withTpl = false;

    public function __construct($withTpl) {
        $this->withTpl = $withTpl;

        parent::__construct();
    }

    public function build() {
        $this->setName('testForm');

        if($this->withTpl) {
            $this->setTpl('testForm');
        }

        $this->addField('Input', 'test_text')
            ->label('Text')
            ->maxLength(25);

        $this->addField('Input_Email', 'test_email')
            ->label('Email Address')
            ->validate('IsEmail', 'That was not a valid email address!');

        $this->addField('Input_Password', 'test_password')
            ->label('Password');

        $this->addField('TextArea', 'test_textarea')
            ->label('Text Area')
            ->required('This cannot be left empty');

        $this->addField('Input_Submit', 'submit')
            ->value('Submit');
    }
    
    public function validate() { }
    public function submit() { }
}