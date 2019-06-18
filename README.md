# wp-ipaytotal-woocommerce

Please Note API key is mandatory to complete the integration 
Steps to get the API key 

Login to Merchant dashboard by clicking on the link https://ipaytotal.solutions
Click on the API section in the left menu 
Under the API you will see generate API key , click on generate 
After generating the API key , click on save " Please note : without saving the API , it will not work " 
Paste the API key in your Wordpress iPayTotal payment plugin/ or share the API with your developer if you are going for direct API integration then click on save .
Test the gateway with the following test card details 
4242424242424242 
11/2023 1
23
Once test is successful please check the transaction in your merchant dashboard . 
reply to this email with the successful test transaction screenshot . 
We will activate your account for live transactions. 



## Develop

To install [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer), the  [WordPress Coding Standards](https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards), [WP_Mock](https://github.com/10up/wp_mock) (and its [PHP Unit 7](https://github.com/sebastianbergmann/phpunit) dependency) and wordpress-develop testing environment run:

```
composer install
```

### WordPress Coding Standards

To see WordPress Coding Standards errors run:

```
vendor/bin/phpcs
```

To automatically correct them where possible run:

```
vendor/bin/phpcbf
```

### WP_Mock Tests

WP_Mock tests can be run with:

```
phpunit -c tests/wp-mock/phpunit.xml
```

### WordPress-Develop Tests

The wordpress-develop tests are configured to require a local [MySQL database](https://dev.mysql.com/downloads/mysql/) (which gets wiped each time) and this plugin is set to require a database called `wordpress_tests` and a user named `wordpress-develop` with the password `wordpress-develop`. 

To setup the database, open MySQL shell:

```
mysql -u root -p
```

Create the database and user, granting the user full permissions:

```
CREATE DATABASE wordpress_tests;
CREATE USER 'wordpress-develop'@'%' IDENTIFIED WITH mysql_native_password BY 'wordpress-develop'
GRANT ALL PRIVILEGES ON wordpress_tests.* TO 'wordpress-develop'@'%';
```

```
quit
```

The wordpress-develop tests can then be run with:

```
phpunit -c tests/wordpress-develop/phpunit.xml 
```

### Code Coverage

Code coverage reporting requires [Xdebug](https://xdebug.org/) installed. Reports are output to `tests/wordpress-develop/reports` and `tests/wp-mock/reports`.

