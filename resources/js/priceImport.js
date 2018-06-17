
 var templateNameId = 'pricemonitor-price-import',
 templateForPrice = document.getElementById(templateId),
 transactionHistoryMasterDataPrices = {},
 currentMasterIdPrices = null,
 currentOffsetPrices = 0,
 limitPrices = 10,
 intervalsPrices = [];

$(document).ready(function() {   
    
    document.getElementById("itemExport").addEventListener("click", initExportForm);
});

function initExportForm()
{
    intervals = [];
    registerEventListenersPrices();
    // loadTransactionHistoryMasterData(limit, currentOffset);
    // loadLastImportData();
    // loadScheduledImport();
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
        // var url = Pricemonitor['config']['urls']['priceImportRunNow'], params = {
        //     'form_key': document['pricemonitorPriceImport']['form_key'].value,
        //     'pricemonitorId': Pricemonitor['config']['pricemonitorId']
        // };

        // Pricemonitor['utility']['loadingWindow'].open();
        // Pricemonitor['ajax']['post'](url, {}, importStarted, 'json', true, params);

        function importStarted(response)
        {
            //success message
        }
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

    populateScheduleDataPrices(data) {

    }
