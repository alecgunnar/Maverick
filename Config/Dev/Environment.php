<?php

return array('enable_ob'      => false,
             'display_errors' => true,
             'log_errors'     => true,
             'error_log_file' => ROOT_PATH . 'Logs' . DS . 'PHPErrors/' . date('n-j-Y') . '.txt',
             'email_errors'   => false);