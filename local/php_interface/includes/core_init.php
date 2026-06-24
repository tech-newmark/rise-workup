<?php
if (!function_exists('initBitrixCore')) {
  /**
   * Инициализирует ядро Битрикс
   * 
   * @param array|string $modules
   */
  function initBitrixCore($modules = ['popup'])
  {
    $modules = is_array($modules) ? $modules : [$modules];

    $availableModules = array_intersect($modules, ['popup', 'ajax', 'date', 'fx', 'json']);

    if (!empty($availableModules)) {
      \CJSCore::Init($availableModules);
    }
  }
}
