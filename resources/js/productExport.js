
    var transactionHistoryMasterData = {},
    limit = 10,
    currentOffset = 0,
    currentMasterId = null,
    intervals = [];

    $(document).ready(function() {   
        $('#datetimepicker1').datetimepicker({
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
       // loadScheduledExport();
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
        console.log("start at");
        console.log(startAt);


        // var url = Pricemonitor['config']['urls']['productExportSaveSchedule'],
        //     startAtDate = createDateObject(document['pricemonitorProductExport']['startAtDate'].value),
        //     transferObject = {
        //         'startAt': startAtDate,
        //         'enableExport': document['pricemonitorProductExport']['enableExport'].checked,
        //         'exportInterval': document['pricemonitorProductExport']['exportInterval'].value
        //     }, params = {
        //         form_key: document['pricemonitorProductExport']['form_key'].value,
        //         pricemonitorId: Pricemonitor['config']['pricemonitorId']
        //     };

        // Pricemonitor['utility']['loadingWindow'].open();
        // Pricemonitor['ajax']['post'](url, transferObject, populateScheduleData, 'json', true, params);
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