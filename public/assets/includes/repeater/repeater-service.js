$(function () {
    var invoiceRepeater = $('.form-invoice-repeater-1');

    if (invoiceRepeater.length) {
        invoiceRepeater.repeater({
            show: function () {
                var wrapper = $(this).closest('.repeater-wrapper');
                var colRepeater = wrapper.closest('.col-repeater').data('repeater-list');
                var newIndex = wrapper.siblings('.repeater-wrapper').length + 1; // Calculate next index

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
                    var id = "form-repeater-" + colRepeater + "-" + newIndex  ;
                    // var nameProduct + colRepeater [" = "product  + "]";
                    var idPrice = "price-" + colRepeater + "-" + newIndex  ;
                    var idDetailProduct = "detailProduct-" + colRepeater + "-" + newIndex  ;
                    var idPriceLabel = "priceLabel-" + colRepeater + "-" + newIndex  ;
                    var idQty = "qty-" + colRepeater + "-" + newIndex  ;
                    var idDisc = "disc-" + colRepeater + "-" + newIndex  ;
                    var idInfo = "info-qty-" + colRepeater + "-" + newIndex  ;
                    var idAmount = "amount-" + colRepeater + "-" + newIndex  ;
                    var idStock = "info-stock-" + colRepeater + "-" + newIndex  ;
                    var idWeight = "info-weight-" + colRepeater + "-" + newIndex  ;
                    var idAmountLabel = "amount-label-" + colRepeater + "-" + newIndex  ;
                    $(product[i]).attr("data-id", newIndex);
                    // $(product[i]).attr("name", nameProduct);
                    $(price[i]).attr("id", idPrice);
                    $(price[i]).val('');
                    $(detailProduct[i]).attr("id", idDetailProduct);
                    $(priceLabel[i]).attr("id", idPriceLabel);
                    $(priceLabel[i]).attr("data-id", newIndex);
                    $(priceLabel[i]).val('');
                    $(qty[i]).attr("id", idQty);
                    $(qty[i]).attr("data-id", newIndex);
                    $(info[i]).attr("id", idInfo);
                    $(info[i]).attr("data-id", newIndex);
                    $(info[i]).val('Pcs');
                    $(info[i]).prop("selected", true);
                    $(disc[i]).attr("id", idDisc).val(0);
                    $(disc[i]).attr("data-id", newIndex).val(0);
                    $(amount[i]).attr("id", idAmount);
                    $(amountLabel[i]).attr("id", idAmountLabel);
                    $(amount[i]).val('');
                    $(amountLabel[i]).val('');
                    $(amount[i]).attr("data-id", newIndex);
                    $(stock[i]).attr("id", idStock);
                    $(weight[i]).attr("id", idWeight);
                });
    
                $(this).slideDown();

                // Update IDs and attributes dynamically
                // $(this)
                //     .find(".form-control, .form-select")
                //     .each(function (index, element) {
                //         var newId = $(element)
                //             .attr("id")
                //             .replace(/\d+-\d+$/, `${colRepeater}-${newIndex}`);
                //         $(element).attr("id", newId);
                //     });
                //     $(this)
                //         .find(".form-control, .form-select")
                //         .each(function (index, element) {
                //             var newId = $(element)
                //                 .attr("data-id")
                //                 .replace(/\d+-\d+$/, `${colRepeater}`);
                //             $(element).attr("data-id", newId);
                //         });

                // $(this)
                //     .find('[name]')
                //     .each(function (index, element) {
                //         var name = $(element).attr("name");
                //         var updatedName = name.replace(/\[\d+\]/, `[${newIndex}]`);
                //         $(element).attr("name", updatedName);
                //     });

                $(this).slideDown();
            },
            remove: function (e) {
                if (confirm("Are you sure you want to delete this element?")) {
                    $(this).slideUp(function () {
                        $(this).remove();
                    });
                }
            },
        });

        // Event listener for add button
        $('[data-repeater-create]').click(function () {
            var colRepeater = $(this).data('repeater-create'); // Get repeater-create value
            var container = $(`.col-repeater[data-repeater-list="${colRepeater}"]`);

            if (container.length) {
                container.find('[data-repeater-list]').repeater('show');
            }
        });
    }
});
