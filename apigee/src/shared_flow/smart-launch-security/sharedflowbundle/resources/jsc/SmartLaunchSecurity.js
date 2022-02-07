try{
  response= JSON.parse(context.getVariable("response.content"))
  var host = context.getVariable("requestHost");
  var securityComponent = {
"cors": true,
"extension": [
  {
    "url": "http://fhir-registry.smarthealthit.org/StructureDefinition/oauth-uris",
    "extension": [
      {
        "url": "authorize",
        "valueUri": "https://"+host+"/oauth/v2/authorize"
      },
      {
        "url": "token",
        "valueUri": "https://"+host+"/oauth/v2/accesstoken"
      },
      {
        "url": "introspect",
        "valueUri": "https://"+host+"/oauth/v2/introspect"
      },
      {
        "url": "revoke",
        "valueUri": "https://"+host+"/oauth/v2/revoke"
      },
      {
          "url": "manage",
          "valueUri": "https://"+host+"/oauth/v2/manage"
      }
    ]
  }
],
"service": [
  {
    "coding": [
      {
        "system": "http://hl7.org/fhir/restful-security-service",
        "code": "SMART-on-FHIR",
        "display": "SMART-on-FHIR"
      }
    ],
    "text": "OAuth2 using SMART-on-FHIR profile (see http://docs.smarthealthit.org)"
  }
]
}
response.rest[0].security=securityComponent;
context.setVariable("response.content", JSON.stringify(response));

}catch(e){
  print("Error " + e);
  context.setVariable('JS_Error', true);
  throw new Error("JS_Error");
}