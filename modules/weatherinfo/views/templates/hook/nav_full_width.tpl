<div class="container">
    {if $weatherinfo}
        <div class="row">
            <div class="col-md-12">
                <h2>{l s='Weather Information' mod='weatherinfo'}</h2>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <p>{l s='City:' mod='weatherinfo'} {$weatherinfo.city}</p>
                <p>{l s='Country:' mod='weatherinfo'} {$weatherinfo.country}</p>
                <p>{l s='Temp:' mod='weatherinfo'} {$weatherinfo.temp}°C</p>
                <p>{l s='Humidity:' mod='weatherinfo'} {$weatherinfo.humidity}%</p>
            </div>
        </div>
    {else}
        <div class="row">
            <div class="col-md-12">
                <h2>{l s='There is not weather information available.' mod='weatherinfo'}</h2>
            </div>
        </div>
    {/if}
</div>