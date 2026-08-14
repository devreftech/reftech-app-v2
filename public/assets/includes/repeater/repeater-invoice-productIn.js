$(function () {
    var invoiceRepeater = $(".form-invoice-repeater");
    if (invoiceRepeater.length) {
        var row = 2;
        var col = 1;
        invoiceRepeater.on("submit", function (e) {
            e.preventDefault();
        });
        invoiceRepeater.repeater({
            show: function () {
                var replacement = $(this).find(".invoice-item-replacement");
                var warehouse = $(this).find(".invoice-item-warehouse");
                var price = $(this).find(".invoice-item-price");
                var priceLabel = $(this).find(".invoice-item-price-label");
                var disc = $(this).find(".invoice-item-disc");
                var discLabel = $(this).find(".invoice-item-disc-label");
                var qty = $(this).find(".invoice-item-qty");
                var amountLabel = $(this).find(".amount-label");
                var amount = $(this).find(".invoice-item-amount");
                var amountLabel = $(this).find(".amount-label");
                var fromControl = $(this).find(".form-control, .form-select");
                var formLabel = $(this).find(".form-label");

                fromControl.each(function (i) {
                    var id = "form-repeater-" + row + "-" + col;
                    var idWarehouse = "warehouse-" + row;
                    var idPrice = "price-" + row;
                    var idPriceLabel = "price-label-" + row;
                    var idDisc = "disc-" + row;
                    var idDiscLabel = "disc-label-" + row;
                    var idReplacement = "replacement-dropdown-" + row;
                    var idQty = "qty-" + row;
                    var idAmount = "amount-" + row;
                    var idAmountLabel = "amount-label-" + row;
                    $(replacement[i]).attr("data-id", row);
                    $(replacement[i]).attr("id", idReplacement);
                    $(warehouse[i]).attr("id", idWarehouse);
                    $(price[i]).attr("id", idPrice);
                    $(priceLabel[i]).attr("id", idPriceLabel);
                    $(priceLabel[i]).attr("data-id", row);
                    $(disc[i]).attr("id", idDisc);
                    $(discLabel[i]).attr("id", idDiscLabel);
                    $(discLabel[i]).attr("data-id", row);
                    $(qty[i]).attr("id", idQty);
                    $(qty[i]).attr("data-id", row);
                    $(amount[i]).attr("id", idAmount);
                    $(amountLabel[i]).attr("id", idAmountLabel);
                    $(amount[i]).attr("data-id", row);
                    col++;
                });

                row++;

                $(this).slideDown();
            },
            remove: function (e) {
                confirm("Are you sure you want to delete this element?") &&
                    $(this).remove(e);
            },
        });
    }
});
