const crypto = require('crypto');
const jose = require('node-jose');

var kid = randomString();
var pubKey, privKey, keys, keyResult, responseObject;
var keystore = jose.JWK.createKeyStore();

const provisionKeyPair = function() {

    keys = [];
    responseObject = {};
    
    newKeyPair().then( ({publicKey, privateKey}) => {
        pubKey = publicKey;
        privKey = privateKey;
        }
     ).then(()=>{
        keystore.add(pubKey, 'pem', {kid, use:'sig'})
        .then( result => {
            keyResult = result;
        });    
     })
     .catch( (error) => {
        console.log("Error - ", error);
     }
     );
     
      
setTimeout(() => {
    keys.push(keyResult.toJSON());
    responseObject.KeyId = kid;
    responseObject.publicKey = pubKey;
    responseObject.privateKey = privKey;
    responseObject.jwks = keys;
},100);

    return responseObject;
}

module.exports = provisionKeyPair

 function randomString(L){
    L = L || 18;
    let s = '';
    do {s += Math.random().toString(36).substring(2, 15); } while (s.length < L);
    return s.substring(0,L);
  }
  
function newKeyPair() {
return new Promise( (resolve, reject) => {
    let keygenOptions = {
        modulusLength: 2048,
        publicKeyEncoding: { type: 'spki', format: 'pem' },
        privateKeyEncoding: { type: 'pkcs8', format: 'pem'}
        };
    crypto.generateKeyPair('rsa', keygenOptions,
                            function (e, publicKey, privateKey) {
                            if (e) { return reject(e); }
                            return resolve({publicKey, privateKey});
                            });
    });
}
