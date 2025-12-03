var CharCounterHelper = (function () {
    let countingMode = 'chars'; // Default mode: 'chars' or 'words'
    let countLimit = 0; // Default mode: 'chars' or 'words'

    function setCountingMode(mode) {
        if (mode === 'chars' || mode === 'words') {
            countingMode = mode;
        } else {
            console.warn('Invalid counting mode. Use "chars" or "words".');
        }
    }

    function getCountingMode() {
        return countingMode;
    }

    function setLimit(limit) {
        countLimit = limit;
    }

    function getLimit(limit) {
        return limit;
    }

    function countWords(text) {
        if (!text.trim()) return 0;

        // Split by whitespace and filter out empty strings
        return text.trim().split(/\s+/).filter(function(word) {
            return word.length > 0;
        }).length;
    }

    function countChars(text) {
        return text.length;
    }

    function updateCount(textarea, charCountDisplay) {
        let text = textarea.val();
        let count = 0;
        let displayText = '';

        if (countingMode === 'words') {
            count = countWords(text);
            displayText = 'Word count: ' + count;
        } else {
            count = countChars(text);
            displayText = 'Character count with spaces: ' + count;
        }

        charCountDisplay.text(displayText);

        if (count > countLimit) {
            charCountDisplay.addClass('text-danger');
        } else {
            charCountDisplay.removeClass('text-danger');
        }

        return count;
    }

    function countTotal(charCountSelectors, totalDisplay) {
        let totalSum = getCountTotal(charCountSelectors);

        totalDisplay.html(totalSum);

        if (totalSum > countLimit) {
            totalDisplay.closest('div').addClass('text-danger');
            totalDisplay.closest('div').removeClass('text-success');
        } else {
            totalDisplay.closest('div').removeClass('text-danger');
            totalDisplay.closest('div').addClass('text-success');
        }
    }

    function getCountTotal(charCountSelectors) {
        let totalSum = 0;

        charCountSelectors.each(function () {
            let count = parseInt($(this).text().replace(/\D+/g, ''), 10) || 0;
            totalSum += count;
        });

        return totalSum;
    }

    function runCounter(textareaSelector, charCountSelector, totalCharCountSelector, mode = 'chars', limit=0) {
        setCountingMode(mode);
        setLimit(limit)

        $(textareaSelector).off('input keydown').on('input keydown', function (event) {
            let textarea = $(this);
            let charCountDisplay = textarea.siblings(charCountSelector);

            // Only count when input changes or when space is pressed
            if (event.type === 'input' || event.key === ' ') {
                updateCount(textarea, charCountDisplay);
                countTotal($(charCountSelector), $(totalCharCountSelector));
            }
        });

        // Initialize counts on page load
        $(textareaSelector).each(function() {
            let textarea = $(this);
            let charCountDisplay = textarea.siblings(charCountSelector);
            updateCount(textarea, charCountDisplay);
        });

        // Update total count
        countTotal($(charCountSelector), $(totalCharCountSelector));
    }

    return {
        init: runCounter,
        updateCount: updateCount,
        countTotal: countTotal,
        setCountingMode: setCountingMode,
        getCountingMode: getCountingMode,
        setLimit: setLimit,
        getLimit: getLimit,
        countWords: countWords,
        countChars: countChars
    };
})();

// flash-helper.js

/**
 * Flash Message Helper Utility
 * Handles display of flash messages from server sessions
 */

const FlashHelper = {
    /**
     * Initialize and display flash messages
     * @param {string|object} flashData - Raw flash data from server
     * @param {object} options - Custom options for SweetAlert2
     */
    init: function(flashData = null, options = {}) {
        const parsedData = this.parseFlashData(flashData);

        if (parsedData) {
            this.showMessage(parsedData, options);
        }
    },

    /**
     * Parse flash data safely
     * @param {string|object} flashData
     * @returns {object|null} Parsed flash data or null
     */
    parseFlashData: function(flashData) {
        if (!flashData || flashData === 'null') {
            return null;
        }

        // If it's already an object, return it
        if (typeof flashData === 'object') {
            return flashData;
        }

        // If it's a string, try to parse it
        if (typeof flashData === 'string') {
            try {
                return JSON.parse(flashData);
            } catch (error) {
                console.error('Error parsing flash data:', error);
                return null;
            }
        }

        return null;
    },

    /**
     * Determine message type and configuration
     * @param {object} flashData
     * @returns {object} Message configuration
     */
    getMessageConfig: function(flashData) {
        const messageTypes = ['error', 'success', 'warning', 'info'];

        for (const type of messageTypes) {
            if (flashData[type]) {
                return {
                    icon: type,
                    title: type.charAt(0).toUpperCase() + type.slice(1),
                    text: flashData[type],
                    type: type
                };
            }
        }

        // Fallback for generic message
        if (flashData.message) {
            return {
                icon: 'info',
                title: 'Information',
                text: flashData.message,
                type: 'info'
            };
        }

        return null;
    },

    /**
     * Show SweetAlert2 message
     * @param {object} flashData
     * @param {object} customOptions
     */
    showMessage: function(flashData, customOptions = {}) {
        const config = this.getMessageConfig(flashData);

        if (!config) return;

        // Default options
        const defaultOptions = {
            icon: config.icon,
            title: config.title,
            text: config.text,
            timer: config.type === 'error' ? 20000 : 20000,
            showConfirmButton: config.type === 'error',
            confirmButtonText: 'OK',
            toast: config.type !== 'error',
            position: config.type === 'error' ? 'center' : 'top-end'
        };

        // Merge with custom options
        const options = { ...defaultOptions, ...customOptions };

        Swal.fire(options);
    },

    /**
     * Show custom flash message programmatically
     * @param {string} type - error, success, warning, info
     * @param {string} message
     * @param {object} options
     */
    showCustom: function(type, message, options = {}) {
        this.showMessage({ [type]: message }, options);
    },

    /**
     * Quick methods for common message types
     */
    success: function(message, options = {}) {
        this.showCustom('success', message, options);
    },

    error: function(message, options = {}) {
        this.showCustom('error', message, options);
    },

    warning: function(message, options = {}) {
        this.showCustom('warning', message, options);
    },

    info: function(message, options = {}) {
        this.showCustom('info', message, options);
    }
};

// Auto-initialize if flash data exists in global scope
if (typeof window.flashData !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        FlashHelper.init(window.flashData);
    });
}
