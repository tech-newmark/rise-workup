<?php
// /local/php_interface/classes/ViteManifest.php

class ViteManifest
{
  private $manifest = null;
  private $templateName;
  private $templatePath;
  private $manifestPath;
  private $isManifestExists = false;
  private $buildDir = '_dist';

  public function __construct($templateName = 'rise-bags')
  {
    $this->templateName = $templateName;
    $this->templatePath = '/local/templates/' . $templateName;
    $this->manifestPath = $_SERVER['DOCUMENT_ROOT'] . $this->templatePath . '/' . $this->buildDir . '/manifest.json';

    $this->isManifestExists = file_exists($this->manifestPath);

    if ($this->isManifestExists) {
      $content = file_get_contents($this->manifestPath);
      $this->manifest = json_decode($content, true);
    }
  }

  /**
   * Получить путь к глобальному CSS файлу
   */
  public function getTemplateCss()
  {
    if (!$this->isManifestExists) {
      return $this->templatePath . '/' . $this->buildDir . '/template_styles.css';
    }

    $sourceKey = 'local/templates/rise-bags/_src/scss/template.scss';

    if (isset($this->manifest[$sourceKey])) {
      return $this->templatePath . '/' . $this->buildDir . '/' . $this->manifest[$sourceKey]['file'];
    }

    return $this->templatePath . '/' . $this->buildDir . '/template_styles.css';
  }

  /**
   * Получить путь к глобальному JS файлу
   */
  public function getTemplateJs()
  {
    if (!$this->isManifestExists) {
      return $this->templatePath . '/' . $this->buildDir . '/template_scripts.js';
    }

    $sourceKey = 'local/templates/rise-bags/_src/js/index.js';

    if (isset($this->manifest[$sourceKey])) {
      return $this->templatePath . '/' . $this->buildDir . '/' . $this->manifest[$sourceKey]['file'];
    }

    return $this->templatePath . '/' . $this->buildDir . '/template_scripts.js';
  }

  /**
   * Получить путь к CSS файлу компонента
   */
  // public function getComponentCss($componentPath)
  // {

  //   if (!$this->isManifestExists) {
  //     $folderPath = str_replace('.', '/', $componentPath);

  //     return $this->templatePath . '/components/bitrix/' . $folderPath . '/style.css';
  //   }

  //   $sourceKey = 'local/templates/rise-bags/components/bitrix/' . $componentPath . '/_src/scss/index.scss';
  //   debug($componentPath);

  //   debug($sourceKey);
  //   if (isset($this->manifest[$sourceKey])) {
  //     return $this->templatePath . '/' . $this->buildDir . '/' . $this->manifest[$sourceKey]['file'];
  //   }

  //   $folderPath = str_replace('.', '/', $componentPath);
  //   return $this->templatePath . '/components/bitrix/' . $folderPath . '/style.css';
  // }

  public function getComponentCss($componentPath)
  {
    $namespace = 'bitrix';

    // Если есть двоеточие, разделяем неймспейс и путь (например, custom:cards/article-card)
    if (strpos($componentPath, ':') !== false) {
      $parts = explode(':', $componentPath, 2);
      if (!empty($parts[0])) {
        $namespace = $parts[0];
        $componentPath = $parts[1];
      }
    }

    if (!$this->isManifestExists) {
      return $this->templatePath . '/components/' . $namespace . '/' . $componentPath . '/style.css';
    }

    $sourceKey = 'local/templates/rise-bags/components/' . $namespace . '/' . $componentPath . '/_src/scss/index.scss';
    if (isset($this->manifest[$sourceKey])) {
      return $this->templatePath . '/' . $this->buildDir . '/' . $this->manifest[$sourceKey]['file'];
    }

    return $this->templatePath . '/components/' . $namespace . '/' . $cleanPath . '/style.css';
  }

  /**
   * Получить путь к JS файлу компонента
   */
  public function getComponentJs($componentPath)
  {
    if (!$this->isManifestExists) {
      $folderPath = str_replace('.', '/', $componentPath);
      return $this->templatePath . '/components/bitrix/' . $folderPath . '/script.js';
    }

    $sourceKey = 'local/templates/rise-bags/components/bitrix/' . $componentPath . '/_src/js/index.js';

    if (isset($this->manifest[$sourceKey])) {
      return $this->templatePath . '/' . $this->buildDir . '/' . $this->manifest[$sourceKey]['file'];
    }

    $folderPath = str_replace('.', '/', $componentPath);
    return $this->templatePath . '/components/bitrix/' . $folderPath . '/script.js';
  }

  /**
   * Получить URL изображения (для использования в PHP)
   */
  public function getImageUrl($imagePath)
  {
    if ($this->isManifestExists) {
      $imageName = basename($imagePath);

      foreach ($this->manifest as $key => $value) {
        if (strpos($key, $imageName) !== false || (isset($value['src']) && strpos($value['src'], $imageName) !== false)) {
          return $this->templatePath . '/' . $this->buildDir . '/' . $value['file'];
        }
      }
    }

    // Если не нашли в манифесте или нет манифеста
    return $this->templatePath . '/' . $this->buildDir . '/images/' . basename($imagePath);
  }

  public function hasManifest()
  {
    return $this->isManifestExists;
  }

  public function debug()
  {
    echo '<pre>';
    echo "=== ОТЛАДКА VITE MANIFEST ===\n";
    echo "Шаблон: " . $this->templateName . "\n";
    echo "Путь к манифесту: " . $this->manifestPath . "\n";
    echo "Манифест загружен: " . ($this->isManifestExists ? 'ДА' : 'НЕТ') . "\n";

    if ($this->isManifestExists) {
      echo "\n--- Примеры ключей манифеста ---\n";
      $i = 0;
      foreach ($this->manifest as $key => $value) {
        $i++;
        if ($i > 5) break;
        echo "\nКЛЮЧ: " . $key . "\n";
        echo "  file: " . $value['file'] . "\n";
        if (isset($value['src'])) {
          echo "  src: " . $value['src'] . "\n";
        }
      }

      echo "\n--- Результаты методов ---\n";
      echo "getTemplateCss(): " . $this->getTemplateCss() . "\n";
      echo "getTemplateJs(): " . $this->getTemplateJs() . "\n";
      echo "getImageUrl('hero-bg.png'): " . $this->getImageUrl('hero-bg.png') . "\n";
    }
    echo '</pre>';
  }
}
