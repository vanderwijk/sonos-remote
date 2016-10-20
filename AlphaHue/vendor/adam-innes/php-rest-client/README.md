# PHP RESTful Client Library

## Synopsis
This is an easy to use client for [RESTful web services](https://en.wikipedia.org/wiki/Representational_state_transfer).

## Setup
### Installation with Composer.
Clone the repository.
```
$ git clone https://github.com/innesian/PhpRestClient.git
```
Install Composer in your project using cURL (command below) or [download the composer.phar directly](http://getcomposer.org/composer.phar).
```
$ curl -sS http://getcomposer.org/installer | php
```
Let Composer install the project dependencies:
```
$ php composer.phar install
```
Once installed, include the autoloader in your script.
```php
<?php
include_once 'vendor/autoload.php'; // Path to autoload.php file.
$rest = new \PhpRestClient\PhpRestClient('http://base.url/to/api/');
```
### (or) add PhpRestClient as a dependency to your REST project using Composer.
Create a *composer.json* file in your project and add `adam-innes/php-rest-client` as a required dependency.
```
{
    "require": {
        "adam-innes/php-rest-client": "1.0.*"
    }
}
```
## Usage
### Standard Requests
```php
$rest = new \PhpRestClient\PhpRestClient('http://base.url/to/api');

/** Get Example **/
# Set custom headers.
$headers = array(
    'CURLOPT_VERBOSE' => true,
);
# The get function will take a query string or array of parameters.
$response = $rest->get('account/information', 'variable=1&variable=2', $headers);

/** Put Example **/
$params['variable_1'] = 'value_1';
$params['variable_2'] = 'value_2';
$response = $rest->put('user/information', $params);

/** Post Example **/
$params['variable_1'] = 'value_1';
$params['variable_2'] = 'value_2';
$response = $rest->post('user/information', $params);

/** Delete Example **/
$response = $rest->delete('delete/user/5');
```
### Basic and Digest Authentication
The `setAuthentication()` function will set Basic or Digest authenication headers for the remainder of the session unless explicitly unset. 

Authentication uses Basic by default. The `unsetAuthentication()` function will clear out the authentication headers.
```php
$rest = new \PhpRestClient\PhpRestClient('http://base.url/to/api');
# Set Basic Authentication Headers.
$rest->setAuthentication('myUsername', 'myPassword', CURLAUTH_DIGEST);
$rest->get('account/information');
# Unset the Authentication headers.
$rest->unsetAuthentication();
```
