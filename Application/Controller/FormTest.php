<?php

namespace Application\Controller;

class FormTest extends \Maverick\Lib\Controller {
    public function main() {
        $this->setPageTitle('Form Test');

        $form = new \Application\Form\Test;

        $this->setVariable('form', $form->render());
    }
}