function showTabContentContent(evt, nameTab,el) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontentprestaprice");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinksprestaprice");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(nameTab).style.display = "block";
    evt.currentTarget.className += " active";

    if(nameTab == "Contracts") {
        $("#tabContractInfo").click();
        $("#tabContractInfo").addClass("active");
        assignDataToContract(el);        
    }
}

function assignDataToContract(el) {

    $el = $(el);

    var contractId = $el.attr("data-contractId");
    var contractName = $el.text();

    $("#contractId").val(contractId);
    $("#contractName").val(contractName);
}

$(document).ready(function() {
      
    console.log("document ready");

    // Get the element with id="defaultOpen" and click on it
      document.getElementById("defaultOpen").click(); 
  });

function expandCollapseMenu(el) {

 var $el = $(el);
 $el.css("display","block");
 $(".tablinksprestaprice").removeClass("active");

 $el.addClass("active");
 var liExpand = $el.next('ul');

 liExpand.toggle();

}

function showTabContent(evt, tabName) {

    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    
    document.getElementById(tabName).style.display = "block";

    if(evt != null)
    {
        if(evt.currentTarget != null)
            evt.currentTarget.className += " active";
    }
}


 function updateContractInfo() {

        var priceMonitorId = $("#contractId").val();
        var salesPriceImportIn = $("#salesPrice").val();
        var isInsertSalesPrice = $("#salesPriceVariationSelect").val();

        var data = {
            'priceMonitorId': priceMonitorId,
            'salesPriceImportInId': salesPriceImportIn,
            'isInsertSalesPrice' : isInsertSalesPrice
        };

        $.ajax({
            type: "POST",
            url: "/updateContractInfo",
            data: data,
            success: function(data)
            {
                console.log("data");
                console.log(data);

                // var data = jQuery.parseJSON( data );
                // if(data != null)
                // {
                //    console.log("Uspesno sacuvano");
                // }
                // else
                // {
                //     alert("ERROR");
                // }
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }