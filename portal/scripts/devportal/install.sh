#! /bin/bash
home=$PWD
yum -y install epel-release.noarch
yum -y install https://repo.ius.io/ius-release-el7.rpm
yum -y install iptables-services
systemctl stop firewalld && sudo systemctl start iptables
firewall-cmd --state
systemctl disable firewalld
systemctl mask firewalld
systemctl enable iptables
iptables -L --line-numbers -nv
sed -i 's/enforcing/disabled/g' /etc/selinux/config
setenforce 0
yum -y update
iptables -F
chkconfig iptables off
yum -y install nginx
curl -sS https://downloads.mariadb.com/MariaDB/mariadb_repo_setup | bash
yum -y install MariaDB-server MariaDB-client
yum -y install php73-cli.x86_64 php73-pdo.x86_64 php73-mbstring.x86_64 php73-common.x86_64 php73-bcmath.x86_64 php73-fpm-nginx.noarch php73-gd.x86_64 php73-xml.x86_64 php73-json.x86_64 php73-opcache.x86_64 php73-mysqlnd.x86_64
yum -y install patch
yum -y install pwgen.x86_64
yum -y install unzip.x86_64
yum -y install wget.x86_64
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
systemctl enable php-fpm.service nginx.service mariadb.service
sed -i 's/^\[mysqld\]/\[mysqld\]\nmax_allowed_packet=200M/' /etc/my.cnf.d/server.cnf
systemctl restart nginx.service
systemctl restart php-fpm.service
systemctl start mariadb.service
cd /opt
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
HASH="$(wget -q -O - https://composer.github.io/installer.sig)"
php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
export COMPOSER_HOME="$HOME/.config/composer";
php composer-setup.php --install-dir=/usr/bin --filename=composer
/usr/bin/composer self-update 1.10.9 -n
pwgen -s 15 1 > /var/tmp/.mysql_password
db_drupaladmin_password=$(</var/tmp/.mysql_password)
mysql -u root -e "CREATE USER 'drupaladmin'@'localhost' IDENTIFIED BY '$db_drupaladmin_password';"
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'drupaladmin'@localhost IDENTIFIED BY '$db_drupaladmin_password';"
mysql -u root -e "flush privileges;"
mkdir -p /var/www/drupal/
mkdir -p /var/www/drupal/ghc-hosting
mkdir -p /var/www/drupal/ghc-hosting/health-apix2.2
cp -r $home/../../../portal /var/www/drupal/ghc-hosting/health-apix2.2/
cd /var/www/drupal/ghc-hosting/health-apix2.2/portal
php -d memory_limit=-1 /usr/bin/composer install -n

# for the dropdown in the swagger
curl -sL https://rpm.nodesource.com/setup_14.x | sudo bash -
yum install nodejs -y
node --version
npm --version
yum install gcc-c++ make -y

cd /var/www/drupal/ghc-hosting/health-apix2.2/portal/web/libraries/swagger-ui

sudo npm install
sudo npm install pm2 -g
sudo npm run-script build
pm2 start npm -- start

cd /var/www/drupal/ghc-hosting/health-apix2.2/portal/scripts/install

source ./ghc.config
touch backup.config
# Username of the database
echo "DB_USERNAME=$DB_USER_NAME" > ./backup.config
# This is the name of the database that you want to take the backup
echo "DB_NAME=$DB_NAME_AUTO" >> ./backup.config
# This is the path to the files directory. The files directory is the public files directory where the files are stored.
# ideally the files directory resides in under web/sites/default/files inside the project root path
echo "FILES_BACKUP_DIR_PATH=./../../web/sites/default/files" >> ./backup.config
# Please enter the absolute path for the login.cnf file
echo "LOGIN_CNF_PATH=./login.cnf" >> ./backup.config

source ./backup.config
# Drupal directory: 2 level up.
DRUPAL_ROOT_PATH=${PWD%/*/*}

mkdir /opt/backup

mkdir -p $DRUPAL_ROOT_PATH/web/sites/default/files
chmod -R 777 $DRUPAL_ROOT_PATH/web/sites/default/files

mysql -u "$DB_USER_NAME" -p"$DB_PASSWORD" -e "create database $DB_NAME_AUTO"

# Settings.php
DRUPAL_SETTINGS_PHP_FILE_NAME=settings.php

cp $DRUPAL_ROOT_PATH/web/sites/default/default.settings.php $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
chmod 777 $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME

# use shell script functions to generate this
SETTINGS_SAL_HASH="$(echo $(for((i=1;i<=74;i++)); do printf '%s' "${RANDOM:0:1}"; done) | tr '[0-1000]' '[A-Z]')"

#Update Databasebase Connectivity
echo ' $databases = [ ' >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "   'default' =>" >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "     [" >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "       'default' =>" >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "         [ " >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "           'database' => '$DB_NAME_AUTO'," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "            'username' => '$DB_USER_NAME'," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "            'password' => '$DB_PASSWORD'," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "            'host' => '$DB_HOST'," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "            'port' => '$DB_PORT'," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "            'driver' => 'mysql'," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "            'prefix' => ''," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "          ]," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "        ]," >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "  ];" >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME

echo "\$settings['hash_salt'] = \"${SETTINGS_SAL_HASH}\";" >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME
echo "\$config_directories = [ CONFIG_SYNC_DIRECTORY => dirname(DRUPAL_ROOT) . '/config'];" >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME

echo 'ini_set("memory_limit", "-1");' >> $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME

chmod 644 $DRUPAL_ROOT_PATH/web/sites/default/$DRUPAL_SETTINGS_PHP_FILE_NAME

# Drush cache clear & Set admin password

php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php si -y
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set system.site uuid e2d2886b-d7cd-4220-be78-5ea4027cf4ab -y
#getting an error during config import. So have added the below line
mysql -uroot -e "use $DB_NAME_AUTO; delete from shortcut";
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php cim -y
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php cr
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php user-password admin $ADMIN_PASSWORD
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set system.site name "$SITE_NAME" -y
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set system.site mail "$SITE_EMAIL" -y

if $UPDATE_SMTP_SETTINGS
then
  php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set smtp.settings smtp_host "$smtp_host" -y
  php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set smtp.settings smtp_port "$smtp_port" -y
  php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set smtp.settings smtp_protocol "$smtp_protocol" -y
  php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set smtp.settings smtp_username "$smtp_username" -y
  php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set smtp.settings smtp_password "$smtp_password" -y
  php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set smtp.settings smtp_from "$smtp_from" -y
fi
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php config-set system.file path.temporary "$DRUPAL_TMP_PATH" -y

rm -rf ./login.cnf
echo "[client]" >> ./login.cnf
echo "user"="$DB_USER_NAME" >> ./login.cnf
echo "password"="$DB_PASSWORD" >> ./login.cnf
php $DRUPAL_ROOT_PATH/vendor/drush/drush/drush.php cr
chown -Rc nginx.nginx /var/www/drupal/
cd /var/www/drupal/ghc-hosting/health-apix2.2/portal/
php vendor/drush/drush/drush.php cr
cp /var/www/drupal/ghc-hosting/health-apix2.2/portal/scripts/devportal/nginx.conf /etc/nginx/
cp /var/www/drupal/ghc-hosting/health-apix2.2/portal/scripts/devportal/php-fpm.conf /etc/nginx/conf.d/
cp /var/www/drupal/ghc-hosting/health-apix2.2/portal/scripts/devportal/www.conf /etc/php-fpm.d/
systemctl restart nginx.service
systemctl restart php-fpm.service
