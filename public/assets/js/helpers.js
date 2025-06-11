var WordCounterHelper = (function () {
    function updateCharCount(textarea, charCountDisplay) {
        let text = textarea.val();

        // Count all characters including spaces
        let charCount = text.length;

        charCountDisplay.text('Character count with spaces: ' + charCount);

        if (charCount > 2500) {
            charCountDisplay.addClass('text-danger');
        } else {
            charCountDisplay.removeClass('text-danger');
        }
    }

    function countTotalChars(charCountSelectors, totalDisplay) {
        let totalCharsSum = 0;

        charCountSelectors.each(function () {
            let charCount = parseInt($(this).text().replace(/\D+/g, ''), 10) || 0;
            totalCharsSum += charCount;
        });

        totalDisplay.html(totalCharsSum);

        if (totalCharsSum > 2500) {
            totalDisplay.closest('div').addClass('text-danger');
            totalDisplay.closest('div').removeClass('text-success');
        } else {
            totalDisplay.closest('div').removeClass('text-danger');
            totalDisplay.closest('div').addClass('text-success');
        }
    }

    function runCounter(textareaSelector, charCountSelector, totalCharCountSelector) {
        $(textareaSelector).off('input keydown').on('input keydown', function (event) {
            let textarea = $(this);
            let charCountDisplay = textarea.siblings(charCountSelector);

            // Only count characters when input changes or when space is pressed
            if (event.type === 'input' || event.key === ' ') {
                updateCharCount(textarea, charCountDisplay);
                countTotalChars($(charCountSelector), $(totalCharCountSelector));
            }
        });
    }

    return {
        init: runCounter,
        updateCharCount: updateCharCount,
        countTotalChars: countTotalChars
    };
})();
