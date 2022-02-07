var inputScopes = context.getVariable("scopes");
var consentWindowStartTime = new Date().toISOString();
var consentWindowEndTime = new Date();
consentWindowEndTime.setFullYear(consentWindowEndTime.getFullYear() + 1);
consentWindowEndTime = consentWindowEndTime.toISOString();
var formattedInputScopes = inputScopes.split(" ").join("\\\",\\\"");
context.setVariable("consent_window_start_time", consentWindowStartTime);
context.setVariable("consent_window_end_time", consentWindowEndTime);
context.setVariable("formatted_scopes", "\\\"" + formattedInputScopes + "\\\"");