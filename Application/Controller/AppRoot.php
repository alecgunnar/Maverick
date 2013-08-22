<?php

namespace Application\Controller;

class AppRoot extends \Maverick\Lib\Controller {
    public function main() {
        $this->output->addCssFile('main');

        $this->startSession();
    }

    private function startSession() {
        $session = \Maverick\Lib\Session::getInstance();
        $session->setUserModel(new \Application\Model\User(array('username' => 'alec')));
    }
}