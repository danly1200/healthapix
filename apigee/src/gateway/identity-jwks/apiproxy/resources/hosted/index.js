var http = require('http');
const create = require("./provisionNewKeyPair");
create();
var jwksDetails ;

console.log('node.js application starting...');
console.log(process.env);

var svr = http.createServer(function(req, resp) {
if(req.url == '/create'){
  console.log(req.method, req.url);
  
  jwksDetails = create();
  setTimeout(()=>{
    console.log("Jwks details - ", JSON.stringify(jwksDetails));
    resp.setHeader("Content-Type", "application/json");
    console.log("response - ", JSON.stringify({jwksDetails}));
    resp.end(JSON.stringify({jwksDetails}));
  },150);


}
  
});

svr.listen(process.env.PORT || 3000, function() {
  console.log('Node HTTP server is listening');
});


