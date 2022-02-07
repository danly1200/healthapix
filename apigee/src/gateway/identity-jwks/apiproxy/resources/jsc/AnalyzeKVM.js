var pubKeyName = context.getVariable("publickeyRS384Name");
context.setVariable("existingPubKeyName",pubKeyName);
var privKeyName = context.getVariable("privatekeyRS384Name");
context.setVariable("existingPrivKeyName",privKeyName);
var jwksVal = context.getVariable("jwksJson");
var pubKey = context.getVariable("publickeyRS384");
var privKey = context.getVariable("private.privateKeyRS384");

var checkResponse= {};

if(!pubKeyName || pubKeyName === ""){
    print("public key name not found");    
    context.setVariable("installPublicKeyName",true);
    checkResponse.installPublicKeyName = true;
} else {
    print("public key config name - " + pubKeyName);
    checkResponse.installPublicKeyName = false;
}

if(!privKeyName || privKeyName === ""){
    print("private key name not found");        
    context.setVariable("installPrivateKeyName",true);
    checkResponse.installPrivateKeyName = true;
} else {
    print("private key config name - " + privKeyName);
    checkResponse.installPrivateKeyName = false;
}

if(!jwksVal || jwksVal === ""){
    print("Jwks value not found");    
    context.setVariable("installJwksJson",true);
    checkResponse.installJwksJson = true;
} else {
   print("Jwks json - " + jwksVal);
   checkResponse.installJwksJson = false;
}

if(!pubKey || pubKey === ""){
    print("No public key found for key name - " + pubKeyName);  
    context.setVariable("installPublicKey",true);
    checkResponse.installPublicKey = true;
} else {
    print("Public key - " + pubKey);
    checkResponse.installPublicKey = false;
}

if(!privKey || privKey === ""){
    print("No private key found for key name - " + privKeyName);        
    context.setVariable("installPrivateKey",true);
    checkResponse.installPrivateKey = true;
} else {
    print("Private key - " + privKey);
    checkResponse.installPrivateKey = false;
}

context.setVariable("jwksStatus",JSON.stringify(checkResponse));
var flowName = context.getVariable("current.flow.name");
context.setVariable("flowName",flowName);


