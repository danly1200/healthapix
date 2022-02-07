var apiProductJSON = context.getVariable("apiProductJSON");

var product = JSON.parse(apiProductJSON);

var attributeArr = product.ApiProduct.Attributes.Attribute;

var is_fhir_server = "";
var fhir_server_name = "";
var fhir_server_url = "";
var google_fhirstore_url = ""


for(var i = 0; i < attributeArr.length; i++) {

 if(attributeArr[i].Name == "is_fhir_server"){
     is_fhir_server = attributeArr[i].Value;
     context.setVariable("is_fhir_server",is_fhir_server);
 }
 
 if(attributeArr[i].Name == "fhir_server_url"){
    fhir_server_url = attributeArr[i].Value;
    context.setVariable("fhir_server_url",fhir_server_url);
 }
 
  if(attributeArr[i].Name == "google_fhirstore_url"){
     google_fhirstore_url = attributeArr[i].Value;
     context.setVariable("google_fhirstore_url",google_fhirstore_url);
 } 
 
}
if(fhir_server_url){
    var fhir_server_name = "";
    var splitUrl = fhir_server_url.split("/");
    fhir_server_name = splitUrl[ splitUrl.length - 1 ];
    fhir_version = splitUrl[ splitUrl.length - 2 ];
    context.setVariable("fhir_server_name",fhir_server_name);
    context.setVariable("fhir_version",fhir_version);
}