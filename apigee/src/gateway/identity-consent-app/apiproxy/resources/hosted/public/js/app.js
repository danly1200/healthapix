$(document).ready(function() {
    $('.revoke-consent').click(function(e){
        var consentid = $(this).data("consent-id");
        var application_name = $(this).parents(".panel-collapse").siblings(".panel-heading").find("a").text();
        var permissions = $(this).prev()[0].outerHTML;
        // var modalTitle = "Confirm revoking of consent";
        var modalTitle = $(this).data("consentstitle");
        var consentsPer = $(this).data("consentsper");
        var consentsText = $(this).data("consentstext");
        var modalContent = "<div class='confirm-revoke'>";
            modalContent += "<span class='consent-id' style='display:none' data-consent-id='"+consentid+"'></span>";
            modalContent += "<h4>"+consentsPer+" "+application_name+"</h4>";
            modalContent += "<p>"+consentsText+"</p>";
            modalContent += permissions;
            modalContent += "</div>";
            $("#grassModal .modal-title").html(modalTitle);
            $("#grassModal .modal-body").html(modalContent);
            $("#grassModal").modal();
            $("#grassModal").show();



        
    });
    
    $("#grassModal").on('hidden', function(){
        $("#grassModal .confirm-revoke").remove();
    });
    
    $("#confirm-revoke-consent").click(function(e){
        
        var consentid = $(this).parent().prev().find('.consent-id').data('consent-id');
        console.log(consentid);
        
        $.ajax({
            url:"/openid/apps/revoke",
            type: 'GET',
            data:{consentid:consentid}
        }).done(function(){
            $("#accordian").find("#"+consentid).remove();
            $('#grassModal').modal('hide');
        });
        
    });
});