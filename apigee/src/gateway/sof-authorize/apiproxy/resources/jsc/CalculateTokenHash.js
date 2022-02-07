var authHeader = context.getVariable("req.Authorization");

var sha512 = crypto.getSHA512();
sha512.update(authHeader);
    
var _hashed_token = sha512.digest();
context.setVariable("_hashed_token",_hashed_token);