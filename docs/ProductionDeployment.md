# 1. Смена пароля для админа
Для обновления данных для аутентификации пользователя `admin@dev.dev`
можно использовать консольную комманду
`\console\controllers\UserController::actionChangeMainAdminPassword()`

В качестве параметров экшн принимает новый пароль, если его не указать - комманда сгенерирует
новый 10-ти значный пароль и выведет его в консоль.

## Пример использования
`./yii user/change-main-admin-password qwe123`

# 2. Создание нового пользователя
Для создания нового пользователя есть комманда
`\console\controllers\UserController::actionCreateNew()`

В качестве параметров данный экшн принимает e-mail, пароль и в качестве не обязательных
параметроа - имя пользователя и роль rbac.
Если имя пользователя оставить пустым - на его место подставится e-mail.

## Пример использования

Без добавления роли
`./yii user/create-new-user client@example.com qwe123 client`

С добавлением роли
`./yii user/create-new client@example.com qwe123 client admin`
