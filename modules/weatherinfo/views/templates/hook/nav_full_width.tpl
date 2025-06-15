<div class="container">
    {if $weatherinfo}
        <div class="row">
            <div class="col-md-12">
                <p><img src="https://openweathermap.org/img/wn/{$weatherinfo.icon}@2x.png" alt="Weather icon" style="height:24px;"> {$weatherinfo.countryCode} - {$weatherinfo.city} - {$weatherinfo.temp}°C - {$weatherinfo.humidity}%</p>
            </div>
        </div>
    {else}
        <div class="row">
            <div class="col-md-12">
                <p>{l s='There is not weather information available.' mod='weatherinfo'}</p>
            </div>
        </div>
    {/if}
</div>