/**
 * Reftech ERP - Universal Currency & Thousands Input Masking Engine
 * Automatically formats numeric inputs with Indonesian thousand separators (000.000)
 * Handles live typing, caret preservation, dynamic repeaters, and form submission.
 */
(function (window, document) {
    'use strict';

    /**
     * Format a raw string or number into Indonesian thousand dot-separated format:
     * Example: "1500000" -> "1.500.000", "4311880,18" -> "4.311.880,18"
     */
    function formatRupiah(val) {
        if (val === null || val === undefined) return '';
        var str = String(val).trim();
        if (str === '') return '';

        // Check if negative
        var isNegative = str.indexOf('-') === 0;

        // Check for decimal comma
        var hasComma = str.indexOf(',') !== -1;
        if (hasComma) {
            var parts = str.split(',');
            var intDigits = parts[0].replace(/\D/g, '').replace(/^0+/, '') || '0';
            var decDigits = parts.slice(1).join('').replace(/\D/g, '').slice(0, 2);
            var formattedInt = intDigits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            var result = formattedInt + (parts.length > 1 ? ',' + decDigits : '');
            return isNegative ? '-' + result : result;
        }

        // Strip non-digits
        var digits = str.replace(/\D/g, '');
        if (!digits) return '';

        // Remove leading zeroes if longer than 1 digit
        digits = digits.replace(/^0+/, '') || '0';

        // Add dot separator every 3 digits from the right
        var formatted = digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

        return isNegative ? '-' + formatted : formatted;
    }

    /**
     * Unmask a dot-separated string into pure numeric digits:
     * Example: "1.500.000" -> "1500000", "4.311.880,18" -> "4311880.18"
     */
    function unmaskRupiah(val) {
        if (val === null || val === undefined) return '';
        var str = String(val).trim();
        var isNegative = str.indexOf('-') === 0;
        var hasComma = str.indexOf(',') !== -1;
        if (hasComma) {
            var parts = str.split(',');
            var intDigits = parts[0].replace(/\D/g, '');
            var decDigits = parts.slice(1).join('').replace(/\D/g, '').slice(0, 2);
            var result = (intDigits || '0') + '.' + decDigits;
            return isNegative ? '-' + result : result;
        }
        var digits = str.replace(/\D/g, '');
        return isNegative ? '-' + digits : digits;
    }

    /**
     * Target selectors for rupiah masked inputs
     */
    var SELECTOR = '.rupiah-mask, .input-rupiah, .format-rupiah, .rupiah-input, [data-type="currency"]:not(.invoice-item-price-label):not(.no-rupiah-mask), [data-rupiah], input[name="harga_jual"], input[name="harga_jual[]"], input[name="selling_price"]';

    /**
     * Apply masking on a single input element with caret preservation
     */
    function maskInputElement(input) {
        if (!input || input.readOnly || input.disabled) return;

        // If input is type="number", change to type="text" with inputmode="numeric"
        // because HTML5 number inputs reject dot separators
        if (input.type === 'number') {
            try {
                input.type = 'text';
                input.setAttribute('inputmode', 'numeric');
                input.removeAttribute('step');
            } catch (e) {
                // Ignore if browser restricts changing type
            }
        }

        var oldVal = input.value;
        var oldCursor = input.selectionStart || 0;
        var digitsBeforeCursor = oldVal.substring(0, oldCursor).replace(/\D/g, '').length;

        var formatted = formatRupiah(oldVal);
        if (formatted !== oldVal) {
            input.value = formatted;

            // Calculate new cursor position based on number of digits before cursor
            var newCursor = 0;
            var digitCount = 0;
            for (var i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) {
                    digitCount++;
                }
                if (digitCount <= digitsBeforeCursor) {
                    newCursor = i + 1;
                } else {
                    break;
                }
            }
            try {
                input.setSelectionRange(newCursor, newCursor);
            } catch (err) {}
        }
    }

    /**
     * Initialize masking on all matching inputs in the DOM
     */
    function initAllMasks(context) {
        var root = context || document;
        var inputs = root.querySelectorAll(SELECTOR);
        for (var i = 0; i < inputs.length; i++) {
            var el = inputs[i];
            if (el.type === 'number') {
                try {
                    el.type = 'text';
                    el.setAttribute('inputmode', 'numeric');
                } catch (e) {}
            }
            if (el.value) {
                el.value = formatRupiah(el.value);
            }
        }
    }

    // -------------------------------------------------------------
    // Event Listeners (Global Delegation)
    // -------------------------------------------------------------

    // Live typing and input
    document.addEventListener('input', function (e) {
        var target = e.target;
        if (target && target.matches && target.matches(SELECTOR)) {
            maskInputElement(target);
        }
    }, true);

    // Change & Blur
    document.addEventListener('change', function (e) {
        var target = e.target;
        if (target && target.matches && target.matches(SELECTOR)) {
            if (target.value) {
                target.value = formatRupiah(target.value);
            }
        }
    }, true);

    document.addEventListener('blur', function (e) {
        var target = e.target;
        if (target && target.matches && target.matches(SELECTOR)) {
            if (target.value) {
                target.value = formatRupiah(target.value);
            }
        }
    }, true);

    // Auto init on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initAllMasks();
        });
    } else {
        initAllMasks();
    }

    // Modal show/shown listener (Bootstrap 4/5)
    document.addEventListener('show.bs.modal', function (e) {
        if (e.target) initAllMasks(e.target);
    });
    document.addEventListener('shown.bs.modal', function (e) {
        if (e.target) initAllMasks(e.target);
    });

    // Dynamic Observer for repeaters / dynamically added rows / modals
    if (typeof MutationObserver !== 'undefined') {
        var observer = new MutationObserver(function (mutations) {
            for (var i = 0; i < mutations.length; i++) {
                var addedNodes = mutations[i].addedNodes;
                for (var j = 0; j < addedNodes.length; j++) {
                    var node = addedNodes[j];
                    if (node.nodeType === 1) { // ELEMENT_NODE
                        if (node.matches && node.matches(SELECTOR)) {
                            maskInputElement(node);
                        }
                        if (node.querySelectorAll) {
                            initAllMasks(node);
                        }
                    }
                }
            }
        });
        observer.observe(document.body || document.documentElement, {
            childList: true,
            subtree: true
        });
    }

    // Unmask on form submit so backend receives clean raw numbers
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form && form.querySelectorAll) {
            var maskedInputs = form.querySelectorAll(SELECTOR);
            for (var i = 0; i < maskedInputs.length; i++) {
                var input = maskedInputs[i];
                if (input.value) {
                    input.value = unmaskRupiah(input.value);
                }
            }
        }
    }, true);

    // Export to window for programmatic use
    window.ReftechMask = {
        format: formatRupiah,
        unmask: unmaskRupiah,
        init: initAllMasks,
        maskElement: maskInputElement
    };

})(window, document);
