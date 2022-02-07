1. CreateApiDocs.sh is used to create API Docs for a particular Apigee Instance.
2. The Script takes apigee organization and Apigee Environment as arguments and does following
1.1 Creates a folder under apidocs with name as apigeeOrg-apigeeEnv
1.2 Creates DSTU2, STU3 and R4 folder under this newly created folder
1.3 Processed each file under apidocs/DSTU2, apidocs/STU3 and apidocs/R4, replaces org-env with the given input and creates same file under newly created folder.

For example - ./CreateApiDocs.sh healthapix-dev dev
1. Creates a folder healthapix-dev-dev under apidocs (apidocs/healthapix-dev-dev)
2. Creates 3 folders, DSTU2, STU3 and R4 under apidocs/healthapix-dev-dev
3. Processes all files under apidocs/DSTU2, apidocs/STU3, apidocs/R4 and replaces 'org-env' with 'healthapix-dev-dev'in each file and writes this file to apidocs/healthapix-dev-dev/DSTU2, apidocs/healthapix-dev-dev/STU3 and apidocs/healthapix-dev-dev/R4 respectively.
4. Finally, you have Api docs pointing to 'healthapix-dev-dev' apigee instance
5. These files now can be uploaded to intended Developer portal.