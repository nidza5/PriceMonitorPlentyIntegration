
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
     loadLastImportData();
     loadScheduledImport();
}

 /**
     * Registers events for import now and save action
     */
    function registerEventListenersPrices()
    {
        var importNow = document.getElementById('import-now'),
            saveActionPrices = document.getElementById('pricemonitor-product-import-submit');

        importNow.addEventListener('click', onClickImportNow);
        saveActionPrices.addEventListener('click', onClickSavePrices);
    }

    /**
     * Event for runs import now action
     */
    function onClickImportNow()
    {
        var transferObject = {
            'pricemonitorId' : $("#contractId").val()
        };

        $.ajax({
            type: "POST",
            url: "/runPriceImport",
            data: transferObject,
            success: function(data)
            {
                if(data == null) 
                   return;
              
              toastr["success"]("Price import has been started.");
                    
            },
            error: function(data)
            {
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
            }
        });
    }

    function populateTransactionHistoryMasterTablePrices(response, limit, offset)
    {
        var wrapper = document.getElementById('transaction-history-import-master');
 
         if(response == null)
             return;
                      
         var data = jQuery.parseJSON(response);

         populateTable(data, wrapper, limit, offset,
             createTransactionHistoryMasterRowImport,
             loadTransactionHistoryMasterDataImport);
     
    }

    function createTransactionHistoryMasterRowImport(data, index, table) {
        var div = document.createElement('button'), row = table.insertRow();

        var linkText = document.createTextNode("details");
        div.appendChild(linkText);
        div.classList.add('pricemonitor-transaction-history-details','btn', 'btn-success');
        div.setAttribute('data-id', data.id);
        div.addEventListener('click', onDetailClickImport);

        row.insertCell().innerHTML = index;
        row.insertCell().innerHTML = data.importTime;
        row.insertCell().innerHTML = data.status;
        row.insertCell().innerHTML = data.importedPrices;
        row.insertCell().innerHTML = data.updatedPrices;
        row.insertCell().innerHTML = data.failedCount;
        row.insertCell().innerHTML = data.note;

        if (data.inProgress) {
            row.insertCell();
        } else {
            row.insertCell().appendChild(div);
        }

        transactionHistoryMasterData[data.id] = data;
    }

    function onDetailClickImport() {
        var masterId = this.getAttribute('data-id');

        if (masterId) {
            loadTransactionHistoryDetailDataImport(limit, 0, masterId);
        }
    }

    function loadTransactionHistoryDetailDataImport(limit, offset, masterId) {

        var masterData = transactionHistoryMasterData[masterId];

        if (masterData) {
            currentMasterId = masterId;
            document.getElementById('pricemonitor-export-time').innerHTML = masterData.exportTime;
            document.getElementById('pricemonitor-export-status').innerHTML = masterData.status;
            document.getElementById('pricemonitor-export-note').innerHTML = masterData.note;
         }

        var dataOption = {
            'pricemonitorId' : $("#contractId").val(),
            'type': 'import_prices',
            'limit' : limit,
            'offset' : offset,
            'masterId': currentMasterId
        };

        $.ajax({
            type: "GET",
            url: "/getTransactionHistory",
            data: dataOption,
            success: function(data)
            {
                populateTransactionHistoryDetailTableImport(data, limit, offset);
            },
            error: function(xhr)
            {
            }
        }); 

    }

    function populateTransactionHistoryDetailTableImport(response, limit, offset)
    {
        var wrapper = document.getElementById('transaction-history-import-detail'),
        modal = document.getElementById('pricemonitor-transaction-history-import-detail-modal');

        if(response == null)
            return;
                     
        var data = jQuery.parseJSON(response);

        populateTable(data, wrapper, limit, offset,
            createTransactionHistoryDetailRow,
            loadTransactionHistoryDetailData);

         $('#pricemonitor-transaction-history-import-detail-modal').modal('show'); 
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
                populateScheduleDataPrices(data);
            },
            error: function(xhr)
            {
            }
        }); 
    }

   function populateScheduleDataPrices(data) {
      if(data == null)
         return;
          
      var response = jQuery.parseJSON(data);

      document['pricemonitorPriceImport']['enableImport'].checked = response.enableImport == 1 ? true : false;
        
    }

    function loadLastImportData()
    {
        var dataOption = {
            'pricemonitorId' : $("#contractId").val(),
            'type': 'import_prices'
        };
        
        $.ajax({
            type: "GET",
            url: "/getLastTransactionHistory",
            data: dataOption,
            success: function(data)
            {
                populateLastTransactionBoxImport(data);
            },
            error: function(xhr)
            {
            }
        });
    }

    function populateLastTransactionBoxImport(response)
    {
        var dataResponse = null;
        if(response !== null)
            dataResponse = jQuery.parseJSON(response);

        var contract = dataResponse,
            lastExportBox = document.getElementById('pricemonitor-last-import');
        
        if (contract['importStart']) {
            $('#lastImportStartedAt').html(contract.importStart);
            $('#successfullyLastImport').html(contract.importSuccessCount);
            $("#productImportNoSync").hide();
            $("#dataLastImport").show();           
        }
        else
        {
            $("#dataLastImport").hide();
            $("#productImportNoSync").show();
        }
    }