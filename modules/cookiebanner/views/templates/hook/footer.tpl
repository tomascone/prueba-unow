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
    var cookieSaveUrl = '{/literal}{$link->getModuleLink('cookiebanner', 'savecookie', [], true)}{literal}';

    var banner = $('#cookie-banner');
    var accept = $('#cookie-accept');
    var decline = $('#cookie-decline');
    var popup = $('#popup-cookie-banner-container');

    function handleConsent(consentValue) {
        $.ajax({
            url: cookieSaveUrl,
            type: 'POST',
            data: { consent: consentValue },
            success: function(response) {
                banner.hide();
                if (typeof popup !== 'undefined' && popup !== null) {
                    popup.hide();
                }
            }
        });
    }

    accept.on('click', function() {
        handleConsent('accepted');
    });

    decline.on('click', function() {
        handleConsent('declined');
    });
});
</script>
{/literal}