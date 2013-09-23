<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Test extends \Maverick\Lib\Controller {
    public function main() {
        Output::setPageTitle('Testing Form');

        $form = new \Application\Form\Test;
        $this->setVariable('content', $form->render());
    }
}