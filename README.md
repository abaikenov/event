# Yii 2 Event System

Данный проект является решением тестового задания RGK GROUP

Потраченное время - 12 часов

Ссылка на резюме - [ссылка](https://hh.kz/resume/e23510e4ff0106f0b10039ed1f50614b4f7150) 

============================

На странице Уведомления(CRUD), находится функционал немедленной отправки ввиде icon-button, рядом с view, update, delete

============================

В форме "Уведомление" в списке получателей, есть чекбокс "себе". При ее отметке, 
уведомление уходит тому, кто инициировал событие. Нужно для регистрации, чтобы отправиь код авторизации

============================

<strong> Чтобы добавить новый тип уведомления (например Push-уведомление): </strong>

 * Зайти в раздел - "Управление сайтом -> Типы уведомления"
 * Жмём "Создать тип"
 * Вводим заголовок, наименование (краткое слово на латинице, например push) и код. Доступны следующие переменные и функции:

```php
$this->from;  	//User объект
$this->to;		//User объект
$this->text;	//string
$this->title;	//string

$this->sendMail($this->from->email, $this->to->email, $this->title, $this->text) // отправка email
$this->saveToDb($this->from, $this->to, $this->title, $this->text) // сохранение в базу
```
Все остальное генерируются.

============================

Проект включает в себя тесты - функциональные и unit тесты.

Чтобы их запустить нужно добавить в переменную среду PATH следующий путь: 

```
<directory>/vendor/bin
```
и вызвать следующие команды из директории tests:

```
codecept run functionl;
codecept run unit;
```