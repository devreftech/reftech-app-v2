$(function(){
    var invoiceRepeater = $('.form-invoice-repeater')
    if (invoiceRepeater.length) {
        var wrapperDivs = document.querySelectorAll('div.repeater-wrapper');
        var countWrap =  wrapperDivs.length;
        console.log(countWrap + 1);
        var row = countWrap + 1;
        var col = 1;
        invoiceRepeater.on("submit", function (e) {
            e.preventDefault();
        });
        invoiceRepeater.repeater({
            show: function () {
                var product = $(this).find(".invoice-item-product");
                var detailProduct = $(this).find(".invoice-item-detail-product");
                var price = $(this).find(".invoice-item-price");
                var priceLabel = $(this).find(".invoice-item-price-label");
                var qty = $(this).find(".invoice-item-qty");
                var disc = $(this).find(".invoice-item-disc");
                var info = $(this).find(".invoice-item-info");
                var amount = $(this).find(".invoice-item-amount");
                var amountLabel = $(this).find(".amount-label");
                var fromControl = $(this).find(".form-control, .form-select");
                var stock = $(this).find(".info-stock-label");
                var weight = $(this).find(".info-weight-label");
                var formLabel = $(this).find(".form-label");
    
                fromControl.each(function (i) {
                    var id = "form-repeater-" + row + "-" + col;
                    // var nameProduct = "product[" + col + "]";
                    var idPrice = "price-" + row;
                    var idDetailProduct = "detailProduct-" + row;
                    var idPriceLabel = "priceLabel-" + row;
                    var idQty = "qty-" + row;
                    var idDisc = "disc-" + row;
                    var idInfo = "info-qty-" + row;
                    var idAmount = "amount-" + row;
                    var idStock = "info-stock-" + row;
                    var idWeight = "info-weight-" + row;
                    var idAmountLabel = "amount-label-" + row;
                    $(product[i]).attr("data-id", row);
                    // $(product[i]).attr("name", nameProduct);
                    $(price[i]).attr("id", idPrice);
                    $(price[i]).val('');
                    $(detailProduct[i]).attr("id", idDetailProduct);
                    $(priceLabel[i]).attr("id", idPriceLabel);
                    $(priceLabel[i]).attr("data-id", row);
                    $(priceLabel[i]).val('');
                    $(qty[i]).attr("id", idQty);
                    $(qty[i]).attr("data-id", row);
                    $(info[i]).attr("id", idInfo);
                    $(info[i]).attr("data-id", row);
                    $(info[i]).val('Pcs');
                    $(info[i]).prop("selected", true);
                    $(disc[i]).attr("id", idDisc).val(0);
                    $(disc[i]).attr("data-id", row).val(0);
                    $(amount[i]).attr("id", idAmount);
                    $(amountLabel[i]).attr("id", idAmountLabel);
                    $(amount[i]).val('');
                    $(amountLabel[i]).val('');
                    $(amount[i]).attr("data-id", row);
                    $(stock[i]).attr("id", idStock);
                    $(weight[i]).attr("id", idWeight);
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

