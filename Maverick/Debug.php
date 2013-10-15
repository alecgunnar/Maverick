<?php

/**
 * Debug function - prints all passed variables to screen
 *
 * @return null
 */
function dump() {
    $args   = func_get_args();
    $output = '';

    if(count($args)) {
        foreach($args as $a) {
            ob_start();
            var_dump($a);
            $dumped = ob_get_clean();

            $output .= '<pre style="background:#CCCCCC; border:1px solid #333333; padding:10px; margin:10px;">' . $dumped . '</pre>';
        }
    }

    print $output;
}

/**
 * Debug function - prints all passed variables to screen then dies
 *
 * @return null
 */
function _dump() {
    $args = func_get_args();

    if(count($args)) {
        foreach($args as $a) {
            dump($a);
        }
    }

    die('End.');
}