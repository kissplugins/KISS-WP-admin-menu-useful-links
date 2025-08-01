/**
 * Admin settings page JavaScript functionality
 */
document.addEventListener('DOMContentLoaded', function() {
    function safeSet(key, value) {
        try { 
            if (window.localStorage) { 
                localStorage.setItem(key, value); 
            } 
        } catch (e) {
            // Silently fail if localStorage is not available
        }
    }
    
    function safeGet(key) {
        try { 
            return window.localStorage ? localStorage.getItem(key) : null; 
        } catch (e) { 
            return null; 
        }
    }
    
    function safeRemove(key) {
        try { 
            if (window.localStorage) { 
                localStorage.removeItem(key); 
            } 
        } catch (e) {
            // Silently fail if localStorage is not available
        }
    }

    var tabs = document.querySelectorAll('.kwamul-tab');
    var form = document.getElementById('kwamul-options-form');
    var submitBtn = document.getElementById('kwamul_submit');

    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            safeSet('kwamul_last_save', Date.now().toString());
        });
    }

    function changeTab(target) {
        var url = new URL(window.location.href);
        if (url.searchParams.get('tab') !== target) {
            url.searchParams.set('tab', target);
            window.location.href = url.toString();
        }
    }

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            var nextTab = this.getAttribute('data-tab');
            safeSet('kwamul_next_tab', nextTab);

            var lastSave = parseInt(safeGet('kwamul_last_save') || '0', 10);
            var now = Date.now();

            if (!form || now - lastSave < 5000) {
                changeTab(nextTab);
            } else {
                safeSet('kwamul_last_save', now.toString());
                form.submit();
            }
        });
    });

    var nextTab = safeGet('kwamul_next_tab');
    if (nextTab) {
        safeRemove('kwamul_next_tab');
        changeTab(nextTab);
    }
});