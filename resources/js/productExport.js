
    var transactionHistoryMasterData = {},
    limit = 10,
    currentOffset = 0,
    currentMasterId = null,
    intervals = [];

    $(document).ready(function() {   
        $('#datetimepicker1').datetimepicker({
            format: 'DD/MM/YYYY HH:mm',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                next: "fa fa-arrow-right",
                previous: "fa fa-arrow-left"
            }
        });
        document.getElementById("itemExport").addEventListener("click", initExportForm);
    });

    function initExportForm()
    {
        intervals = [];
        registerEventListenersExport();
      //  toggleFieldSets(true);
         loadTransactionHistoryMasterData(limit, currentOffset);
       // loadLastExportData();
         loadScheduledExport();
    }

    /**
     * Loads scheduled export data
     */
    function loadScheduledExport()
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

                populateScheduleData(data);
            },
            error: function(xhr)
            {
                console.log(xhr);
            }
        }); 
    }

    /**
     * Registers events for export now and save action
     */
    function registerEventListenersExport()
    {
        var scheduledExport = document.getElementById('enabled-scheduled-export'),
            exportNow = document.getElementById('export-now'),
            saveAction = document.getElementById('pricemonitor-product-export-submit');

        scheduledExport.addEventListener('click', onClickEnableScheduledExport);
        exportNow.addEventListener('click', onClickExportNow);
        saveAction.addEventListener('click', onClickSave);

        // refresh transaction history table view and
        // last export box on every 10s
        // var intervalId = setInterval(
        //     function () {
        //         loadTransactionHistoryMasterData(limit, currentOffset);
        //         loadLastExportData();
        //     }, 10000
        // );

       // intervals.push(intervalId);
    }

       /**
     * Event for change of enable/disable export checkbox
     */
    function onClickEnableScheduledExport()
    {
        toggleScheduledExport(this.checked)
    }


     /**
     * Loads transaction history master data
     *
     * @param limit
     * @param offset
     */
    function loadTransactionHistoryMasterData(limit, offset)
    {

        console.log("limiiit je ");
        console.log(limit);

        var dataOption = {
            'pricemonitorId' : $("#contractId").val(),
            'limit' : limit,
            'offset' : offset
        };
        
        currentOffset = offset;
        transactionHistoryMasterData = {};

        $.ajax({
            type: "GET",
            url: "/getTransactionHistory",
            data: dataOption,
            success: function(data)
            {
                populateTransactionHistoryMasterTable(data, limit, offset);
            },
            error: function(xhr)
            {
                console.log(xhr);
            }
        }); 
    }

    
   function populateTransactionHistoryMasterTable(response, limit, offset)
   {
       var wrapper = document.getElementById('transaction-history-export-master');

        if(response == null)
            return;
                     
        var data = jQuery.parseJSON(data);

        populateTable(response, wrapper, limit, offset,
            createTransactionHistoryMasterRow,
            loadTransactionHistoryMasterData)
    
   }

      /**
     * Toggles form for scheduled export depending on enabled/disabled.
     *
     * @param checked
     */
    function toggleScheduledExport(checked)
    {
        var startAtDate = document.getElementById('start-at-date'),
            repeatInterval = document.getElementById('export-interval');

        startAtDate.disabled = !checked;
        repeatInterval.disabled = !checked;
    }

    /**
     * Executes export of products
     */
    function onClickExportNow()
    {
        console.log("export now");
        var transferObject = {
            'pricemonitorId' : $("#contractId").val()
        };

        $.ajax({
            type: "POST",
            url: "/runProductExport",
            data: transferObject,
            success: function(data)
            {
                console.log(data);
                if(data == null) 
                    return;
                
                toastr["success"]("Product export has been started.");
                
                // if(typeof dataJson.token != 'undefined'  &&  typeof dataJson.queueName != 'undefined' && dataJson.token && dataJson.queueName) {
                //     toastr["success"]("Product export has been started.");
                //     callAssyncSync(dataJson);
                // }
                    
            },
            error: function(data)
            {
                console.log(data);
            }
        });
     }

    //  function callAssyncSync(dataForSync)
    //  {      
    //         var transferObject = {
    //             'queueName' : dataForSync.queueName,
    //             'token' : dataForSync.token,
    //             'pricemonitorId' : $("#contractId").val(),
    //             'filterType' : 'export_products'
    //         };

    //         console.log("data for sync transfer");
    //         console.log(transferObject);

    //         $.ajax({
    //             type: "POST",
    //             url: "/run",
    //             data: transferObject,
    //             async: true,
    //             success: function(data)
    //             {
    //                 console.log(data);
                    
    //                 if(data == null) 
    //                     return;

    //                 var dataSync = jQuery.parseJSON(data);

    //             //     if(dataSync != null && dataSync == true) {
    //             //         callAssyncSync(dataForSync);

    //             //     if(dataForSync.queueName == "Default") {
    //             //         dataForSync.queueName = "StatusChecking";
    //             //         callAssyncSync(dataForSync);
    //             //     }
    //             // }
                   
                    
    //             },
    //             error: function(data)
    //             {
    //                 console.log(data);
    //             }
    //         });
    //  }

      /**
     * Save all form elements
     */
    function onClickSave()
    {
        var startAt = $("#datetimepicker1").find("input").val();
        var startAtDate = createDateObject(startAt);
        
        var transferObject = {
            'startAt': startAtDate,
            'enableExport': document['pricemonitorProductExport']['enableExport'].checked,
            'exportInterval': document['pricemonitorProductExport']['exportInterval'].value,
            'pricemonitorId' : $("#contractId").val()

        };

        console.log("transfer object");
        console.log(transferObject);

        $.ajax({
            type: "POST",
            url: "/saveSchedule",
            data: transferObject,
            success: function(data)
            {
                if(data == null) 
                   return;

                populateScheduleData(data);

                toastr["success"]("Data are successfully saved!", "Successfully saved!");
            },
            error: function(data)
            {
                console.log(data);
            }
        });
    }


    function populateScheduleData(data)
    {
        if(data == null)
          return;
            
        var response = jQuery.parseJSON(data);

        document['pricemonitorProductExport']['enableExport'].checked = response.enableExport == 1 ? true : false;
       // toggleFieldSets(!response.data.hasMappings);
        toggleScheduledExport(response.enableExport);

        if (response.enableExport) {
            if (response.exportStart) {
                $('#datetimepicker1').find("input").val(createDateForViews(response.exportStart));                
            }

            if (response.exportInterval) {
                document['pricemonitorProductExport']['exportInterval'].value = response.exportInterval;
            }
        } else {
            $('#datetimepicker1').find("input").val(createDateForView(response.exportStart));
        }

    }

        /**
     * @param dateISOString
     * @return {*}
     */
    function createDateForViews(dateISOString)
    {
        var datetimeParts = dateISOString.split(' '),
            dateParts = datetimeParts[0].split('-'),
            timeParts = datetimeParts[1].split(':');

        if (dateParts.length === 3 && timeParts.length === 3) {
            var day = dateParts[2],
                month = dateParts[1],
                year = dateParts[0],
                hour = timeParts[0],
                minute = timeParts[1];

            return day + '/' + month + '/' + year + ' ' + hour + ':' + minute;
        }

        return dateISOString;
    }

     /**
     * Creates date object
     *
     * @param dateText
     * @return string
     */
    function createDateObject(dateText)
    {
        var datetimeParts = dateText.split(' '),
            dateParts = datetimeParts[0].split('/'),
            timeParts = datetimeParts[1].split(':'),
            day = dateParts[0] ? dateParts[0] : '',
            month = dateParts[1] ? dateParts[1] : '',
            year = dateParts[2] ? dateParts[2] : '',
            hour = timeParts[0],
            minutes = timeParts[1],
            date = new Date(year + '-' + month + '-' + day + 'T' + hour + ':' + minutes);

        var parts = date.toISOString().split('T');

        if (parts.length !== 2) {
            throw 'Unable to parse datetime.'
        }

        return parts[0] + ' ' + parts[1].split('.')[0];
    }

    /**
     * Creates one row for transaction history detail data
     *
     * @param data
     * @param index
     * @param table
     * @return {string}
     */
    function createTransactionHistoryDetailRow(data, index, table)
    {
        var row = table.insertRow();
        row.insertCell().innerHTML = index;
        row.insertCell().innerHTML = data.status;
        row.insertCell().innerHTML = data.gtin;
        row.insertCell().innerHTML = data.name;
        row.insertCell().innerHTML = data.refPrice;
        row.insertCell().innerHTML = data.minPrice;
        row.insertCell().innerHTML = data.maxPrice;
        row.insertCell().innerHTML = data.note;
    }

    /**
     * Creates one row for transaction history master data
     *
     * @param data
     * @param index
     * @param table
     * @return {string}
     */
    function createTransactionHistoryMasterRow(data, index, table)
    {
        var div = document.createElement('div'), row = table.insertRow();

        div.classList.add('pricemonitor-transaction-history-details');
        div.setAttribute('data-id', data.id);
        div.addEventListener('click', onDetailClick);

        row.insertCell().innerHTML = index;
        row.insertCell().innerHTML = data.exportTime;
        row.insertCell().innerHTML = data.status;
        row.insertCell().innerHTML = data.successCount;
        row.insertCell().innerHTML = data.failedCount;
        row.insertCell().innerHTML = data.note;

        if (data.inProgress) {
            row.insertCell();
        } else {
            row.insertCell().appendChild(div);
        }

        transactionHistoryMasterData[data.id] = data;
    }

    function populateTable(response, table, limit, offset, createRow, loadRecords)
    {
        var pageCount, rows = '',
            totalElement = table.querySelector('.pricemonitor-total'),
            pagination = table.querySelector('.pricemonitor-pagination'),
            tableBody = table.querySelector('tbody'),
            currentPage = pagination.getAttribute('data-current-page'),
            counter = offset;

        if (response.data === undefined || response.count === undefined) {
            return console.warn('Response must have data and count fields');
        }

        currentPage = parseInt((currentPage && offset !== 0) ? currentPage : 1);
        tableBody.innerHTML = '';

        var tableDataRows = response.data;

        for (var i = 0; i < tableDataRows.length; i++) {
            rows += createRow(tableDataRows[i], ++counter, tableBody);
        }

        pagination.innerHTML = '';
        totalElement.innerHTML = response.count;

        // pageCount = Math.ceil(response.count / limit);
        // pagination.setAttribute('data-page-count', pageCount);
        // pagination.setAttribute('data-limit', limit);

        // if (response.count > limit) {
        //     initPaginationButtons(pagination, pageCount, currentPage, loadRecords);
        // }

        // showNoDataMessage(table, response.count === 0);

        // if (response.count > 0) {
        //     registerTooltips(tableBody);
        // }
    }

    function initPaginationButtons(pagination, pageCount, currentPage, loadRecords)
    {
        var i, nextPage, previousPage, pagesToShow = [];

        nextPage = parseInt(currentPage) + 1;
        previousPage = parseInt(currentPage) - 1;

        createPaginationButton(pagination, '<', previousPage, false, loadRecords);

        if (pageCount > 6) {
            pagesToShow.push(1);

            if (currentPage > 2) {
                pagesToShow.push(currentPage - 1);
            }

            if (currentPage !== 1 && currentPage !== pageCount) {
                pagesToShow.push(currentPage);
            }

            if (currentPage < pageCount - 1) {
                pagesToShow.push(currentPage + 1);
            }

            pagesToShow.push(pageCount);
        } else {
            for (i = 1; i <= pageCount; i++) {
                pagesToShow.push(i);
            }
        }

        for (i = 0; i < pagesToShow.length; i++) {
            createPaginationButton(
                pagination,
                pagesToShow[i],
                pagesToShow[i],
                pagesToShow[i] === currentPage,
                loadRecords
            )
        }

        createPaginationButton(pagination, '>', nextPage, false, loadRecords);
    }


    function createPaginationButton(pagination, text, page, isCurrent, loadRecords)
    {
        var button = document.createElement('button');
        button.appendChild(document.createTextNode(text));
        button.setAttribute('data-page', page);
        button.addEventListener(
            'click',
            function () {
                goToPage(this.getAttribute('data-page'), pagination, loadRecords);
            }
        );

        if (isCurrent) {
            button.classList.add('back');
        }

        pagination.appendChild(button);
    }