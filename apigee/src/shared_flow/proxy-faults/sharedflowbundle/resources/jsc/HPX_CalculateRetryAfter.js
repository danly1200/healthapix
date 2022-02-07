var expiryTime = context.getVariable("ratelimit.HPX_EnforceQuota.expiry.time");
var currentTime = context.getVariable("system.timestamp");
var remainingTime = expiryTime - currentTime;
remainingTime = Math.floor(remainingTime/=1000).toString();
context.setVariable("Retry-After",remainingTime);