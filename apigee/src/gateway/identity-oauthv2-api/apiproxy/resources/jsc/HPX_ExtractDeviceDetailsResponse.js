 var fhir_server_version = context.getVariable("fhir_server_version")
 var DeviceDetailsResponse = JSON.parse(context.getVariable("HPX_DeviceDetailRequestCalloutResponse.content"));
 var DeviceDetailsResponseEntry = DeviceDetailsResponse.entry;
 var DeviceDetailsResponseEntryLength = DeviceDetailsResponseEntry.length;
 var ExtractConsentResponse = context.getVariable("ExtractConsentResponse");
 var ExtractConsentResponseLength = ExtractConsentResponse.length;
 var resp = {};
 
 if((fhir_server_version == 'dstu2') || (fhir_server_version == 'stu3')){
     for(i=0; i<DeviceDetailsResponseEntryLength; i++){
     var DeviceDetailsDeviceId = DeviceDetailsResponseEntry[i].resource.id;
     var appClientId = DeviceDetailsResponseEntry[i].resource.identifier[0].value;
     for(j=0; j < ExtractConsentResponseLength; j++){
         var ExtractConsentDeviceId = ExtractConsentResponse[j].deviceId
         if(DeviceDetailsDeviceId == ExtractConsentDeviceId){
             var revokeConsent = ExtractConsentResponse[0].fullURL;
             resp[appClientId] = {"revoke_consent":revokeConsent};
             break;
         }
     }
    }
 } else {
     for(i=0; i<DeviceDetailsResponseEntryLength; i++){
     var DeviceDetailsDeviceId = DeviceDetailsResponseEntry[i].resource.id;
     var appName = DeviceDetailsResponseEntry[i].resource.deviceName[0].name
     for(j=0; j < ExtractConsentResponseLength; j++){
         var ExtractConsentDeviceId = ExtractConsentResponse[j].deviceId
         if(DeviceDetailsDeviceId == ExtractConsentDeviceId){
             var revokeConsent = ExtractConsentResponse[0].fullURL;
             resp[appName] = {"revoke_consent":revokeConsent};
             break;
         }
     }
    }
 }
  
 
context.setVariable("ManagementResponse", JSON.stringify(resp));