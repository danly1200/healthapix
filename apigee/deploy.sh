#!/bin/bash
#set -x

scriptDir=`pwd`

getEnvSettings() {

    if [ ! -e ./env.sh ]
    then
        echo "Configuration file not found"
        exit 1
    fi

    apigee_user=$1
    apigee_password=$2

    source ./env.sh

    if [ ! -n "$gcp_cloud_healthcare_host" ] || [ ! -n "$gcp_cloud_healthcare_port" ]  || [ ! -n "$gcp_cloud_healthcare_path" ] || [ ! -n "$gcp_api_version" ] 
    then
        echo "Required GCP Fhir information is not configured."
        exit 1
    fi

    if [ ! -n "$oauth2_web_client_id" ] || [ ! -n "$oauth2_redirect_domain" ] || [ ! -n "$oauth2_redirect_url" ]
    then
        echo "Required Oauth2.0 client information is not configured."
        exit 1
    fi

    if [ ! -n "$apigee_org" ] || [ ! -n "$apigee_env" ] || [ ! -n "$app_manager_email" ] || [ ! -n "$product_owner_email" ]
    then
        echo "Required Apigee information is not configured."
        exit 1
    fi

    if [ ! -n "$sandbox_r4_fhirstore" ] || [ ! -n "$sandbox_r4_fhirproxyurl" ] || [ ! -n "$sandbox_stu3_fhirstore" ] || [ ! -n "$sandbox_stu3_fhirproxyurl" ] || 
    [ ! -n "$sandbox_dstu2_fhirstore" ] || [ ! -n "$sandbox_dstu2_fhirproxyurl" ]
    then
        echo "Required sandbox proxy information is not configured."
        exit 1
    fi

    if [ ! -n "$r4_scopes" ] || [ ! -n "$stu3_scopes" ] || [ ! -n "$dstu2_scopes" ]
    then
        echo "Required Product scopes information is not configured."
        exit 1
    fi

    if [ ! -n "$common_scopes" ] || [ ! -n "$wildcard_scopes" ]
    then
        echo "Required common or wildcard scopes information is not configured."
        exit 1
    fi

    if [ ! -n "$apigeeUrl" ]
    then
        echo "Required apigee enterprise url is not configured."
        exit 1
    fi

}

getScopes() {
    fhir_version=$1
    if [ "R4" == $fhir_version ] || [ "r4" == $fhir_version ]
    then 
        default_app_kvm_key_name="default.r4.fhir.app"
        scopes=$r4_scopes,$common_scopes,$wildcard_scopes
    elif [ "STU3" == $fhir_version ] || [ "stu3" == $fhir_version ]
    then 
        default_app_kvm_key_name="default.stu3.fhir.app"
        scopes=$stu3_scopes,$common_scopes,$wildcard_scopes
    elif [ "DSTU2" == $fhir_version ] || [ "dstu2" == $fhir_version ]
    then
        default_app_kvm_key_name="default.dstu2.fhir.app"
        scopes=$dstu2_scopes,$common_scopes,$wildcard_scopes
    fi
}

