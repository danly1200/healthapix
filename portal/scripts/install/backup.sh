# Please provide the absolute path for the backup.config file
cd /var/www/drupal/ghc-hosting/health-apix2.2/portal/scripts/install
source ./backup.config
DATE=`date +'%m-%d-%Y'`

rm /tmp/*.sql
mkdir -p /tmp/$DATE

mysqldump --defaults-extra-file=$LOGIN_CNF_PATH -u $DB_USERNAME $DB_NAME > /tmp/ghc.sql

cd $FILES_BACKUP_DIR_PATH && tar -zcf /tmp/$DATE/files.tar.gz *

cd /tmp && tar -zcf /tmp/$DATE/db.tar.gz ghc.sql

cp -r /tmp/$DATE /var/www/drupal/ghc-hosting/health-apix2.2/portal/scripts/install

rm -rf /tmp/$DATE


