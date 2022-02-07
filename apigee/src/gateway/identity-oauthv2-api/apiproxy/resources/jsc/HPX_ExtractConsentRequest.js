 var fhir_server_version = context.getVariable("fhir_server_version")
 var ConsentRequest = JSON.parse(context.getVariable("HPX_ConsentRequestCalloutResponse.content"))
 var entry = ConsentRequest.entry;
 var devId = "";
 var NullConsentResponse = '';
 
if(entry){
    var entryLength = entry.length;
    var arrayOfEachEntry = [];
    
    for(i=0; i<entryLength; i++){
        
        var eachEntry = entry[i];
        
        if(fhir_server_version == 'dstu2'){
            dstu2(eachEntry)
        } else if(fhir_server_version == 'stu3'){
            stu3(eachEntry);
        } else if(fhir_server_version == 'r4'){
            r4(eachEntry);
        }
    }
    
    context.setVariable("ExtractConsentResponse",arrayOfEachEntry);
    context.setVariable("deviceId",devId.substring(1, devId.length));
    context.setVariable("NullConsentResponse", false);
    
        } else {
            context.setVariable("NullConsentResponse", true);
            }

    
function r4(eachEntry) {
  var resourceId = eachEntry.resource.id;
  var reference = eachEntry.resource.provision.actor[0].reference.reference;
  var patt = /Device[/]/i;
  var Id = reference.replace(patt,'');
  devId += ','+Id;
  var eachConsentEntry = {
      "consentId": eachEntry.resource.id,
      "deviceId":Id,
      "consentStartDate":eachEntry.resource.provision.period.start,
      "consentEndDate":eachEntry.resource.provision.period.end,
      "consentScopes":eachEntry.resource.scope.coding[0].code,
      "fullURL":eachEntry.fullUrl
  };
  arrayOfEachEntry.push(eachConsentEntry);
}

function dstu2(eachEntry) {
  var resourceId = eachEntry.resource.id;
  var reference = eachEntry.resource.actor[0].entity.reference;
  var patt = /Device[/]/i;
  var Id = reference.replace(patt,'');
  devId += ','+Id;
  var eachConsentEntry = {
      "consentId": eachEntry.resource.id,
      "deviceId":Id,
    //   "consentStartDate":eachEntry.resource.provision.period.start,
    //   "consentEndDate":eachEntry.resource.provision.period.end,
    //   "consentScopes":eachEntry.resource.scope.coding[0].code,
      "fullURL":eachEntry.fullUrl
  };
  arrayOfEachEntry.push(eachConsentEntry);
}

function stu3(eachEntry) {
  var resourceId = eachEntry.resource.id;
  var reference = eachEntry.resource.actor[0].reference.reference;
  var patt = /Device[/]/i;
  var Id = reference.replace(patt,'');
  devId += ','+Id;
  var eachConsentEntry = {
      "consentId": eachEntry.resource.id,
      "deviceId":Id,
      "consentStartDate":eachEntry.resource.period.start,
      "consentEndDate":eachEntry.resource.period.end,
      "consentScopes":eachEntry.resource.except[0].class[0].code,
      "fullURL":eachEntry.fullUrl
  };
  arrayOfEachEntry.push(eachConsentEntry);
}
