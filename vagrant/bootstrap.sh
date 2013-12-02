#!/bin/bash

sudo apt-get update
sudo apt-get install -y curl git apache2 libapache2-mod-php5 php5-mcrypt
rm -rf /var/www
ln -fs /website /var/www
sudo a2enmod rewrite
sudo service apache2 restart