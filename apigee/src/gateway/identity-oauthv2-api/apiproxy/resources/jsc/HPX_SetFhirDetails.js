var apiProductJSON = context.getVariable("apiProductJSON");

var product = JSON.parse(apiProductJSON);

var attributeArr = product.ApiProduct.Attributes.Attribute;

var fhir_server_url = "";

for(var i = 0; i < attributeArr.length; i++) {
 if(attributeArr[i].Name == "fhir_server_url"){
    fhir_server_url = attributeArr[i].Value;
    context.setVariable("fhir_server_url",fhir_server_url);
 }
}

var fhir_version = "";

if(fhir_server_url){
    var splitUrl = fhir_server_url.split("/");
    fhir_server_name = splitUrl[ splitUrl.length - 1 ];
    context.setVariable("fhirServerName",fhir_server_name);
    fhir_version = splitUrl[ splitUrl.length - 2 ];
    context.setVariable("fhir_version",fhir_version.toLowerCase());
}
