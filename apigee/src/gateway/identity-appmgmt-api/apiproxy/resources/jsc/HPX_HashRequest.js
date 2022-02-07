var hashData = JSON.parse(context.getVariable("request.content"));
var hashRequest = JSON.stringify(hashData)

context.setVariable("hashRequest", hashRequest);

 
var sha512 = crypto.getSHA512();
    sha512.update(hashRequest);
    
var _hashed_token = sha512.digest();

context.setVariable("hashed_token", _hashed_token);
