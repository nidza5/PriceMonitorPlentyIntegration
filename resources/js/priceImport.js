
 var templateNameId = 'pricemonitor-price-import',
 templateForPrice = document.getElementById(templateNameId),
 transactionHistoryMasterDataPrices = {},
 currentMasterIdPrices = null,
 currentOffsetPrices = 0,
 limitPrices = 10,
 intervalsPrices = [];

$(document).ready(function() {   
    
    document.getElementById("itemImport").addEventListener("click", initImportForm);
});

function initImportForm()
{
    intervals = [];
    registerEventListenersPrices();
    // loadTransactionHistoryMasterData(limit, currentOffset);
    // loadLastImportData();
     loadScheduledImport();
}

 /**
     * Registers events for import now and save action
     */
    function registerEventListenersPrices()
    {
        console.log("register event handlers prices");

        var importNow = document.getElementById('import-now'),
            saveActionPrices = document.getElementById('pricemonitor-product-import-submit');

        importNow.addEventListener('click', onClickImportNow);
        saveActionPrices.addEventListener('click', onClickSavePrices);

        // refresh transaction history table view and
        // last export box on every 10s
        // var intervalId = setInterval(
        //     function () {
        //         loadTransactionHistoryMasterData(limit, currentOffset);
        //         loadLastImportData();
        //     }, 10000
        // );

        // intervals.push(intervalId);
    }

    /**
     * Event for runs import now action
     */
    function onClickImportNow()
    {
        console.log("import now");
        var transferObject = {
            'pricemonitorId' : $("#contractId").val()
        };

        $.ajax({
            type: "POST",
            url: "/runPriceImport",
            data: transferObject,
            success: function(data)
            {
                console.log(data);
                if(data == null) 
                    return;
                
                var dataJson = jQuery.parseJSON(data);
                if(dataJson == null) {
                    console.log("dataJSON is null");
                    return;
                }
                
                if(typeof dataJson.token != 'undefined'  &&  typeof dataJson.queueName != 'undefined' && dataJson.token && dataJson.queueName) {
                    toastr["success"]("Product export has been started.");
                    callAssyncSyncImport(dataJson);
                }
                    
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

    function callAssyncSyncImport(dataForSync)
    {      
           var transferObject = {
               'queueName' : dataForSync.queueName,
               'token' : dataForSync.token,
               'pricemonitorId' : $("#contractId").val(),
               'filterType' : 'import_prices'
           };

           console.log("data for sync transfer import");
           console.log(transferObject);

           $.ajax({
               type: "POST",
               url: "/run",
               data: transferObject,
               async: true,
               success: function(data)
               {
                   console.log(data);
                   
                   if(data == null) 
                       return;

                   var dataSync = jQuery.parseJSON(data);

               //     if(dataSync != null && dataSync == true) {
               //         callAssyncSync(dataForSync);

               //     if(dataForSync.queueName == "Default") {
               //         dataForSync.queueName = "StatusChecking";
               //         callAssyncSync(dataForSync);
               //     }
               // }
                  
                   
               },
               error: function(data)
               {
                   console.log(data);
               }
           });
    }

     /**
     * Save all form elements
     */
    function onClickSavePrices()
    {        
        var transferObject = {
            'enableImport': document['pricemonitorPriceImport']['enableImport'].checked,
            'pricemonitorId' : $("#contractId").val()
        };

        console.log("transfer object");
        console.log(transferObject);

        $.ajax({
            type: "POST",
            url: "/saveSchedulePrices",
            data: transferObject,
            success: function(data)
            {
                if(data == null) 
                   return;

                populateScheduleDataPrices(data);

                toastr["success"]("Data are successfully saved!", "Successfully saved!");
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }

      /**
     * Loads schedule data
     */
    function loadScheduledImport()
    {

        var dataOption = {
            'pricemonitorId' : $("#contractId").val()
        };
        
        $.ajax({
            type: "GET",
            url: "/getSchedule",
            data: dataOption,
            success: function(data)
            {
                console.log(data);

                populateScheduleDataPrices(data);
            },
            error: function(xhr)
            {
                console.log(xhr);
            }
        }); 
    }

   function populateScheduleDataPrices(data) {
        console.log(data);

        if(data == null)
            return;
          
      var response = jQuery.parseJSON(data);

      document['pricemonitorPriceImport']['enableImport'].checked = response.enableExport == 1 ? true : false;
        
    }
