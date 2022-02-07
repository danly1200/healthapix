var existingScopes = JSON.parse(context.getVariable("existing_scopes"));
var newScopes = JSON.parse(context.getVariable("new_scopes"));
combinedScopes = existingScopes.concat(newScopes);
var consentWindowEndTime = new Date();
consentWindowEndTime.setFullYear(consentWindowEndTime.getFullYear() + 1);
consentWindowEndTime = consentWindowEndTime.toISOString();
context.setVariable("combined_scopes", "\\\"" + combinedScopes.join("\\\",\\\"") + "\\\"");
context.setVariable("consent_window_end_time", consentWindowEndTime);