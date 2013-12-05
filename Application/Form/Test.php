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
            ->maxLength(25)
            ->tpl('field');

        $this->addField('Input_File', 'test_file')
            ->label('File');

        $this->addField('Input_Email', 'test_email')
            ->label('Email Address')
            ->validate('IsEmail', 'That was not a valid email address!');

        $this->addField('Input_Password', 'test_password')
            ->label('Password');

        $this->addField('TextArea', 'test_textarea')
            ->label('Text Area');

        $this->addField('Input_CheckBox', 'test_checkbox')
            ->label('Check Box')
            ->addLabel('Check this box')
            ->value('1');

        $this->addField('Input_Radio', 'test_radio')
            ->label('Radio Buttons')
            ->addOptions(array('a' => '1',
                               'b' => '2',
                               'c' => '3'))
            ->addOption('d', '4');

        $this->addField('Select', 'test_select')
            ->label('Select')
            ->value('e')
            ->addOptions(array('0' => 'This option is zero',
                               'a' => '1',
                               'b' => '2',
                               'c' => '3'))
            ->addGroup('Option Group', array('d' => '4', 'e' => '5', 'f' => '6'));

        $this->addField('Select', 'test_select_multiple')
            ->label('Select Multiple')
            ->multiple()
            ->value('e')
            ->addOptions(array('a' => '1',
                               'b' => '2',
                               'c' => '3'));

        $this->addField('Input_Submit', 'submit')
            ->value('Submit');
    }
    
    public function validate() { }
    public function submit() {
        dump('Submission successful!');
    }
}