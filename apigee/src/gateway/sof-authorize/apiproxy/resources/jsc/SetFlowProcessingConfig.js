try {

var isJwtFlow = false;
var IS_CLIENT_REGISTRATION_ENABLED = false;

//Get all KVM Config

var SPIKEARREST_RATELIMIT = context.getVariable("SPIKEARREST_RATELIMIT");
var FHIR_ISSUER = context.getVariable("FHIR_ISSUER");
var SERVICE_ACCOUNT_NAME = context.getVariable("SERVICE_ACCOUNT_NAME");
var ALLOWED_PATH_PREFIX = context.getVariable("ALLOWED_PATH_PREFIX");
var DISABLE_AUDIT_LOG = context.getVariable("DISABLE_AUDIT_LOG");
var AUDIENCE = context.getVariable("AUDIENCE");
var FHIR_ISSUER_CLIENT_ID = context.getVariable("FHIR_ISSUER_CLIENT_ID");
var FHIR_ISSUER_CLIENT_SECRET = context.getVariable("FHIR_ISSUER_CLIENT_SECRET");
var REMOVE_SCOPES = context.getVariable("REMOVE_SCOPES");
var VERIFY_ACCESS_TOKEN = context.getVariable("VERIFY_ACCESS_TOKEN");
var MAX_TOKEN_CACHE_MINUTES = context.getVariable("MAX_TOKEN_CACHE_MINUTES");
var JWKS_CACHE_TIMEOUT_SECONDS = context.getVariable("JWKS_CACHE_TIMEOUT_SECONDS");
var CHECK_CLIENT_REGISTRATION_ENABLED = context.getVariable("IS_CLIENT_REGISTRATION_ENABLED");
var SOF_CLAIM_CONFIG = context.getVariable("SOF_CLAIM_CONFIG");
var MAX_HEADER_SIZE_BYTES = context.getVariable("MAX_HEADER_SIZE_BYTES");


var reqApiVersion = context.getVariable("urirequest.apiVersion");
var reqProject = context.getVariable("urirequest.project");
var reqLocation = context.getVariable("urirequest.location");
var reqDataset = context.getVariable("urirequest.dataset");
var reqFhirstore = context.getVariable("urirequest.fhirStore");

var base64HashIssuerClientAndSecret = "";

//var reqAuthorization = context.getVariable("request.header.Authorization");
var reqXClientId = context.getVariable("request.header.X-Client-Id");
var reqXClientSecret = context.getVariable("request.header.X-Client-Secret"); 

context.setVariable("logIpAddress",context.getVariable("client.ip"));
context.setVariable("logRequestPath",context.getVariable("request.path"));

//context.setVariable("req.Authorization", reqAuthorization);
context.setVariable("req.X-Client-Id", reqXClientId);
context.setVariable("req.X-Client-Secret", reqXClientSecret);

if(!VERIFY_ACCESS_TOKEN || VERIFY_ACCESS_TOKEN.trim() === ""){
    isJwtFlow = true;
    //base64HashIssuerClientAndSecret = encodeBase64(FHIR_ISSUER_CLIENT_ID+":"+FHIR_ISSUER_CLIENT_SECRET);
    //context.setVariable("base64HashIssuerClientAndSecret", base64HashIssuerClientAndSecret);
} else if(VERIFY_ACCESS_TOKEN.trim().toLowerCase() === "JWT"){
    isJwtFlow = true;
} else if(VERIFY_ACCESS_TOKEN.trim().toLowerCase() === "introspect"){
    isJwtFlow = false;
} else if(VERIFY_ACCESS_TOKEN.trim().toLowerCase() === "userinfo"){
    isJwtFlow = false;
} else {
    isJwtFlow = true;
}

if(CHECK_CLIENT_REGISTRATION_ENABLED && CHECK_CLIENT_REGISTRATION_ENABLED !== "" && CHECK_CLIENT_REGISTRATION_ENABLED === true){
    IS_CLIENT_REGISTRATION_ENABLED = true;
}

context.setVariable("isJwtFlow", isJwtFlow);
context.setVariable("IS_CLIENT_REGISTRATION_ENABLED", IS_CLIENT_REGISTRATION_ENABLED);

var claimsJson = JSON.parse(SOF_CLAIM_CONFIG);
for (var key in claimsJson) {
    if (claimsJson.hasOwnProperty(key)) {
        print(key + " -> " + claimsJson[key]);
    }
}
context.setVariable("CLAIM_PATIENT", claimsJson.CLAIM_PATIENT);
context.setVariable("CLAIM_SCOPE", claimsJson.CLAIM_SCOPE);
context.setVariable("CLAIM_FHIRUSER", claimsJson.CLAIM_FHIRUSER);
context.setVariable("CLAIM_EMAIL", claimsJson.CLAIM_EMAIL);
context.setVariable("CLAIM_SUBJECT", claimsJson.CLAIM_SUBJECT);
context.setVariable("CLAIM_TOKEN_ID", claimsJson.CLAIM_TOKEN_ID);
context.setVariable("CLAIM_PROFILE", claimsJson.CLAIM_PROFILE);

}catch(Error){
    print("Error " + Error);
    context.setVariable('JS_Error', true);  
    throw new Error("JS_Error");
}