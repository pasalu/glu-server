To start the Vagrant box use:
vagrant up

Once the Vagrant box is running use:
vagrant ssh
to ssh in.

Once in to create the database use:
cd code
php artisan migrate:fresh
to create the database table(s).

To connect to the database use:
mysql --user=homestead --password=secret --database=homestead

To run the tests use:
composer test --verbose
or
phpunit

The routes to hit are in web.php
