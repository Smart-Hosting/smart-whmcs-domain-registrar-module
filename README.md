<p align="center"><a href="https://smarthosting.co.uk" target="_blank"><img width="150"src="https://avatars1.githubusercontent.com/u/25201851?v=3&s=200"></a></p>

## About
This is a custom WHMCS module built for our resellers who would like to register domains directly though [us] (https://smarthosting.co.uk).

## Upgrading from our old module
If you've been running our old domain registrar module (pre February 2017), you'll need to do a few extra things.

1. Follow the installation instructions below
2. Deactivate and delete the old registrar module, which is called `SmarthostingAPI`
3. Run the following SQL on your WHMCS database using phpMyAdmin (found in your cPanel):
```
UPDATE `tbldomains` SET `registrar` = 'smarthosting' WHERE `registrar` LIKE 'smarthostingapi';
UPDATE `tbldomainpricing` SET `autoreg` = 'smarthosting' WHERE `autoreg` LIKE 'smarthostingapi';
```

## Installation
1. Download the module via the following [link](https://github.com/Smart-Hosting/smart-whmcs-domain-registrar-module/releases/latest)
1. Copy the `smarthosting` directory into the respective WHMCS directory in your WHMCS setup: `/whmcs/modules/registrars/`
2. Go to WHMCS, Setup, Domain Registrars and activate the `Smart Hosting` registrar
3. Enter your API username and API secret. These can be obtained via your [client area](https://www.bestwebhosting.co.uk/client/account/apikeys).
4. Choose to pay via credit card. You must have one on file. This will ensure domains are registered/renewed when your WHMCS requests. If you do not want to use a credit card, you must have a credit balance on your account with Smart Hosting.

## Help
If you have any questions or problems please submit a support ticket via our [client area](https://www.bestwebhosting.co.uk/client).

## Security Vulnerabilities

If you discover a security vulnerability within this project, please submit a support ticket via our [client area](https://www.bestwebhosting.co.uk/client). All security vulnerabilities will be promptly addressed.

## License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
