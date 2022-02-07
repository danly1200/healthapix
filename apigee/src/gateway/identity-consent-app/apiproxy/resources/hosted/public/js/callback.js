var _hash = window.location.hash;
var id_token = _hash.match(/(?:id_token)\=([\S\s]*?)\&/)[1];
var state = _hash.match(/(?:state)\=([\S\s]*?)\&/)[1];
window.open("/openid/callback"+"?id_token="+id_token+"&state="+state,"_self");