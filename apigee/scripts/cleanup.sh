#!/bin/bash

#Remove KVMS
#Remove Caches
#Remove TargetServers
#Remove Apps
#Remove Products
#Remove Proxies
#Remove Developers

if [ "$#" -ne 4 ]; then
  echo "Need Apgiee org, env, username, and password."
  echo "Usage: cleanup.sh <apigee-org> <apigee-env> <apigee-username> <apigee-password>"
  echo "Example: cleanup.sh gcp-hcls test adm.healthapix@gmail.com pazzword"
  exit 0
fi

set -x

org=$1
env=$2
user=$3
pass=$4



apigeetool deleteKVMmap -u $user -p $pass -o $org -e $env --mapName HPX_Config
apigeetool deleteKVMmap -u $user -p $pass -o $org -e $env --mapName HPX_FHIRServerServiceAccountCredentials
apigeetool deleteKVMmap -u $user -p $pass -o $org -e $env --mapName HPX_Secrets

apigeetool deletecache -u $user -p $pass -o $org -e $env --cache consent-session-cache
apigeetool deletecache -u $user -p $pass -o $org -e $env --cache session-cookie-cache
apigeetool deletecache -u $user -p $pass -o $org -e $env --cache backend-services-jwks-cache
apigeetool deletecache -u $user -p $pass -o $org -e $env --cache nonce-cache
apigeetool deletecache -u $user -p $pass -o $org -e $env --cache fhir-resources-cache
apigeetool deletecache -u $user -p $pass -o $org -e $env --cache request-hash-links

apigeetool deleteTargetServer -u $user -p $pass -o $org -e $env --targetServerName ghcapiserver
apigeetool deleteTargetServer -u $user -p $pass -o $org -e $env --targetServerName ApigeeMgmtServer

apigeetool deleteApp -u $user -p $pass -o $org --name IdentityApp --email user@identity.com
apigeetool deleteApp -u $user -p $pass -o $org --name r4App --email appmngr1@gmail.com
apigeetool deleteApp -u $user -p $pass -o $org --name stu3App --email appmngr1@gmail.com
apigeetool deleteApp -u $user -p $pass -o $org --name dstu2App --email appmngr1@gmail.com
apigeetool deleteApp -u $user -p $pass -o $org --name DevPortalAppCreator --email appmngr1@gmail.com

apigeetool deleteProduct -u $user -p $pass -o $org --productName Identity_Mgmt_Product
apigeetool deleteProduct -u $user -p $pass -o $org --productName sandbox_r4
apigeetool deleteProduct -u $user -p $pass -o $org --productName sandbox_stu3
apigeetool deleteProduct -u $user -p $pass -o $org --productName sandbox_dstu2
apigeetool deleteProduct -u $user -p $pass -o $org --productName IdentityApp

apigeetool deleteDeveloper -u $user -p $pass -o $org --email testuser@apigee.com
apigeetool deleteDeveloper -u $user -p $pass -o $org --email user@identity.com
apigeetool deleteDeveloper -u $user -p $pass -o $org --email appmngr1@gmail.com

apigeetool undeploy  -u $user -p $pass -o $org -e $env -n identity-usermgmt-api -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n identity-consentmgmt-api -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n identity-consent-app -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n identity-oauthv2-api -D

apigeetool undeploy  -u $user -p $pass -o $org -e $env -n sandbox_r4 -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n sandbox_stu3 -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n sandbox_dstu2 -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n identity-jwks -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n identity-appmgmt-api -D
apigeetool undeploy  -u $user -p $pass -o $org -e $env -n fhirApis -D

apigeetool delete  -u $user -p $pass -o $org -n identity-usermgmt-api 
apigeetool delete  -u $user -p $pass -o $org -n identity-consentmgmt-api 
apigeetool delete  -u $user -p $pass -o $org -n identity-consent-app 
apigeetool delete  -u $user -p $pass -o $org -n identity-oauthv2-api 

apigeetool delete  -u $user -p $pass -o $org -n sandbox_r4
apigeetool delete  -u $user -p $pass -o $org -n sandbox_stu3
apigeetool delete  -u $user -p $pass -o $org -n sandbox_dstu2
apigeetool delete  -u $user -p $pass -o $org -n identity-jwks
apigeetool delete  -u $user -p $pass -o $org -n identity-appmgmt-api
apigeetool delete  -u $user -p $pass -o $org -n fhirApis


apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n authorization -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n google-token -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n proxy-faults -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n response-headers -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n rewrite-links-D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n smart-launch-security -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n spike-arrest -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n supported-resources -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n target-faults -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n threat-protection -D
apigeetool undeploySharedflow -u $user -p $pass -o $org -e $env -n unsupported-operation -D


apigeetool deleteSharedflow -u $user -p $pass -o $org -n authorization -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n google-token -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n proxy-faults -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n response-headers -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n rewrite-links-D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n smart-launch-security -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n spike-arrest -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n supported-resources -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n target-faults -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n threat-protection -D
apigeetool deleteSharedflow -u $user -p $pass -o $org -n unsupported-operation -D