var consentResponse = context.getVariable("getConsentInfoResponse.content");
consentResponse = JSON.parse(consentResponse);
var accessToken = consentResponse.except[0].class[1] ? consentResponse.except[0].class[1].code : null;
var refreshToken = consentResponse.except[0].class[2] ? consentResponse.except[0].class[2].code : null;
context.setVariable("access_token", accessToken);
context.setVariable("refresh_token", refreshToken);