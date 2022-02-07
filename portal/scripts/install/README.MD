This file tells about the requirements and the process of taking the backup in GCP

## Prerequisites
1. Please make sure that there is a login.cnf file in the current install folder
which has the contetnts in the following format

[client]
user = username
password = password

2. Please copy the example.backup.config file and rename it to backup.config file.
Please provide correct values for varialbes that are mentioned in example.backup.config file.

## How to take manual backup

To run the manual backup, navigate to the install folder using the "cd" command.
Run the following command
> bash ./backup.sh

The above command will take the database and the files folder backup and store it in the folder
named with the current date in the bucket that is mentioned in the backup.config file

## How to set the cron to take the backup

Please enter the following command to add/edit the cron in the application

> crontab -e

It will open an editor. Please enter the following command to set the cron in the editor
Please enter the absolute path of the backup.sh and the cleanup.sh file

>0 8 * * * nohup sh /root/backup.sh > /root/cron.log 2>&1
>0 9 * * * nohup sh /root/cleanup-bucket.sh > /root/cron.log 2>&1

The above command in the crontab will help us run the backup script at 8AM UTC everyday
and run the clean up bucket which runs the script at 9am UTC everyday.

We can use the below command to list all the crontabs in the kernel

> crontab -l


