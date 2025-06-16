{if $config.position == 'popup'}
    <div id="popup-cookie-banner-container" style="background-color:rgba(0, 0, 0, 0.5);">
        <div id="cookie-banner" class="p-2" style="background-color: {$config.bg}; top: 50%; left: 50%; transform: translate(-50%, -50%);">
            <div style="color: {$config.text_color};">
                {$config.text}
            </div>
            <div class="mt-2">
                <button id="cookie-accept" class="btn mr-2" style="background-color: {$config.accept_btn_color}; color: {$config.accept_text_color};">{$config.accept_text}</button>
                <button id="cookie-decline" class="btn" style="background-color: {$config.decline_btn_color}; color: {$config.decline_text_color};">{$config.decline_text}</button>
            </div>
        </div>
    </div>
    
{else}

    <div id="cookie-banner" class="w-100 p-2" style="background-color: {$config.bg}; {if $config.position == 'top'} top: 0; {else} bottom: 0;{/if} ">
        <div style="color: {$config.text_color};">
            {$config.text}
        </div>
        <div class="mt-2">
            <button id="cookie-accept" class="btn mr-2" style="background-color: {$config.accept_btn_color}; color: {$config.accept_text_color};">{$config.accept_text}</button>
            <button id="cookie-decline" class="btn" style="background-color: {$config.decline_btn_color}; color: {$config.decline_text_color};">{$config.decline_text}</button>
        </div>
    </div>
    
{/if}

{literal}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var banner = document.getElementById('cookie-banner');
    var accept = document.getElementById('cookie-accept');
    var decline = document.getElementById('cookie-decline');
    var popup = document.getElementById('popup-cookie-banner-container');

    // Show banner only if not already accepted/declined
    if (localStorage.getItem('cookie_consent') === null) {
        banner.style.display = 'flex';
        popup ? popup.style.display = 'flex' : null;
    } else {
        banner.style.display = 'none';
        popup ? popup.style.display = 'none' : null;
    }

    accept.onclick = function() {
        localStorage.setItem('cookie_consent', 'accepted');
        banner.style.display = 'none';
        popup ? popup.style.display = 'none' : null;
    };
    decline.onclick = function() {
        localStorage.setItem('cookie_consent', 'declined');
        banner.style.display = 'none';
        popup ? popup.style.display = 'none' : null;
    };
});
</script>
{/literal}