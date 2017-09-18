#!/bin/sh
sudo chmod -R 777 var
php bin/console cache:clear --env=dev
php bin/console cache:clear --env=prod
sudo chmod -R 777 var

