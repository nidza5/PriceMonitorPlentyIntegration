
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
    loadTransactionHistoryMasterDataImport(limit, currentOffset);
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
              
              toastr["success"]("Price import has been started.");
                    
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

  
    function loadTransactionHistoryMasterDataImport(limitPrices, offsetPrices)
    {
        var dataOption = {
            'pricemonitorId' : $("#contractId").val(),
            'type': 'import_prices',
            'limit' : limitPrices,
            'offset' : offsetPrices
        };
        
        currentOffset = offsetPrices;
        transactionHistoryMasterDataPrices = {};

        $.ajax({
            type: "GET",
            url: "/getTransactionHistory",
            data: dataOption,
            success: function(data)
            {
                populateTransactionHistoryMasterTablePrices(data, limitPrices, offsetPrices);
            },
            error: function(xhr)
            {
                console.log(xhr);
            }
        });
    }

    function populateTransactionHistoryMasterTablePrices(response, limit, offset)
    {
        var wrapper = document.getElementById('transaction-history-import-master');
 
         if(response == null)
             return;
                      
         var data = jQuery.parseJSON(response);

         console.log("populateTransactionHistoryMasterTablePrices");
         console.log(data);

 
         populateTable(data, wrapper, limit, offset,
             createTransactionHistoryMasterRow,
             loadTransactionHistoryMasterDataImport);
     
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

      document['pricemonitorPriceImport']['enableImport'].checked = response.enableImport == 1 ? true : false;
        
    }
