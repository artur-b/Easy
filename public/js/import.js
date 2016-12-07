$(function() {
    $('input#xls[multiple]').change(function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, ''),
            fLabel = ((numFiles % 10 > 4) || (numFiles % 10 < 2)) ? " plików" : " pliki",
            log = numFiles > 1 ? "wybrano " + numFiles + fLabel : label;
            if (numFiles) {
                $(".import-help").text(log);
                $("#import-submit").removeAttr("disabled");
            } else {
                $(".import-help").text("wybierz pliki");
                $("#import-submit").attr("disabled", true);
            }
    });
});
