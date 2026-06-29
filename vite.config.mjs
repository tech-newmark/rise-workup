// vite.config.mjs

import { defineConfig } from "vite";
import path from "path";
import fs from "fs";
import svgSpritemap from "vite-plugin-svg-spritemap";
import vue from "@vitejs/plugin-vue";
import autoprefixer from "autoprefixer";

// Базовые пути
const TEMPLATE_NAME = "rise-bags";
const TEMPLATE_PATH = `local/templates/${TEMPLATE_NAME}`;
const BASE_PATH = `/${TEMPLATE_PATH}`;
const DIST_PATH = `${TEMPLATE_PATH}/_dist`;
const COMPONENTS_BASE_PATH = `${TEMPLATE_PATH}/components`;

// Вспомогательная функция: безопасное чтение файла
const fileExists = (filePath) => {
  try {
    return fs.statSync(filePath).isFile();
  } catch {
    return false;
  }
};

// Функция для создания структуры _src в папке (только если папки нет)
function createSrcStructureIfNotExists(templatePath) {
  const srcPath = path.join(templatePath, "_src");

  // Проверяем, существует ли уже папка _src
  if (!fs.existsSync(srcPath)) {
    console.log(`📁 Создание папки _src: ${templatePath}`);
    fs.mkdirSync(srcPath, { recursive: true });

    // Создаем подпапки js, scss, images
    const subDirs = ["js", "scss", "images"];
    subDirs.forEach((dir) => {
      const dirPath = path.join(srcPath, dir);
      if (!fs.existsSync(dirPath)) {
        console.log(`   📁 Создание папки ${dir}/`);
        fs.mkdirSync(dirPath, { recursive: true });
      }
    });

    console.log(`   ✅ Структура _src создана`);
  } else {
    console.log(`   ✅ Структура _src уже создана`);
  }
}

