var consentResponse = response.content.asJSON;
var consent_id = consentResponse.id;
response.content = JSON.stringify({consent_id: consent_id});