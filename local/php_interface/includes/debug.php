<?php

if (!function_exists('debug')) {
  /**
   * Простая функция для отладки
   * 
   * @param mixed $data
   * @param bool $die
   */
  function debug($data, $die = false)
  {
    echo '<pre>' . print_r($data, true) . '</pre>';

    if ($die) {
      die('DEBUG STOP');
    }
  }
}
