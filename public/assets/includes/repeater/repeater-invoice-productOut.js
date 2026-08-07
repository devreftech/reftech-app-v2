$(function(){
    var invoiceRepeater = $('.form-invoice-repeater')
    if (invoiceRepeater.length) {
        var row = 2;
        var col = 1;
        invoiceRepeater.on("submit", function (e) {
            e.preventDefault();
        });
        invoiceRepeater.repeater({
            show: function () {
                var commodity = $(this).find(".invoice-item-commodity");
                var replacement = $(this).find(".invoice-item-replacement");
                var warehouse = $(this).find(".invoice-item-warehouse");
                var equivalent = $(this).find(".invoice-item-equivalent");
                var price = $(this).find(".invoice-item-price");
                var priceLabel = $(this).find(".invoice-item-price-label");
                var qty = $(this).find(".invoice-item-qty");
                var amountLabel = $(this).find(".amount-label");
                var amount = $(this).find(".invoice-item-amount");
                var qtyLabel = $(this).find(".info-max-label");
                var fromControl = $(this).find(".form-control, .form-select");
                var formLabel = $(this).find(".form-label");
    
                fromControl.each(function (i) {
                    var id = "form-repeater-" + row + "-" + col;
                    var idWarehouse = "warehouse-" + row;
                    var idPrice = "price-" + row;
                    var idPriceLabel = "price-label-" + row;
                    var idQty = "qty-" + row;
                    var idQtyLabel = "info-max-" + row;
                    var idReplacement = "replacement-dropdown-" + row;
                    var idEquivalent = "equivalent-dropdown-" + row;
                    var idAmount = "amount-" + row;
                    var idAmountLabel = "amount-label-" + row;
                    $(commodity[i]).attr("data-id", row);
                    $(replacement[i]).attr("id", idReplacement);
                    $(replacement[i]).attr("data-id", row);
                    $(warehouse[i]).attr("id", idWarehouse);
                    $(equivalent[i]).attr("id", idEquivalent);
                    $(equivalent[i]).attr("data-id", row);
                    $(price[i]).attr("id", idPrice);
                    $(priceLabel[i]).attr("id", idPriceLabel);
                    $(priceLabel[i]).attr("data-id", row);
                    $(qty[i]).attr("id", idQty);
                    $(qty[i]).attr("data-id", row);
                    $(qtyLabel[i]).attr("id", idQtyLabel);
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