// Функция для создания файлов шаблона компонента с нижним подчеркиванием.
// Только если уже нет рабочего template.php
function createComponentTemplateFiles(templatePath, componentName) {
  const cleanComponentName = componentName.replace(/^.*?\//, "");
  // Проверяем наличие рабочего template.php
  const hasTemplate = fileExists(path.join(templatePath, "template.php"));
  const hasResultModifier = fileExists(
    path.join(templatePath, "result_modifier.php"),
  );
  const hasComponentEpilog = fileExists(
    path.join(templatePath, "component_epilog.php"),
  );

  // Содержимое для _template.php
  const templatePhpContent = `<? 
		if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
		$this->setFrameMode(true);

		// includeComponentAssets('${cleanComponentName}');
	?>
	<?/* debug($arResult) */?>`;

  // Содержимое для _result_modifier.php
  const resultModifierContent = `<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>`;

  // Содержимое для _component_epilog.php
  const componentEpilogContent = `
		<? 
			if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
			// includeComponentAssets('${cleanComponentName}');
		?>
	`;

  // Создаем _template.php только если нет template.php
  if (!hasTemplate) {
    const templatePhpPath = path.join(templatePath, "_template.php");
    if (!fileExists(templatePhpPath)) {
      console.log(`   📄 Создание файла _template.php (т.к. нет template.php)`);
      fs.writeFileSync(templatePhpPath, templatePhpContent);
    }
  }

  // Создаем _result_modifier.php только если нет result_modifier.php
  if (!hasResultModifier) {
    const resultModifierPath = path.join(templatePath, "_result_modifier.php");
    if (!fileExists(resultModifierPath)) {
      console.log(
        `   📄 Создание файла _result_modifier.php (т.к. нет result_modifier.php)`,
      );
      fs.writeFileSync(resultModifierPath, resultModifierContent);
    }
  }

  // Создаем _component_epilog.php только если нет component_epilog.php
  if (!hasComponentEpilog) {
    const componentEpilogPath = path.join(
      templatePath,
      "_component_epilog.php",
    );
    if (!fileExists(componentEpilogPath)) {
      console.log(
        `   📄 Создание файла _component_epilog.php (т.к. нет component_epilog.php)`,
      );
      fs.writeFileSync(componentEpilogPath, componentEpilogContent);
    }
  }
}

// Поиск папок 3-го уровня вложенности от components/ (папки шаблонов)
function findComponentTemplateDirs(baseDir) {
  const components = [];

  function walk(currentDir, currentDepth) {
    let files;
    try {
      files = fs.readdirSync(currentDir);
    } catch {
      return;
    }

    for (const file of files) {
      const fullPath = path.join(currentDir, file);

      // Пропускаем, если это не директория
      if (!fs.statSync(fullPath).isDirectory()) {
        continue;
      }

      // Пропускаем только специальные системные папки
      if (file === "_src" || file === "_dist") {
        continue;
      }

      // НЕ пропускаем папки, начинающиеся с точки (например .default)
      // Пропускаем только . и .. (текущая и родительская директория)
      if (file === "." || file === "..") {
        continue;
      }

      // Текущая глубина +1 (так как мы заходим в новую папку)
      const newDepth = currentDepth + 1;

      if (newDepth === 3) {
        // 3-й уровень вложенности - это папка шаблона компонента
        // Например: components/bitrix/news.list/.default
        console.log(`🔍 Найден шаблон компонента: ${fullPath}`);

        // Создаем структуру _src
        createSrcStructureIfNotExists(fullPath);

        // Получаем относительный путь для имени компонента
        const relativePath = path
          .relative(COMPONENTS_BASE_PATH, fullPath)
          .replace(/\\/g, "/");

        // Создаем файлы шаблона компонента с нижним подчеркиванием
        // (только если нет аналогов без нижнего подчеркивания)
        createComponentTemplateFiles(fullPath, relativePath);

        // Заменяем точки и слэши на нижнее подчёркивание для безопасного имени
        const safeName = relativePath.replace(/\./g, "_").replace(/\//g, "_");

        components.push({
          name: safeName,
          path: fullPath,
        });

        // НЕ заходим в подпапки 3-го уровня (lang, images и т.д.)
        // так как это служебные папки
        continue;
      }

      // Продолжаем поиск только если еще не достигли 3-го уровня
      if (newDepth < 3) {
        walk(fullPath, newDepth);
      }
    }
  }

  // Проверяем существование базовой папки components
  if (!fs.existsSync(COMPONENTS_BASE_PATH)) {
    console.log(`📁 Создание папки components: ${COMPONENTS_BASE_PATH}`);
    fs.mkdirSync(COMPONENTS_BASE_PATH, { recursive: true });
  }

  // Начинаем поиск с глубины 0 (сама папка components)
  console.log("🔍 Проверка структуры компонентов...");
  walk(baseDir, 0);
  return components;
}

// Запускаем поиск и создание структуры
const detectedComponents = findComponentTemplateDirs(COMPONENTS_BASE_PATH);
const componentConfigs = Object.fromEntries(
  detectedComponents.map((comp) => [comp.name, comp.path]),
);

// Логирование найденных компонентов
if (detectedComponents.length === 0) {
  console.warn(
    "⚠️  Не найдено шаблонов компонентов (3-й уровень) в " +
      COMPONENTS_BASE_PATH,
  );
} else {
  console.log(`✅ Найдено шаблонов компонентов: ${detectedComponents.length}`);
  detectedComponents.forEach((c) => console.log(`   - ${c.name} → ${c.path}`));
}

// Глобальные точки входа
const componentPaths = {
  template: `${TEMPLATE_PATH}/_src/scss/template.scss`,
  script: fileExists(`${TEMPLATE_PATH}/_src/js/index.js`)
    ? `${TEMPLATE_PATH}/_src/js/index.js`
    : null,
};

const validComponentEntries = [];

Object.entries(componentConfigs).forEach(([key, componentDir]) => {
  const scssPath = `${componentDir}/_src/scss/index.scss`;
  const jsPath = `${componentDir}/_src/js/index.js`;

  const hasScss = fileExists(scssPath);
  const hasJs = fileExists(jsPath);

  if (!hasScss && !hasJs) {
    console.log(`ℹ️  Шаблон "${key}" не содержит файлов для сборки.`);
    return;
  }

  validComponentEntries.push({
    key,
    paths: {
      ...(hasScss && { scss: scssPath }),
      ...(hasJs && { js: jsPath }),
    },
    basePath: componentDir,
  });
});

// Формируем входные точки для Rollup
const rollupInput = {};

// Глобальный SCSS
if (fileExists(componentPaths.template)) {
  rollupInput.template = componentPaths.template;
} else {
  console.warn(`⚠️  Файл template.scss не найден: ${componentPaths.template}`);
}

// Глобальный JS
if (componentPaths.script) {
  rollupInput.script = componentPaths.script;
}

// Компоненты
validComponentEntries.forEach(({ key, paths }) => {
  if (paths.scss) {
    rollupInput[`${key}_scss`] = paths.scss;
  }
  if (paths.js) {
    rollupInput[`${key}_js`] = paths.js;
  }
});

// Маппинг для выходных путей CSS
const cssPathMapping = Object.fromEntries(
  validComponentEntries.map(({ key, basePath }) => [key, basePath]),
);

// Определяем путь для CSS-файлов
const getCssOutputPath = (fileName) => {
  if (fileName?.includes("template")) {
    return `template_styles.[hash].css`;
  }

  for (const [key, basePath] of Object.entries(cssPathMapping)) {
    if (fileName?.includes(key)) {
      return `${basePath.replace(TEMPLATE_PATH + "/", "")}/style.[hash].css`;
    }
  }

  return `template_styles.[hash].css`;
};

// Основная конфигурация
export default defineConfig({
  base: `${BASE_PATH}/_dist/`,
  publicDir: path.resolve(__dirname, `${TEMPLATE_PATH}/_src/public`), // Файлы отсюда будут скопированы в _dist как есть

  plugins: [
    vue(),
    svgSpritemap({
      pattern: `${TEMPLATE_PATH}/_src/sprite/**/*.svg`,
      filename: `sprite.svg`,
      prefix: "",
      svgo: {
        multipass: true,
        plugins: [
          { name: "cleanupAttrs", params: { removeEmptyAttrs: true } },
          {
            name: "removeAttrs",
            params: {
              attrs: ["fill", "fill-rule", "stroke", "stroke-width"],
            },
          },
        ],
      },
    }),
  ],

  css: {
    postcss: {
      plugins: [
        autoprefixer({
          overrideBrowserslist: ["> 2%", "last 5 versions", "not dead"],
        }),
      ],
    },
    preprocessorOptions: {
      scss: {
        api: "modern-compiler",
      },
    },
  },

  build: {
    sourcemap: true,
    manifest: "manifest.json",

    rollupOptions: {
      input: rollupInput,

      output: {
        // Обработка JS-файлов
        entryFileNames: (chunkInfo) => {
          const name = chunkInfo.name;

          if (name === "script") {
            return `template_scripts.[hash].js`;
          }

          if (name.endsWith("_js")) {
            const componentName = name.slice(0, -3);
            const basePath = cssPathMapping[componentName];
            if (basePath) {
              return `${basePath.replace(
                TEMPLATE_PATH + "/",
                "",
              )}/script.[hash].js`;
            }
          }

          return `[name].[hash].js`;
        },

        // Обработка статики
        assetFileNames: (assetInfo) => {
          const fileName = assetInfo.names?.[0];

          // CSS
          if (fileName?.match(/\.(css)$/i)) {
            return getCssOutputPath(fileName);
          }

          // Шрифты
          if (fileName?.match(/\.(woff|woff2)$/i)) {
            return `fonts/[name].[hash].[ext]`;
          }

          // Изображения
          if (fileName?.match(/\.(png|jpg|jpeg|gif|svg|webp)$/i)) {
            const originalPath = assetInfo.originalFileNames?.[0] || "";
            if (originalPath.includes("components")) {
              const match = originalPath.indexOf("components");
              const componentPath = originalPath
                .slice(match)
                .replace("/_src/img/", "/img/");
              return componentPath;
            }

            return `images/[name].[hash].[ext]`;
          }

          return `[name].[hash].[ext]`;
        },
      },
    },

    outDir: DIST_PATH,
    emptyOutDir: true,
    copyPublicDir: true,
  },

  resolve: {
    alias: {
      "@": path.resolve(__dirname, `${TEMPLATE_PATH}/_src`),
      "@scss": path.resolve(__dirname, `${TEMPLATE_PATH}/_src/scss`),
      "@img": path.resolve(__dirname, `${TEMPLATE_PATH}/_src/images`),
      "@fonts": path.resolve(__dirname, `${TEMPLATE_PATH}/_src/fonts`),
      "@public": path.resolve(__dirname, `${TEMPLATE_PATH}/_src/public`),
      "@vue-components": path.resolve(
        __dirname,
        `${TEMPLATE_PATH}/_src/vue-components`,
      ),
    },
  },
});
