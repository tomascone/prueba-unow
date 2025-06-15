<?php
/**
* 2007-2025 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2025 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Weatherinfo extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'weatherinfo';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Tomas Carrasco';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Show users weather info');
        $this->description = $this->l('Module that shows users info about the weather');

        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('WEATHERINFO_HOURS_IN_CACHE', 24);
        Configuration::updateValue('WEATHERINFO_OPEN_WEATHER_KEY', '');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayNavFullWidth');
    }

    public function uninstall()
    {
        Configuration::deleteByName('WEATHERINFO_HOURS_IN_CACHE');
        Configuration::deleteByName('WEATHERINFO_OPEN_WEATHER_KEY');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit_weatherinfo_config')) {
            $hours_in_cache = (float)Tools::getValue('WEATHERINFO_HOURS_IN_CACHE');
            $api_key = Tools::getValue('WEATHERINFO_OPEN_WEATHER_KEY');

            Configuration::updateValue('WEATHERINFO_HOURS_IN_CACHE', $hours_in_cache);
            Configuration::updateValue('WEATHERINFO_OPEN_WEATHER_KEY', $api_key);

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        $output .= $this->renderForm();
        return $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Weather Info Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Hours in Cache'),
                    'name' => 'WEATHERINFO_HOURS_IN_CACHE',
                    'required' => true,
                    'desc' => $this->l('Number of hours to cache weather data (can be decimal, e.g., 1.5)'),
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('OpenWeatherMap API Key'),
                    'name' => 'WEATHERINFO_OPEN_WEATHER_KEY',
                    'required' => true,
                    'desc' => $this->l('Your OpenWeatherMap API key'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;
        $helper->title = $this->displayName;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'submit_weatherinfo_config';

        // Load current values
        $helper->fields_value['WEATHERINFO_HOURS_IN_CACHE'] = Configuration::get('WEATHERINFO_HOURS_IN_CACHE');
        $helper->fields_value['WEATHERINFO_OPEN_WEATHER_KEY'] = Configuration::get('WEATHERINFO_OPEN_WEATHER_KEY');

        return $helper->generateForm($fields_form);
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Live mode'),
                        'name' => 'WEATHERINFO_LIVE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Use this module in live mode'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter a valid email address'),
                        'name' => 'WEATHERINFO_ACCOUNT_EMAIL',
                        'label' => $this->l('Email'),
                    ),
                    array(
                        'type' => 'password',
                        'name' => 'WEATHERINFO_ACCOUNT_PASSWORD',
                        'label' => $this->l('Password'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    /**
     * Get the weather information based on the user's IP address.
     * @param string $ip The user's IP address.
     * @return array An associative array containing the weather information.
     */ 
    public function getWeatherInfo(string $ip)
    {   
        $cacheKey = 'WeatherData::getWeatherInfo_' . md5($ip);

        // Try to get data from cache
        if (Cache::isStored($cacheKey)) {
            return Cache::retrieve($cacheKey);
        } else {

            $json = file_get_contents("http://ip-api.com/json/$ip");

            // Get location from IP
            $geo = json_decode(file_get_contents("http://ip-api.com/json/$ip"), true);

            if ($geo && $geo['status'] === 'success') {

                $lat = $geo['lat'];
                $lon = $geo['lon'];

                // Get weather from OpenWeatherMap
                $apiKey = Configuration::get('WEATHERINFO_OPEN_WEATHER_KEY');
                $weatherJson = file_get_contents("https://api.openweathermap.org/data/3.0/onecall?lat=$lat&lon=$lon&appid=$apiKey&units=metric");
                $weather = json_decode($weatherJson, true);

                $weatherData = array(
                    'city' => $geo['city'],
                    'country' => $geo['country'],
                    'temp' => $weather['current']['temp'],
                    'humidity' => $weather['current']['humidity'],
                ); 

            } else {
                $weatherData = false; // If geo data is not available, return false
            }

            // Store in cache for the configured number of hours
            Cache::store($cacheKey, $weatherData, 3600 * Configuration::get('WEATHERINFO_HOURS_IN_CACHE'));

            return $weatherData;
        }
    }
}
