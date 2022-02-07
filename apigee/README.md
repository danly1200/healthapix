# HealthAPIx Gateway

HealthAPIx Gateway runs on Apigee. Out of the box it integrates with the Google Cloud Healthcare FHIR API. Installation and configuration of the HealthAPIx Gateway is described in the 5 steps below.

## Install Process

[Step 1: Prerequisites](#Step-1:-Prerequisites)  
[Step 2: Install Dependencies](#Step-2:-Install-Depedencies)  
[Step 3: Run Deployment Scripts](#Step-3:-Run-Deployment-Scripts)  
[Step 4: Manual Configuration](#Step-4:-Manual-Configuration)  
[Step 5: Verify Installation](#Step-5:-Verify-Installation)  

[Addtional Reference](#Additional-Reference)  

---

### Step 1: Prerequisites

#### A) Apigee details

HealthAPIx requires an Apigee org and environments. Information about your Apigee instance listed below is required for installation of the HealthAPIx gateway:

1. Apigee organization - (referred as `apigee-org`)
1. Apigee environment - (referred as `apigee-env`)
1. Custom role - (referred as `app-mgmt-user`) - A custom apigee user role needs to be created who can list Products and create, update, edit Apps. The custom role is required for App approval workflow. See permissions below.
1. Custom Apigee user - (referred as `App Manager`)
1. Custom Apigee user email - (referred as `app-manager-email`)
1. Custom Apigee user password - (referred as `app-manager-password)`
1. Organization Admin credentials to deploy and configure HealthAPIx gateway components in Apigee - (referred as `apigee-username` and `apigee-password` in STEP 3 below)
1. Product owner email - (referred as `product-owner-email`) - required for App approval workflow

#### B) Add custom role: `app-mgmt-user` with limited permissions in Apigee

1. Create a custom role: `app-mgmt-user` in Apigee with permissions as shown in the screenshot below:

![Image](./src/doc-images/custom_role_apigee.png)

#### C) GCP Setup

HealthAPIx gateway requires [OAuth 2.0](https://developers.google.com/identity/protocols/oauth2) credentials and [Cloud Healthcare API](https://cloud.google.com/healthcare) in GCP. Obtain OAuth 2.0 credentials using [Google Cloud console](https://console.cloud.google.com/apis/credentials). If needed create Healthcare Dataset and FHIR Data stores in GCP using [Google Cloud console](https://console.cloud.google.com/healthcare?_ga=2.167748734.1344093353.1616821001-1921915808.1610847126). You need the following information about your GCP environment.

1. GCP Project - This either exists or you can [create a new GCP Project.](https://cloud.google.com/resource-manager/docs/creating-managing-projects#creating_a_project)
2. Healthcare Dataset - This either exists or you can [create a new Healthcare Dataset.](https://cloud.google.com/healthcare/docs/how-tos/datasets)
3. Dataset location - Region where the Healthcare Dataset resides.
4. FHIR store name - This either exists or you can [create a new FHIR Datastore.](https://cloud.google.com/healthcare/docs/how-tos/fhir)
5. Google Cloud Health Care API hostname - `healthcare.googleapis.com`
6. Google Cloud Health Care API Version - (`v1` = GA or `v1beta1` = Preview)
7. Service account key - Apigee will use a [service account](https://cloud.google.com/iam/docs/understanding-service-accounts) for authentication to the Cloud Healthcare API. [Create a new Service Account and Key](https://cloud.google.com/iam/docs/creating-managing-service-accounts).
8. OAuth 2.0 Client ID - This is required to configure Google as an Identity Provider (IDP) for OAuth2.0 authorization code grant type flow and SMART Authorization flow. [Create OAuth 2.0 credentials](https://developers.google.com/identity/protocols/oauth2/web-server) using [Google Cloud console](https://console.cloud.google.com/apis/credentials).
9. OAuth 2.0 authorized JavaScript origins - This is required for CORS to render Consent form in SMART Authorization flow. Value must be set to:

    `https://<apigee-org>-<apigee-env>.apigee.net`

10. OAuth 2.0 Authorized redirect URIs - This is required for the OAuth2.0 authorization code grant type flow and SMART Authorization flow. The redirect URI is set to the `implicit` endpoint of an Apigee proxy: `identity-consent-app`.

      `https://<apigee-org>-<apigee-env>.apigee.net/openid/implicit`

#### D) A VM to use for deployment

A linux VM is required to install HealthAPIx. It can be removed after installation is done.  A 2 vCPU, 8GB RAM, 20GB storage, Debian OS is sufficient.

### Step 2: Install Depedencies

#### A) Open an ssh connection to the VM and clone the code repository

Connect to your linux workstation using `ssh`.

```bash
# Install Git if needed
git --version

sudo apt-get -y install git

cd ~

# Clone healthapix git repository to your local machine. You may need manually generated credentials. You may generate and store your Git credentials using Google Cloud Source Repositories.

git clone https://source.developers.google.com/p/hpdi-artifacts/r/healthapix

# Change current directory to git repo home
cd ~/healthapix
```

#### B) Run install-dependencies.sh

You need Node version 8.12.0 and gulp CLI version 2.2.0 to setup HealthAPIx. This script installs tools required for the HealthAPIx setup.

```bash
#You might need root access to install/update tools. You can get an interactive root shell using sudo.
sudo -i

# Change current directory to git repo home
cd <git-repo-home>

GIT_ROOT=$PWD

# Install/Update tools needed for deployment
cd $GIT_ROOT/apigee/scripts

chmod +x ./install-dependencies.sh

./install-dependencies.sh

#Source your bashrc file to make sure all installed tools are correctly loaded
cd

. ./.bashrc

# Make sure you have the node verison is v10.24.0 
node --version
```

### Step 3: Run Deployment Scripts

### A) Configure env.sh

The env.sh file holds configuration values to be used by deploy.sh script later on. You need configuration data listed in the prerequisites section to update the env.sh file.

### B) Deploy HealthAPIx proxies and other configurations using gulp

Running gulp will install the identity proxies, shared flows, Caches, KVMs, Target Server and other configuration.

`Note: If you are using an environment that is not named 'dev', 'test' or 'prod' add your environment name to the list in config.yml.orig in configurations section around line 30. Otherwise it won't deploy correctly. Same needs to be entered as <apigee-env>`

```bash
cd $GIT_ROOT/apigee/

npm install

# Run gulp to create apigee configurations: target server, shard flows, developers, caches, KVMs, and proxies.
gulp start --env <apigee-env>

#For example - gulp start --env test
```

When prompted, enter the following values:

1. Enter the Client Id to use Google Sign-in: (use the OAuth 2.0 Client ID from previous step A)

1. Enter the redirect url registered with Google Sign-in: (use the OAuth 2.0 Authorized redirect URIs from OAuth 2.0 client configuration details) -  
`https://<apigee-org>-<apigee-env>.apigee.net/openid/implicit`

1. Apigee Org name: `<apigee-org>`
1. Apigee Username: `<apigee-username>`
1. Apigee Password: `<apigee-password>`
1. Target server host URL for CHC FHIR Store:  `healthcare.googleapis.com`
1. Target server port for CHC Fhir Store:  `443`
1. Target server basepath for CHC FHIR Store: `/`

If you are getting a 401 error, then you likely entered the wrong credentials or org name.  If this is the first time you are running this script, you will see delete errors in the beginning, since there is nothing to delete.  You might get some errors related to deployment.  This is common, see next step.

### C) Create FHIR Products, Apps and update KVMs

Running this script creates FHIR products, default apps, and updates KVMs. It also deploys 2 shared flows which were created but not deployed in previous step.

```bash
cd $GIT_ROOT/apigee/
chmod +x deploy.sh
./deploy.sh <apigee-username> <apigee-password>
```

### D) Verify Installation

[Login to Apigee](https://login.apigee.com/login), select your `apigee-org` and confirm that the items listed below were deployed to to your `apigee-env`. In some cases, the shared flows or proxies may not have been deployed due to a cache or KVM not created yet.  If this is the case, deploy the proxy or shared flows manually.  All proxies and shared flows listed below should be deployed.

Proxies - 10

1. identity-oauthv2-api (will be deployed as part of this step)
1. identity-consent-app (will be deployed as part of this step)
1. identity-usermgmt-api (will be deployed as part of this step)
1. identity-consentmgmt-api (will be deployed as part of this step)
1. identity-jwks (will be deployed as part of this step)
1. identity-appmgmt-api (will be deployed as part of this step)
1. sandbox_r4 (will be deployed as part of this step)
1. sandbox_stu3 (will be deployed as part of this step)
1. sandbox_dstu2 (will be deployed as part of this step)
1. fhirApis (will be deployed as part of this step)

Shared Flows - 11

1. authorization
1. google-token
1. proxy-faults
1. target-faults
1. response-headers
1. rewrite-links
1. smart-launch-security
1. spike-arrest
1. supported-resources
1. threat-protection
1. unsupported-operation

KVMs - 3

1. HPX_Config
1. HPX_Secrets
1. HPX_FHIRServerServiceAccountCredentials

Caches - 6

1. consent-session-cache - used by identity-consentmgmt-api
1. nonce-cache - used by identity-oauthv2-api
1. fhir-resources-cache - used by supported-resources shared flow
1. session-cookie-cache - used by identity-consent-app
1. backend-service-jwks-cache - used by backend services access token flow in identity-oauthv2-api
1. request-hash-links used by identity-appmgmt-api

Target Server - 2

1. ghcapiserver - points to healthcare.googleapis.com
1. ApigeeMgmtServer - points to api.enterprise.apigee.com

API Products - 4

1. Identity_Mgmt_Product - used by DevPortalAppCreator
1. sandbox_r4 - used by sandbox_r4 proxy
1. sandbox_stu3 - used by sandbox_stu3 proxy
1. sandbox_dstu2 - used by sandbox_dstu2 proxy

Developers - 1

1. App Manager - Created using app_manager_email entered in env.sh

Developer Apps - 4

1. DevPortalAppCreator - client app used by Developer portal for App Approval workflow
1. r4App - used as default r4 app
1. stu3App - used as default stu3 app
1. dstu2App - used as default dstu2 app

### Step 4: Manual Configuration

1. Make sure custom apigee role is present - "app-mgmt-role"
1. Make sure App Manager user has been created with this "app-mgmt-role"
1. Update KVM - HPX_Secrets - For entry "username" use "app-manager-email" as stated in STEP 1-A above
1. Update KVM - HPX_Secrets - For entry "password" use "app-manager-password" as stated in STEP 1-A above
1. Update KVM - HPX_FHIRServerServiceAccountCredentials - Copy the content of "Service account key file" mentioned above and use it as the value for a key whose name matches the apigee product name that is configured to connect to the Google cloud healthcare FHIR Store.
1. Assign following roles for the Service Account in GCP:
      * FHIR Store Viewer
      * FHIR Resource Editor

### Step 5: Verify Installation

 Use the following tests to verify the installation of HealthAPIx gateway

#### A) Get OAuth Token using Client Credentials

Login to the Apigee UI and open the user app that was newly created.  It should have a naming convention similar to r4App.  Copy the client_id and client_secret.  For OAuth client credentials, you'll need a [base64](https://www.base64encode.org/) string of client_id:client_secret.

```bash
curl -X POST \
  -H "Authorization: Basic base64{client_id:client_secret}" \
  -d '' \
  "https://<apigee-org>-<apigee-env>.apigee.net/oauth/v2/accesstoken?grant_type=client_credentials&scope=system/Patient.read"
```

Example:

```bash
curl -X POST \
  -H "Authorization: Basic aFcwQVVNN0UzMkFlWUtMb1pHZk0ybUdMOXdBUThkalU6eGFVNGRrcDJQcHRyUE1qZw==" \
  -d '' \
  "https://gcp-hcls-test.apigee.net/oauth/v2/accesstoken?grant_type=client_credentials&scope=system/Patient.read"
```

#### B) Import test data and make GET /Patient call

```bash
curl -X POST \
   --data "{
     'contentStructure': 'RESOURCE',
     'gcsSource': {
       'uri': 'gs://interop-test-data/fhir_r4/ndjson/*.ndjson'
     }
   }" \
   -H "Content-Type: application/json; charset=utf-8" \
   -H "Authorization: Bearer $(gcloud auth print-access-token)" \
 "https://healthcare.googleapis.com/v1/projects/<project>/locations/<location>/datasets/<dataset>/fhirStores/<fhir-store>:import"
```

```bash
curl -X GET \
  -H "Authorization: Bearer $ACCESS_TOKEN" \
  "https://<apigee-org>-<apigee-env>.apigee.net/v1/r4/sandbox_r4/Patient/"
```

Example:

```bash
curl -X GET \
  -H "Authorization: Bearer mhbO5TBgZ41wsdGDc1cqtIels53A" \
  "https://gcp-hcls-test.apigee.net/v1/r4/sandbox_r4/Patient/"
```

---
***At this point, all the HealthAPIx Gateway components in Apigee, sandbox FHIR proixes, and sandbox FHIR stores are ready for use.  You may proceed to the [Developer Portal install instructions](../portal).***

---

## Additional Reference

This section describes the components of the HealthAPIx solution and what they are used for

### A)  Create Consent Screens in GCP IAM and get Client Id

Create OAuth ClientId for OAuth Login. This is used for the identity provider application for the OIDC OAuth B2C example. You’ll need a client_id and redirect URL. Follow the instructions here: [https://developers.google.com/identity/protocols/oauth2/web-server#creatingcred](https://developers.google.com/identity/protocols/oauth2/web-server#creatingcred)

The redirect url can be `https://<apigee-org>-<apigee-env>.apigee.net`

Go to this link to create credentials:  (choose OAuth client ID)
[https://console.cloud.google.com/apis/credentials](https://console.cloud.google.com/apis/credentials)

![Image](https://lh5.googleusercontent.com/z72FmLTYMea2mlvRzXaZco2czEidO8p6GjGA8AAFAaep3po6BPkXoeRN_BB1tUtDmv3T5yq0ppbqyNlQ8BEuDH_n_T1uBX67BImP7VtUwk31j40j6X8_BRwzlUVdwxIDc-7YfJ84Hg)

Enable OAuth Consent Screen (If you haven’t done this already, follow these steps to create an application)

*Googlers: Choose “Internal” for the B2C signin with Google account only. Choose “External” for all Google and GMail accounts.*

![Image](https://lh6.googleusercontent.com/Ip4hrA9zv7Wv0VganlWCs16NLhIf9n2a5tW39UnLrNSHy6tZ2Mxp7EQroBCuQ44X1cdsc-14PaQ-TLkNL7F_YUD5RvcWxAmeSXYrEvA7zwGRtB29JIOfiRKLjENkS_H75PQb4YxObQ)

On the “OAuth consent screen”, fill in:

* Application Name (HealthAPIx Dev Portal)
* Authorized domains
* Add “apigee.net”

Click Save.

Go back to the Credentials page

[https://console.cloud.google.com/apis/credentials](https://console.cloud.google.com/apis/credentials)

Choose “Web Application”, enter:

* “Name”: HealthAPIx Developer Portal Web App
* Authorized Javascript Origins:  
`https://<apigee-org>-<apigee-env>.apigee.net`
* Authorized redirect urls:  
`https://<apigee-org>-<apigee-env>.apigee.net/openid/implicit`

Click “Create”

![Image](https://lh6.googleusercontent.com/uldfws-VvAU2fSWiRjUrP0EbPAnikk0BYCxwNpg2HXH66AuKtY2OaSc9Cebly60NKlSwnmA4NyaDd9OgRFMfuFteWgKFgd4VqwtdBTUR3jS2n_xH8mWWoCk-H1-XaSDjnw_uY19vag)

Copy “client Id” for use in the install script.

![Image](https://lh4.googleusercontent.com/7WLPOZTGakYHmg9Y-NtdOda5mlt3QlQs_k_woKZqJCMKyJdU6FcWzFTfDVS1441Hcb2btDZ34m6BVGjJ7i3EHbnp2zD4e2UTjL1AG1_9-C-axLwGCxBQRVF2mjGa12MjrsnxW71eMA)

### B) Apigee Edge Components

### API Proxies

Apigee FHIR Gateway consists of three types of proxies described below:

a) FHIR Proxies (templates)

Templates for API Proxies which are compatible with FHIR (Fast Healthcare Interoperability Resources) DSTU2, STU3 and R4 standards. These proxies connect to Google FHIR store implementation for DSTU2, STU3 and R4 FHIR standards. However, these samples are first converted into a deployable proxy based on the provisioning input and then deployed to the Apigee instance.  

b) Identity Proxies

This proxy provides Oauth 2.0 based authorization and authentication services including Backend Services access token flow.

c) User and Consent management Proxies

These proxies provide user and consent management apis and integrate with Identity proxies. These proxies use FHIR Store to check for the User based on the identifier provided. Consent is also being stored in the FHIR store and validation is being done against FHIR store.

#### Short description of HealthAPIx proxies

* `fhirApis` - This proxies acts as a catch-all proxy for the Api calls from API docs only. Internally, it routes the call to the appropriate FHIR proxy based on the client-id in the incoming API call.

* `sandbox_r4` - Sample R4 Fhir proxy connecting to a R4 Fhir store.

* `sandbox_stu3` - Sample STU3 Fhir proxy connecting to a STU3 Fhir store.

* `sandbox_dstu2` - Sample DSTU2 Fhir proxy connecting to a DSTU2 Fhir store.

* `identity-oauthv2-api` - OAuth2 Authorization proxy using Apigee out of the box features.  

* `identity-consent-app` - Proxy for Login redirection and consent interaction. Consent form is displayed in case of a Patient and RelaedPerson user. Consent form in not displayed to the Practitioner users.

* `identity-usermgmt-api` - Proxy which takes a gmail/google email as input and finds out if the email belongs to a Patient, Practitioner or a RelatedPerson in the FHIR Store.

* `identity-consentmgmt-api` - Proxy to validate, create and updated Consent resource when a Patient logs in.

* `identity-jwks` - Creates RSA Key-pair and JWKS json

* `identity-appmgmt-api` - handles approve/reject link in app approval workflow

#### Short description of shared-flows

* `authorization` - shared-flow for quota management, scope and access-token validation

* `google-token` - shared-flow for creating and embedding a google access token. Takes client_id as input and queries product to look at attributes to find out fhirstore details

* `proxy-faults` - Proxy fault management to be used in Fhir proxies in Proxy endpoints.

* `target-faults` - Target fault management to be used in Fhir proxies in Target endpoints.

* `response-headers` - Shared-flow to add CORS response headers on top of response returned from Google fhir store.

* `rewrite-links` - Shared-flow that re-writes links in Fhir store response and replaces the FQDN with that of the Fhir proxy.

* `smart-launch-security` - This shared flow add 'security' block to the capability statement from the Fhir store response and adds 'authorize' and 'token' urls in the security element. This is done to support SMART launch framwork requirements.

* `spike-arrest` - Shared flow for throttling the requests.

* `supported-resources` - Shared flow to validate whether the requested resource is a valid FHIR resource. Capability statement of the Fhir store is accessed and cached to arrive at the list of supported resource by a particular Fhir store.

* `threat-protection` - Shared flow to apply Threat protection policies to the flow within a FHIR Proxy.

* `unsupported-operation` - When incoming request to a FHIR proxy matches with none of the flows in the Proxy endpoint then this shared flow is invoked to return error response.

#### KVM

Key value maps are used by Apigee Edge proxies to get configuration information for some policies to work. Following KVMs will be created and used as part of this installation

* `HPX_Config` - Required by identity-oauthv2-api

* `HPX_Secrets` - Required by identity-oauthv2-api - Stores the private key for the JWT and username and password of App Manager Custom Apigee User

* `HPX_FHIRServerServiceAccountCredentials` - Required by multiple proxies. Holds service account keys.

#### Caches

Caches are being used by some Proxies to store information for cross proxy communication as well as session storage. Following caches are being used in this installation

* `consent-session-cache` - used by identity-consentmgmt-api

* `nonce-cache` - used by identity-oauthv2-api

* `fhir-resources-cache` - used by supported-resources shared flow

* `session-cookie-cache` - used by identity-consent-app

* `backend-service-jwks-cache` used by backend services access token flow in identity-oauthv2-api

* `request-hash-links` used by identity-appmgmt-api

#### Target Server

Target servers are being used to parameterize the backend url that are being used by FHIR proxies. Following target servers will be created and used in the FHIR proxies

* `ghcapiserver` - points to healthcare.googleapis.com

* `ApigeeMgmtServer` - points to api.enterprise.apigee.com

#### API Products

Initial deployment creates following products which can be used to kick-start exploration and serve as templates to create more customized products. API products related to FHIR proxies are created by provisioning scripts when the FHIR Stores and FHIR proxies are being created. For more information see Provisioning script documentation.

* `identityproduct` - used by identity-consent-app api proxy
* `Identity_Mgmt_Product` - used by DevPortalAppCreator
* `sandbox_r4` - used by sandbox_r4 proxy
* `sandbox_stu3` - used by sandbox_stu3 proxy
* `sandbox_dstu2` - used by sandbox_dstu2 proxy

### Developers

Default developers are created to support App creation. Following is the list of developers which are created as part of initial deployment

* `identity user` - user@identity.com - used to create apps for Identity, User and consent management proxies
* `test user` - testuser@apigee.com
* `App Manager` - Created using App Manager Email entered at the prompt

App for FHIR proxies are created using the user credentials which is provided at the time of running provisioning scripts.

### Developer Apps

Default apps are created to support quickly test and explore HealthCare FHIR APIs.

* `identityApp` - client app used by identity-consent-app api proxy
* `DevPortalAppCreator` - client app used by Developer portal for App Approval workflow
* `r4App` - used as default r4 app
* `stu3App` - used as default stu3 app
* `dstu2App` - used as default dstu2 app

### OAuth 2.0 Client ID and redirect Url

This is required to connect to Google Sign-in experience. Register an OAuth 2.0 Client for web application in your GCP Project:

<https://developers.google.com/identity/protocols/oauth2/web-server>

This is done by creating a OAuth 2.0 Client IDs under APIs and credentials section in GCP Project. This also requires configuring Consent screen with the domain details of Apigee Instance and Developer portal domain.

### Instructions on creating a Consent Screen for User Login

![Image](./src/doc-images/create_consent_screen_1.png)
![Image](./src/doc-images/create_consent_screen_2.png)
![Image](./src/doc-images/create_consent_screen_3.png)

As highlighted above, the domain of the Apigee instance and Developer portal needs to be mentioned here.

### Create an OAuth Client

![Image](./src/doc-images/create_oauth_client_1.png)
![Image](./src/doc-images/create_oauth_client_2.png)

### Use the OAuth Client Id

The clientId highlighted in yellow in previous picture is to be used when the Apigee Deployment script asks for “ Enter the Client Id to use Google Sign-in:” as mentioned in this document - Google Healthcare FHIR Store provisioning scripts.md under point 1 -  1. Deploy Identity Proxies and catch-all fhirApis Proxy
Urls in red and blue are required for the Google Sign in experience to work fine.
The one in red are corresponding to Apigee instance where Apigee proxies will be deployed and correspond to each Org-Env combination in your apigee instance. This is done once to make sure that same Client Id can be used across all the environments.

![Image](./src/doc-images/configure_oauth_client_1.png)

### Use the Redirect uri

The first Url in the Green highlighted box with endpoint as “/openid/implicit” is the Url where Google sign-in calls back to return the JWT token. This Url is to be used when the Apigee Deployment script asks for “Enter the redirect url registered with Google Sign-in:” as mentioned earlier in this document.
