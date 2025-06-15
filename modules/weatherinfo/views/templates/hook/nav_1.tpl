<div class="nav-weather-info">
    {if $weatherinfo}
        <span>
            <img src="https://openweathermap.org/img/wn/{$weatherinfo.icon}@2x.png" alt="Weather icon" style="height:24px;"> {$weatherinfo.country} - {$weatherinfo.city} - {$weatherinfo.temp}°C - {$weatherinfo.humidity}%
        </span>
    {else}
        <span>{l s='There is not weather information available.' mod='weatherinfo'}</span>
    {/if}
</div>