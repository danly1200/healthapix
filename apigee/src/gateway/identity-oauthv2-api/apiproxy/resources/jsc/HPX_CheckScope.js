var scopes = context.getVariable("oauthv2authcode.HPX_GO_GetAuthCodeAttributes.scope") || context.getVariable("oauthv2accesstoken.HPX_GO_AccessTokenInfo.accesstoken.scope");
var arrayScopes = [];
arrayScopes = scopes.split(" ");
var x;
var includeRefreshToken = false

for (x in arrayScopes) {
    if(arrayScopes[x] == 'offline_access'){
        includeRefreshToken = true
        break;
    }
}
context.setVariable("refreshToken", includeRefreshToken);