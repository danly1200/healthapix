var kid = context.getVariable("keypair.keyId");
var _kty = context.getVariable("keypair.kty");
var _kid = context.getVariable("keypair.kid");
var _use = context.getVariable("keypair.use");
var _n = context.getVariable("keypair.n");
var _e = context.getVariable("keypair.e");
var _res = {};
_res.keys = [];
var x = {};
x.kty = _kty;
x.kid = _kid;
x.use = _use;
x.n = _n;
x.e = _e;
_res.keys.push(x);
var strJson = JSON.stringify(_res);
context.setVariable("_jwks",strJson);

context.setVariable("pubKeyName",kid);
context.setVariable("privKeyName",kid);

if(!kid || kid ===""){
    context.setVariable("skipKvm",true);
} else {
    context.setVariable("skipKvm",false);
}