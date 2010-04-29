# Slave

The Slave abuses HTTP to install phpBB for you. He is a command-line PHP script that makes HTTP requests though phpBB 3.0's installation.

## Example

Example usage:

	./slave -u igor -e email@example.com --dbuser root --dbpass secret --dbname phpbb \
		localhost/phpBB3

## Credits

Great thanks to Chris "cs278" Smith for initial code.

## Requirements

* PHP 5.3

## Dependencies

Slave uses components from Zend Framework and Symfony 2.

## Assumptions

config.php must be writable by the webserver.