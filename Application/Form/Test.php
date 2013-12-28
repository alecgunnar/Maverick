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
        $this->setTpl('testForm');
        $this->allowFileUploads();

        $this->renderFieldsWithFormTpl();

        $this->addField('Input_Text', 'test_text')
            ->setLabel('Text')
            ->setMaxLength(25)
            ->setValue('This is the value!')
            ->required();

        $this->addField('Input_Number', 'test_number')
            ->setLabel('Number')
            ->prepend('$')
            ->append('.00')
            ->setSize(5);

        $this->addField('Input_File', 'test_file')
            ->setLabel('File');

        $group = $this->addFieldGroup('group1')
            ->setLabel('This is a Fieldset');

        $email = $group->addField('Input_Email', 'test_email')
            ->setLabel('Email Address')
            ->validate('IsEmail', 'That was not a valid email address!');

        $email->attach('Input_Text', 'phone')
            ->required('Enter a phone number!')
            ->attach('Select', 'countryCode')
            ->addOptions(array(0 => '+1', 1 => '+2'));

        $group->addField('Input_Password', 'test_password')
            ->setLabel('Password')
            ->required();

        $group->addField('TextArea', 'test_textarea')
            ->setLabel('Text Area');

        $groupChecks = $this->addFieldGroup('checkboxGroup')
            ->setLabel('Check Boxes');

        $groupChecks->addField('Input_CheckBox', 'test_checkbox')
            ->setLabel('Check Box')
            ->addLabel('Check this box')
            ->setValue('1')
            ->checkedValue(0)
            ->checked();

        $groupChecks->addField('Input_CheckBox', 'test_checkbox_2')
            ->setLabel('Check Box #2')
            ->addLabel('Check this box')
            ->setValue('1')
            ->checkedValue(0)
            ->checked();

        $this->addField('Input_Radio', 'test_radio')
            ->setLabel('Radio Buttons')
            ->addOptions(array('a' => '1',
                               'b' => '2',
                               'c' => '3'))
            ->addOption('d', '4')
            ->setValue('b');

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
            ->setValue('c')
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