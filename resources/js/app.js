
toastr.options = {
  "closeButton": true,
  "debug": false,
  "newestOnTop": false,
  "progressBar": true,
  "positionClass": "toast-top-right",
  "preventDuplicates": false,
  "onclick": null,
  "showDuration": "300",
  "hideDuration": "1000",
  "timeOut": "5000",
  "extendedTimeOut": "1000",
  "showEasing": "swing",
  "hideEasing": "linear",
  "showMethod": "fadeIn",
  "hideMethod": "fadeOut"
}


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

    var idContract = $el.attr("data-Id");
    var contractId = $el.attr("data-contractId");
    var contractName = $el.text();
    var salesPricesImportIn = $el.attr("data-salesPrice");
    var insertSalesPriceVar = $el.attr("data-insertPrices");

    var insertPricesValue = insertSalesPriceVar == "1" ? "true" : "false";

    var insertSalesPriceValue = "";

    if(salesPricesImportIn != null && salesPricesImportIn != "")
        insertSalesPriceValue = salesPricesImportIn.split(',');

    setDataContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue);
}


function setDataContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue) {

    $("#idContract").val(idContract);
    
    $("#contractId").val(contractId);

    if(contractName != null && contractName != "")
        $("#contractName").val(contractName);
    
    $("#salesPriceVariationSelect").val(insertPricesValue);
    
    $("#salesPrice").val(insertSalesPriceValue).change();
}

function updateDataAttributeContractInfo(idContract,contractId,contractName,insertPricesValue,insertSalesPriceValue)
{
    var $el = $(".secondLevelButton[data-Id=" + idContract + "]");
 
    $el.attr("data-contractId",contractId);
    $el.attr("data-salesPrice",insertSalesPriceValue);
    $el.attr("data-insertPrices",insertPricesValue);

}

$(document).ready(function() {
     
    console.log("document ready");
     $('.js-example-basic-multiple').select2({
        placeholder: "Select sales prices",
        allowClear: true,
        width: '100%'
     });

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

        var idContract = $("#idContract").val();
        var priceMonitorId = $("#contractId").val();
        var salesPriceImportIn = $("#salesPrice").val();
        var isInsertSalesPrice = $("#salesPriceVariationSelect").val();

        var data = {
            'id' : idContract,
            'priceMonitorId': priceMonitorId,
            'salesPricesImport': salesPriceImportIn.join(),
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

                if(data == null) 
                   return;
                
                var data = jQuery.parseJSON( data );
                if(data != null)
                {
                   setDataContractInfo(data.id,data.priceMonitorId,data.name,data.isInsertSalesPrice,data.salesPricesImport);
                   updateDataAttributeContractInfo(data.id,data.priceMonitorId,data.name,data.isInsertSalesPrice,data.salesPricesImport)

                   toastr["success"]("Data are successfully saved!", "Successfully saved!");
                }
                else
                {
                    alert("ERROR");
                }
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }