# ExpensesWebsite

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/8b559408f3724d8ab92307d7c18a1b8c)](https://app.codacy.com/app/rechenmann/ExpensesWebsite?utm_source=github.com&utm_medium=referral&utm_content=Ruijan/ExpensesWebsite&utm_campaign=Badge_Grade_Settings)
[![codecov](https://codecov.io/gh/Ruijan/ExpensesWebsite/branch/master/graph/badge.svg)](https://codecov.io/gh/Ruijan/ExpensesWebsite)
[![Build Status](https://travis-ci.org/Ruijan/ExpensesWebsite.svg?branch=master)](https://travis-ci.org/Ruijan/ExpensesWebsite)
[![CodeFactor](https://www.codefactor.io/repository/github/ruijan/expenseswebsite/badge)](https://www.codefactor.io/repository/github/ruijan/expenseswebsite)
[![BCH compliance](https://bettercodehub.com/edge/badge/Ruijan/ExpensesWebsite?branch=master)](https://bettercodehub.com/results/Ruijan/ExpensesWebsite)

## How To
### Introduction
Every request is a HTTP request. It can be a GET or a POST request. 
Dev server: https://pixelnos-ledger-api.herokuapp.com
Prod server: https://pixelnos-ledger-api.herokuapp.com

To send a request:
Request: `connection/SignIn`
Current: `https://pixelnos-ledger-api.herokuapp.com/BackEnd/index.php?action=request`

Example in PHP:
```php
$url = 'https://pixelnos-ledger-api.herokuapp.com/BackEnd/index.php?action=connection/SignIn';
$data = array('email' => 'example@host.com', 'password' => '123456789');
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
```

### Connecting
*   SignIn: `connection/SignIn`. Required: `email` (string), `password` (string)
*   SignUp: `connection/SignUp`. Required: `email` (string), `password` (string), `first_name` (string), `last_name` (string)

### Accounts
*   Creation: `account/Create`. Required: `user_id` (int), `session_id` (int), `name` (string), `currency_id` (int), `current_amount` (float)
*   Retrieve one belonging to user: `account/Retrieve`. Required: `user_id` (int), `session_id` (int), `id` (int)
*   Retrieve all belonging to user: `account/RetrieveAll`. Required: `user_id` (int), `session_id` (int)
*   Delete: `account/Delete`. Required:  `category_id`(int), `session_id`(int), `user_id`(int)

### Currencies
*   Creation: `currency/Create`. Required: `name`(string),  `short_name`(string), `session_id`(int), `user_id`(int)
*   Retrieve all existing sub categories: `currency/RetrieveAll`. Required: `session_id`(int), `user_id`(int)
*   Delete: `currency/Delete`. Required:   `name`(string),  `short_name`(string), `user_id` (int), `session_id` (int)

### Categories
*   Creation: `category/Create`. Required: `name`(string), `session_id`(int), `user_id`(int)
*   Retrieve all existing categories: `category/RetrieveAll`. Required: `session_id`(int), `user_id`(int)

### SubCategories
*   Creation: `sub_category/Create`. Required: `name`(string),  `parent_id`(int), `session_id`(int), `user_id`(int)
*   Retrieve all existing sub categories: `sub_category/RetrieveAll`. Required: `session_id`(int), `user_id`(int)
*   Delete: `sub_category/Delete`. Required:  `category_id`(int), `user_id` (int), `session_id` (int)

### ExpenseStates
*   Creation: `expenses_state/Create`. Required: `name`(string),   `session_id`(int), `user_id`(int)
*   Retrieve all existing sub categories: `expenses_state/RetrieveAll`. Required: `session_id`(int), `user_id`(int)
*   Delete: `expenses_state/Delete`. Required:  `state_id`(int), `user_id` (int), `session_id` (int)

### Payees
*   Creation: `payee/Create`. Required: `name`(string),  `session_id`(int), `user_id`(int)
*   Retrieve all existing sub categories: `payee/RetrieveAll`. Required: `session_id`(int), `user_id`(int)
*   Delete: `payee/Delete`. Required:  `payee_id`(int), `user_id` (int), `session_id` (int)


## Install

### Prerequisites

#### WampServer or equivalent webserver
If you don't have any webserver install on your computer, please install one among the following list:
*   WampServer: http://www.wampserver.com/en/
*   Mamp: https://www.mamp.info/en/

#### XDebug for code coverage
Once installed, check your php version with:
```
php -i
```
and paste the result in https://xdebug.org/wizard.php. Follow the instructions on the page.

#### Composer
You need **Composer** in order to setup the project. If you don't have it installed, please follow this link to set it up:
*   **Windows**: https://getcomposer.org/doc/00-intro.md#installation-windows
*   **MacOS & Linux**: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos
For a complete description of **Composer** usage, please refer to https://getcomposer.org/doc/01-basic-usage.md

### Download and install
First, download the archive from Github: https://github.com/Ruijan/ExpensesWebsite.git. Click on the green icon in the top right corner. You can also clone the folder with:
```
git clone https://github.com/Ruijan/ExpensesWebsite.git
cd ExpensesWebsite
php composer.phar install
```

## Debug
### Setup PHPStorm
Please follow instructions to download and install PHPStorm here: https://www.jetbrains.com/phpstorm/
Once done, launch PHPStorm.

#### Setup PHP
Once started, go to **File->Settings->Languages & Frameworks->PHP**
In **PHP language level** select 7.2 (object type hint, abstract function override)
Then in CLI Interpreter, click on `...` to open a new window.
Create a new Interpreter by clicking on the `+` on the top left corner.
*   Name: `PHP`
* General
  * PHP executable: `C:\wamp64\bin\php\php7.2.10\php.exe` or your webserver php.exe file
* Additional
  * Debugger extention: `C:\wamp64\bin\php\php7.2.10\zend_ext\php_xdebug-2.6.1-7.2-vc15-x86_64.dll` or the path to your webserver debugger dll.
Apply changes.

#### Setup Coverage
Then go to **File->Settings->Build, Execution, Deployment->Coverage**. Select `Replace active suites with the new one` if you are low on memory. If not checked, check the option `Activate Coverage View`. 

#### Run and debug unit tests
To run the unit tests, go to **Run->Edit Configurations**. Add a new configuration by clicking on `+` on the top left corner of the window. Then select PHPUnit. Define the configuration as follow:
*   Directory: `C:\wamp64\www\Expenses\Website\`
*   Check box **Use alternative configuration file** with `C:\wamp64\www\Expenses\Website\phpunit.xml`
Apply changes and click on ok.

To run the unit tests, right click on the folder **BackEnd** in the project's directory tree and then on **Run BackEnd (PHPUnit)**

## Adding new features
There are many features to implement in this project. There is a list available for each project (BackEnd and FrontEnd). Have a look at it before implementing your own, there might already be a complete descritpion
Please check that your implementation does not break previous code by running unit tests and code coverage during its implementation. Always check your own code.

#### Git
Before adding new features to the project, create a new git branch locally
```
git branch nameOfTheFeature
git checkout nameOfTheFeature
```
Regularly commit and push your code online to avoid losing code:
```
git add fileToAdd
git commit -m "description of the modifications"
git push
```
Once you think that everything has been implemented, go to https://github.com/Ruijan/ExpensesWebsite/pulls and create a new pull request with your branch. In the name of the pull request, precise if you are solving a issue by adding **Closes #NumberOfTheIssue**. Validate and wait for all the automatic quality tools to run. Ideally wait for someone else to check your code.
