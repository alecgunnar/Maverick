<?php

namespace Application\Controller;

use \Maverick\Lib\Output;

class Test extends \Maverick\Lib\Controller {
    public function main($withTpl='') {
        Output::setPageTitle('Testing Form');

        $form = new \Application\Form\Test($withTpl ? true : false);
        $this->setVariable('content', $form->render());
    }
}