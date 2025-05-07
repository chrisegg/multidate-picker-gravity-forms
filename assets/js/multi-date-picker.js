(function($) {
    const configs = window.gfMultiDatePickerConfig || [];
    const initializedFields = new Set();

    function initializeMultiDatePickers() {
        if (!$.fn.datepicker) {
            console.warn('jQuery UI Datepicker is not available.');
            return;
        }

        configs.forEach(config => {
            const fieldSelector = `#input_${config.form_id}_${config.field_id}`;
            if (initializedFields.has(fieldSelector)) return;
            initializedFields.add(fieldSelector);

            const hiddenFieldSelector = `${fieldSelector}_hidden`;
            const $input = $(fieldSelector);
            const selectedDates = new Set($input.val()?.split(', ').filter(Boolean));

            // Ensure the field is wrapped and has a calendar icon
            if (!$input.parent().hasClass('gf-multi-date-field')) {
                $input.wrap('<div class="gf-multi-date-field"></div>').after('<span class="calendar-icon" role="button" aria-label="Open date picker"></span>');
            }

            // Add hidden field for compatibility
            if (!$(hiddenFieldSelector).length) {
                $input.after(`<input type="hidden" id="${hiddenFieldSelector.replace('#', '')}" name="${hiddenFieldSelector.replace('#', '')}" value="${Array.from(selectedDates).join(', ')}">`);
            }

            // Initialize datepicker
            $input.addClass('gfield_date_multi').datepicker({
                dateFormat: config.date_format,
                beforeShow: (input, inst) => {
                    setTimeout(() => {
                        if (!$(".ui-datepicker-close-btn").length) {
                            $(inst.dpDiv).append('<button type="button" class="ui-datepicker-close-btn" aria-label="Close date picker">×</button>');
                            $(".ui-datepicker-close-btn").on("click", () => $input.datepicker("hide"));
                        }
                    }, 10);
                },
                beforeShowDay: date => {
                    const dateString = $.datepicker.formatDate("mm/dd/yy", date);
                    return [true, selectedDates.has(dateString) ? "ui-state-highlight" : ""];
                },
                onSelect: dateText => {
                    selectedDates.has(dateText) ? selectedDates.delete(dateText) : selectedDates.add(dateText);
                    const datesArray = Array.from(selectedDates);
                    $input.val(datesArray.join(", "));
                    $(hiddenFieldSelector).val(datesArray.join(", "));
                    $input.datepicker("refresh");
                    setTimeout(() => $input.datepicker("show"), 0);
                }
            });

            // Open datepicker on icon click or input focus
            $input.parent().find(".calendar-icon").on("click", () => $input.datepicker("show"));
            $input.on("focus click", function() { $(this).datepicker("show"); });
        });
    }

    // Initialize on document ready and after Gravity Forms renders
    $(document).ready(initializeMultiDatePickers);
    $(document).on('gform_post_render', initializeMultiDatePickers);
})(jQuery);
