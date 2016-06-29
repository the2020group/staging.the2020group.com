// JavaScript Document
jQuery(document).ready(function ($) {

    $("#wpTables").change(function () {
        var data = {
            action: 'wpaie_ajax_action',
            operation: 'wpTables',
            tableName: $(this).val()
        };
        $.post(ajaxurl, data, function (response) {
            console.log(response);
//            $("#wpTableCol").css("display", "block");
//            $("#wpTableColumns").html(response);
        });
    });
    $(".submitImportForm").submit(function (event) {
        event.preventDefault();
        var operationCategory = $(this).attr("data-category");
        $("input[data-category='" + operationCategory + "']").hide();
        $("#processing" + operationCategory).show();
        $('#loadingmessage').show();
        var data = {
            action: 'wpaie_ajax_action',
            operation: 'import',
            importData: $(this).serialize()
        };
        $.post(ajaxurl, data, function (response) {
            $('#loadingmessage').hide();
            $("#processing" + operationCategory).hide();
            $("#result" + operationCategory).show();
            $("input[data-category='" + operationCategory + "']").show();
            var output = JSON.parse(response);
            $("#recordsRead" + operationCategory).html(output.recordsRead);
            $("#recordsAdded" + operationCategory).html(output.recordsInserted);
            $("#recordsSkipped" + operationCategory).html(output.recordsSkipped);
            $("form[data-category='" + operationCategory + "']").hide();
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
        });
    });
    $(".wpaieExportForm").submit(function (event) {
        event.preventDefault();
        var operationCategory = $(this).attr("data-type");
        $("input[data-type='" + operationCategory + "']").hide();
        $("#processing" + operationCategory).show();
        $('#loadingmessage').show();
        var data = {
            action: 'wpaie_ajax_action',
            operation: 'export',
            exportData: $(this).serialize()
        };
        $.post(ajaxurl, data, function (response) {
            $('#loadingmessage').hide();
            $("#processing" + operationCategory).hide();
            $("input[data-type='" + operationCategory + "']").show();
            $("#result" + operationCategory).show();
            var output = JSON.parse(response);
            $("#recordsRead" + operationCategory).html(output.recordsRead);
            $("#downloadLink" + operationCategory).html("<a href='" + output.downloadLink + "' title='download file'>Download File</a>");
            $("html, body").animate({
                scrollTop: 0
            }, "slow");
        });
    });

});