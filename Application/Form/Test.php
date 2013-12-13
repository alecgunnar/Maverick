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

        $this->addField('Input', 'test_text')
            ->setLabel('Text')
            ->setMaxLength(25);

        $this->addField('Input_File', 'test_file')
            ->setLabel('File');

        $this->addField('Input_Email', 'test_email')
            ->setLabel('Email Address')
            ->validate('IsEmail', 'That was not a valid email address!');

        $group = $this->addFieldGroup('group1')
            ->setLabel('This is a Fieldset');

        $group->addField('Input_Password', 'test_password')
            ->setLabel('Password');

        $group->addField('TextArea', 'test_textarea')
            ->setLabel('Text Area');

        $group->addField('Input_CheckBox', 'test_checkbox')
            ->setLabel('Check Box')
            ->addLabel('Check this box')
            ->setValue('1');

        $this->addField('Input_Radio', 'test_radio')
            ->setLabel('Radio Buttons')
            ->addOptions(array('a' => '1',
                               'b' => '2',
                               'c' => '3'))
            ->addOption('d', '4');

        $this->addField('Select', 'test_select')
            ->setLabel('Select')
            ->setValue('e')
            ->addOptions(array('0' => 'This option is zero',
                               'a' => '1',
                               'b' => '2',
                               'c' => '3'))
            ->addGroup('Option Group', array('d' => '4', 'e' => '5', 'f' => '6'));

        $this->addField('Select', 'test_select_multiple')
            ->setLabel('Select Multiple')
            ->multiple()
            ->setValue('e')
            ->addOptions(array('a' => '1',
                               'b' => '2',
                               'c' => '3'));

        $this->addField('Input_Submit', 'submit')
            ->setValue('Submit');
    }
    
    public function validate() { }
    public function submit() {
        \Maverick\Lib\Http::location('/', 'Submission successful!');
    }
}