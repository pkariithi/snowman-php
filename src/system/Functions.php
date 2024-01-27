<?php

if(!function_exists('dd')) {
  function dd($var, $die = true) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';

    if($die) {
      die();
    }
  }
}