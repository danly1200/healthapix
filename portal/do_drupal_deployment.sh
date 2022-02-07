#! /bin/bash

# This script works for any current branch. Please checkout manually & run this script to deploy on specific branch.
echo "Do you want do git pull for current branch: $(git rev-parse --abbrev-ref HEAD) ? (yes/no)"
read GIT_PULL

if [ "$GIT_PULL" == "yes" ]
then
  git pull origin $(git rev-parse --abbrev-ref HEAD)
fi


echo "Do you want run composer install? (yes/no)"
read COMPOSER_INSTALL

if [ "$COMPOSER_INSTALL" == "yes" ]
then
  composer install
fi


echo "Do you want import configs with drush cim? (yes/no)"
read DRUSH_CIM

if [ "$DRUSH_CIM" == "yes" ]
then
  php vendor/drush/drush/drush.php cim -y
fi

echo "Do you want to run 'drush updb'? (yes/no)"
read DRUSH_UPDB

if [ "$DRUSH_UPDB" == "yes" ]
then
  php vendor/drush/drush/drush.php updb -y
fi

# RUN cache rebuild at last step.
echo '----- All steps are done. ------'
echo '----- Clearing cache ------'
php vendor/drush/drush/drush.php cr

