# testmcp

Этот проект создан для практики работы с GitHub Copilot и MCP.

## Быстрый старт

1. Клонируйте репозиторий:
   ```
   git clone https://github.com/wpdew/testmcp.git
   ```
2. Установите зависимости (если используется Composer):
   ```
   composer install
   ```
   или для папки public:
   ```
   cd public
   composer install
   ```
   В проекте используется пакет [wpdew/orderclass](https://packagist.org/packages/wpdew/orderclass).
3. Запустите локальный сервер:
   ```
   php -S localhost:8000 -t public
   ```
4. Откройте проект в браузере: http://localhost:8000

## Документация
- Все инструкции по работе с Copilot и MCP смотрите в файле `COPILOT_MCP_DOC.md`
- Ежедневные задачи — в `daily-tasks.md`

## Игнорируемые файлы
- `.DS_Store` добавлен в `.gitignore`

## Лицензия
MIT
