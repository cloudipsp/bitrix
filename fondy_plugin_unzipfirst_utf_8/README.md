Модуль Fondy для 1C Bitrix.
=====

Данный 1 модуль подходит для версий Малый бизнес, Бизнес и Бизнес Веб-кластер.
--


#Инструкция по установке модуля оплаты Fondy под 1C Битрикс

Папку fondy.fondy нужно разместить в `{Корневой каталог сайта}/bitrix/modules/` установить нужную кодировку!

#После этого ативировать модуль(http://{your site}/bitrix/admin/partner_modules.php?lang=ru) и потом :

>1. Зайти в административную часть интернет магазина.

>2. Перейти на страницу "Платежные системы" ( "Магазин" -> "Настройки магазина" -> "Платежные системы" )

>3. Нажать на кнопку "Добавить платежную систему"

>4. Заполнить общие данные о платежной системе.

>5. Перейти на нужную вкладку ( "Физические лица" или "Юридические лица" ) и заполнить всю необходимую информацию

>6. Сделать платежную систему активной и нажать "Сохранить"


В настройках вашего мерчанта на Fondy необходимо указать ссылку возврата информации о статусе платежа на страницу `http://yoursite.com/bitrix/tools/fondy_result/fondy_result.php`

![1]

[1]: https://raw.githubusercontent.com/cloudipsp/bitrix/master/fondy_plugin_unzipfirst_utf_8/settings.png