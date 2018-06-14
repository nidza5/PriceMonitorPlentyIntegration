
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
        registerEventListeners();
      //  toggleFieldSets(true);
      //  loadTransactionHistoryMasterData(limit, currentOffset);
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
    function registerEventListeners()
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
    //     var url = Pricemonitor['config']['urls']['productExportRunNow'], params = {
    //         'form_key': document['pricemonitorProductExport']['form_key'].value,
    //         'pricemonitorId': Pricemonitor['config']['pricemonitorId']
    //     };

    //     Pricemonitor['utility']['loadingWindow'].open();
    //     Pricemonitor['ajax']['post'](url, {}, exportStarted, 'json', true, params);

    //     function exportStarted(response)
    //     {
    //         Pricemonitor['utility']['showMessage'](response['message'], response['success']);
    //     }
     }

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

        toastr["success"]("Data are successfully saved!", "Successfully saved!");

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