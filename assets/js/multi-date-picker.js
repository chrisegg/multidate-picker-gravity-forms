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
            const selectedDates = new Set($(fieldSelector).val()?.split(', ').filter(Boolean));

            $(fieldSelector).wrap('<div class="gf-multi-date-field"></div>').after('<span class="calendar-icon" role="button" aria-label="Open date picker"></span>');
            if (!$(hiddenFieldSelector).length) {
                $(fieldSelector).after(`<input type="hidden" id="${hiddenFieldSelector.replace('#', '')}" name="${hiddenFieldSelector.replace('#', '')}" value="${Array.from(selectedDates).join(', ')}">`);
            }

            $(fieldSelector).attr({
                'aria-describedby': 'datepicker-instructions',
                'aria-label': 'Select multiple dates'
            }).datepicker({
                dateFormat: config.date_format,
                beforeShow: (input, inst) => {
                    setTimeout(() => {
                        if (!$(".ui-datepicker-close-btn").length) {
                            $(inst.dpDiv).append('<button type="button" class="ui-datepicker-close-btn" aria-label="Close date picker">×</button>');
                            $(".ui-datepicker-close-btn").on("click", () => $(fieldSelector).datepicker("hide"));
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
                    $(fieldSelector).val(datesArray.join(", "));
                    $(hiddenFieldSelector).val(datesArray.join(", "));
                    $(fieldSelector).datepicker("refresh");
                    setTimeout(() => $(fieldSelector).datepicker("show"), 0);
                }
            });

            $(fieldSelector).parent().find(".calendar-icon").on("click", () => $(fieldSelector).datepicker("show"));
            $(fieldSelector).on("focus click", function() { $(this).datepicker("show"); });
        });
    }

    $(document).ready(initializeMultiDatePickers);
    $(document).on('gform_post_render', initializeMultiDatePickers);
})(jQuery);
