$(function() {
    $("#export-customers").click(function() {
        e.preventDefault();
        window.location.href = "admin/usersExport/";
    });
    $("#export-orders").click(function(e) {
        e.preventDefault();
        window.location.href = "orders/export";
    });
});