# Define your function here
createFhirProduct () {
    echo "Creating FHIR Product $1"
    productName=$1
    displayName=$1
    proxyName=$1
    fhir_server_name=$1
    approvalType="manual"
    product_owner_email=$2
    fhir_version=$3
    google_fhirstore_url=$4
    fhir_server_url=$5
    proxies=\"$proxyName\",\"identity-consentmgmt-api\",\"identity-oauthv2-api\",\"fhirApis\"
    
    is_fhir_server=true
    quota=10000
    quotaInterval=1
    quotaTimeUnit=month
    apiResources=/**
    environment=$apigee_env

    apigee_product_creation_url=$apigeeUrl/$apigee_org/apiproducts

    getScopes $fhir_version
    data="{\"name\":\"$productName\",\"displayName\":\"$productName\",\"approvalType\":\"$approvalType\",\"attributes\":[{\"name\": \"access\",\"value\": \"public\"},{\"name\":\"product_owner_email\",\"value\":\"$product_owner_email\"},{\"name\":\"fhir_version\",\"value\":\"$fhir_version\"},{\"name\":\"google_fhirstore_url\",\"value\":\"$google_fhirstore_url\"},{\"name\":\"fhir_server_url\",\"value\":\"$fhir_server_url\"},{\"name\":\"is_fhir_server\",\"value\":\"true\"}],\"description\":\"$productName\",\"apiResources\":[\"$apiResources\"],\"environments\":[\"$environment\"],\"proxies\":[ $proxies ],\"quota\":\"$quota\",\"quotaInterval\":\"$quotaInterval\",\"quotaTimeUnit\":\"$quotaTimeUnit\",\"scopes\":[ $scopes ]}" 

    curl -i \
    -H "Accept: application/json" \
    -H "Content-Type:application/json" \
    -u $apigee_user:$apigee_password \
    -X POST --data "$data" "$apigee_product_creation_url"

    if [ $? -ne 0 ]; then
        echo "Error creating product..."
        exit 1
    fi
}

createDefaultFhirApp () {
    echo "Creating Default FHIR App $1 $2"
    appName=$1
    fhir_Server_Product=$2
    fhir_Server_Name=$2
    user=$apigee_user
    pass=$apigee_password
    developer=$app_manager_email

    data="{\"name\":\"$appName\",\"apiProducts\":[\"$fhir_Server_Product\"],\"keyExpiresIn\":-1,\"attributes\":[{\"name\":\"DisplayName\",\"value\":\"$appName\"}],\"callbackUrl\":\"https:\/\/localhost:3000\/callback\"}"

    postUrl=$apigeeUrl/$apigee_org/developers/$developer/apps
    echo $postUrl

    default_app_consumer_key=`curl -s \
    -H "Accept: application/json" \
    -H "Content-Type:application/json" \
    -u $user:$pass \
    -X POST --data "$data" "$postUrl" | jq '.credentials[0].consumerKey' | sed -e 's/^"//' -e 's/"$//'`

    if [ $? -ne 0 ] || [ "null" == $default_app_consumer_key ] 
    then
        echo "Error creating sample app..."
        exit 1
    fi

    echo "Creating KVM Entry in HPX_Config for default fhir app..."
    apigeetool deleteKVMentry -u $user -p $pass -o $apigee_org -e $apigee_env --mapName HPX_Config --entryName "$default_app_kvm_key_name"
    apigeetool addEntryToKVM -u $user -p $pass -o $apigee_org -e $apigee_env --mapName HPX_Config --entryName "$default_app_kvm_key_name" --entryValue "$default_app_consumer_key"
    if [ $? -ne 0 ]; then
        echo "Error creating KVM entry for default fhir app..."
        exit 1
    fi
}

usage() {
  echo "Usage: $(basename $0) [apigee-user] [apigee-password]"
  exit 1
}

#get configuration from env.sh
if [ $# -lt 2 ]; then
    echo "Script cannot proceed without all the inputs..."
	usage
fi

echo "Reading configuration file"
getEnvSettings $1 $2

# Create App Manager Developer
echo "Creating Developer"
apigeetool createDeveloper --firstName App --lastName Manager --userName appmngr --email $app_manager_email --organization $apigee_org -u $apigee_user -p $apigee_password

# Create Sandbox_r4 product
echo "Creating sandbox_r4 product"
createFhirProduct sandbox_r4 $product_owner_email r4 $sandbox_r4_fhirstore $sandbox_r4_fhirproxyurl $apigee_org $apigee_env 
# Create default r4App
echo "Creatin default r4App "
createDefaultFhirApp r4App sandbox_r4 

# Create sandbox_stu3 product
echo "Creating sandbox_stu3 product"
createFhirProduct sandbox_stu3 $product_owner_email stu3 $sandbox_stu3_fhirstore $sandbox_stu3_fhirproxyurl $apigee_org $apigee_env 
# Create default stu3App
echo "Creating default stu3App "
createDefaultFhirApp stu3App sandbox_stu3

# Create sandbox_dstu2 product
echo "Creating sandbox_dstu2 product"
createFhirProduct sandbox_dstu2 $product_owner_email dstu2 $sandbox_dstu2_fhirstore $sandbox_dstu2_fhirproxyurl $apigee_org $apigee_env 
# Create default dstu2App
echo "Creating  default dstu2App "
createDefaultFhirApp dstu2App sandbox_dstu2

CreateProduct () {
    echo "Creating $1"
    productName=$1
    displayName=$1
    proxyName="identity-appmgmt-api"
    approvalType="auto"
    quota=100
    quotaInterval=1
    quotaTimeUnit=minute
    apiResources=/
    environment=$apigee_env
    description="App management product for Devportal"
    proxies=\"identity-appmgmt-api\"

    apigee_product_creation_url=$apigeeUrl/$apigee_org/apiproducts

    data="{\"name\":\"$productName\",\"displayName\":\"$productName\",\"approvalType\":\"$approvalType\",\"description\":\"$description\",\"attributes\":[{\"name\": \"access\",\"value\": \"private\"}],\"apiResources\":[\"$apiResources\"],\"environments\":[\"$environment\"],\"proxies\":[ $proxies ],\"quota\":\"$quota\",\"quotaInterval\":\"$quotaInterval\",\"quotaTimeUnit\":\"$quotaTimeUnit\"}" 

    curl -i \
    -H "Accept: application/json" \
    -H "Content-Type:application/json" \
    -u $apigee_user:$apigee_password \
    -X POST --data "$data" "$apigee_product_creation_url"

    if [ $? -ne 0 ]; then
        echo "Error creating app mgmt product..."
        exit 1
    fi
}

CreateApp () {
    echo "Creating Dev Portal App Manager App $1 $2"
    appName=$1
    product=$2
    user=$apigee_user
    pass=$apigee_password
    developer=$app_manager_email

    data="{\"name\":\"$appName\",\"apiProducts\":[\"$product\"],\"keyExpiresIn\":-1,\"attributes\":[{\"name\":\"DisplayName\",\"value\":\"$appName\"}]}"

    postUrl=$apigeeUrl/$apigee_org/developers/$developer/apps
    echo $postUrl

    devportal_consumer_key=`curl -s \
    -H "Accept: application/json" \
    -H "Content-Type:application/json" \
    -u $user:$pass \
    -X POST --data "$data" "$postUrl" | jq '.credentials[0].consumerKey' | sed -e 's/^"//' -e 's/"$//'`

    echo "Dev Portal App Mgmt client key - $devportal_consumer_key"

    if [ $? -ne 0 ] || [ "null" == $devportal_consumer_key ] 
    then
        echo "Error creating DevPortalAppCreator app..."
        exit 1
    fi
}

#Create Identity Mgmt Product
echo "Creating Identity_Mgmt_Product"
CreateProduct Identity_Mgmt_Product

#Create Dev Portal App Mgmt App
echo "Creating DevPortalAppCreator App"
CreateApp DevPortalAppCreator Identity_Mgmt_Product

#Deploy supported-resources shared flow
echo "Deploying supported-resources shared-flow"
cd $scriptDir/src/shared_flow/supported-resources
apigeetool deploySharedflow  -u $apigee_user -p $apigee_password -o $apigee_org  -e $apigee_env -n supported-resources

#Deploy google-token shared flow
echo "Deploying google-token shared-flow"
cd $scriptDir/src/shared_flow/google-token
apigeetool deploySharedflow  -u $apigee_user -p $apigee_password -o $apigee_org  -e $apigee_env -n google-token

#Invoke Jwks proxy to Create Key pair and Jwks
echo "Invoking JWKS Proxy - "
curl -X GET https://$apigee_org-$apigee_env.apigee.net/identity-jwks/create
echo "JWKS Json output is expected. In case, no json output is seen. Execute the below mentioned curl command again."
echo "curl -X GET https://$apigee_org-$apigee_env.apigee.net/identity-jwks/create"