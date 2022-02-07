var consentListResponse = response.content.asJSON;
if(consentListResponse.total > 0){
    var formattedConsents = [];
    consentListResponse.entry.forEach(function(consent){
        var formattedConsent = {};
        formattedConsent.id = consent.resource.id;
        formattedConsent.validFrom = consent.resource.period.start;
        formattedConsent.validTo = consent.resource.period.end;
        formattedConsent.status = consent.resource.status;
        print(consent.resource.id)
        print(consent.resource.except[0].class[0].code)
        formattedConsent.scopes = JSON.parse(consent.resource.except[0].class[0].code.replace(/ /g,''));
        formattedConsents.push(formattedConsent);
    });
    response.content = JSON.stringify(formattedConsents);
}else{
    response.content = JSON.stringify([]);
}