try {
  var originalResponse = JSON.parse(context.getVariable('responseData.content'));
  if (originalResponse.entry) {
    context.setVariable("id", JSON.stringify(originalResponse.entry[0].resource.id));
  }
  else {
    context.setVariable("id", null);
  }
} catch (e) {
  context.setVariable('JS_Error', true);
  throw new Error("JS_Error");
}