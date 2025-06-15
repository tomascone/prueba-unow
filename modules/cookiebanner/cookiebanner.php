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

class Cookiebanner extends Module
{
    protected $config_form = false;

    const PREFIX = 'COOKIEBANNER_BANNER_';

    public function __construct()
    {
        $this->name = 'cookiebanner';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Tomas Carrasco';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cookie banner');
        $this->description = $this->l('Module that allows to show and personalize a cookie banner');

        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue(self::PREFIX . 'TEXT', '');
        Configuration::updateValue(self::PREFIX . 'ACCEPT_TEXT', '');
        Configuration::updateValue(self::PREFIX . 'DECLINE_TEXT', '');
        Configuration::updateValue(self::PREFIX . 'BG', '');
        Configuration::updateValue(self::PREFIX . 'TEXT_COLOR', '');
        Configuration::updateValue(self::PREFIX . 'ACCEPT_TEXT_COLOR', '');
        Configuration::updateValue(self::PREFIX . 'DECLINE_TEXT_COLOR', '');
        Configuration::updateValue(self::PREFIX . 'BTN_COLOR', '');
        Configuration::updateValue(self::PREFIX . 'POSITION', '');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        Configuration::deleteByName(self::PREFIX . 'TEXT');
        Configuration::deleteByName(self::PREFIX . 'ACCEPT_TEXT');
        Configuration::deleteByName(self::PREFIX . 'DECLINE_TEXT');
        Configuration::deleteByName(self::PREFIX . 'BG');
        Configuration::deleteByName(self::PREFIX . 'TEXT_COLOR');
        Configuration::deleteByName(self::PREFIX . 'ACCEPT_TEXT_COLOR');
        Configuration::deleteByName(self::PREFIX . 'DECLINE_TEXT_COLOR');
        Configuration::deleteByName(self::PREFIX . 'BTN_COLOR');
        Configuration::deleteByName(self::PREFIX . 'POSITION');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submit_cookiebanner_config')) {
            Configuration::updateValue(self::PREFIX . 'TEXT', Tools::getValue(self::PREFIX . 'TEXT'));
            Configuration::updateValue(self::PREFIX . 'ACCEPT_TEXT', Tools::getValue(self::PREFIX . 'ACCEPT_TEXT'));
            Configuration::updateValue(self::PREFIX . 'DECLINE_TEXT', Tools::getValue(self::PREFIX . 'DECLINE_TEXT'));
            Configuration::updateValue(self::PREFIX . 'BG', Tools::getValue(self::PREFIX . 'BG'));
            Configuration::updateValue(self::PREFIX . 'TEXT_COLOR', Tools::getValue(self::PREFIX . 'TEXT_COLOR'));
            Configuration::updateValue(self::PREFIX . 'ACCEPT_TEXT_COLOR', Tools::getValue(self::PREFIX . 'ACCEPT_TEXT_COLOR'));
            Configuration::updateValue(self::PREFIX . 'DECLINE_TEXT_COLOR', Tools::getValue(self::PREFIX . 'DECLINE_TEXT_COLOR'));
            Configuration::updateValue(self::PREFIX . 'BTN_COLOR', Tools::getValue(self::PREFIX . 'BTN_COLOR'));
            Configuration::updateValue(self::PREFIX . 'POSITION', Tools::getValue(self::PREFIX . 'POSITION'));

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        $output .= $this->renderForm();
        return $output;
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    public function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Banner Settings'),
            ],
            'input' => [
                [
                    'type' => 'textarea',
                    'label' => $this->l('Banner Text'),
                    'name' => self::PREFIX . 'TEXT',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Accept Button Text'),
                    'name' => self::PREFIX . 'ACCEPT_TEXT',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Decline Button Text'),
                    'name' => self::PREFIX . 'DECLINE_TEXT',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Background Color'),
                    'name' => self::PREFIX . 'BG',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Text Color'),
                    'name' => self::PREFIX . 'TEXT_COLOR',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Accept Button Color'),
                    'name' => self::PREFIX . 'ACCEPT_TEXT_COLOR',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Decline Button Color'),
                    'name' => self::PREFIX . 'DECLINE_TEXT_COLOR',
                    'required' => true,
                ],
                [
                    'type' => 'color',
                    'label' => $this->l('Button Color'),
                    'name' => self::PREFIX . 'BTN_COLOR',
                    'required' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->l('Banner Position'),
                    'name' => self::PREFIX . 'POSITION',
                    'options' => [
                        'query' => [
                            ['id' => 'top', 'name' => $this->l('Top')],
                            ['id' => 'bottom', 'name' => $this->l('Bottom')],
                            ['id' => 'popup', 'name' => $this->l('Popup')],
                        ],
                        'id' => 'id',
                        'name' => 'name'
                    ],
                    'required' => true,
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
        $helper->submit_action = 'submit_cookiebanner_config';

        // Load current values
        $helper->fields_value[self::PREFIX . 'TEXT'] = Configuration::get(self::PREFIX . 'TEXT');
        $helper->fields_value[self::PREFIX . 'ACCEPT_TEXT'] = Configuration::get(self::PREFIX . 'ACCEPT_TEXT');
        $helper->fields_value[self::PREFIX . 'DECLINE_TEXT'] = Configuration::get(self::PREFIX . 'DECLINE_TEXT');
        $helper->fields_value[self::PREFIX . 'BG'] = Configuration::get(self::PREFIX . 'BG');
        $helper->fields_value[self::PREFIX . 'TEXT_COLOR'] = Configuration::get(self::PREFIX . 'TEXT_COLOR');
        $helper->fields_value[self::PREFIX . 'ACCEPT_TEXT_COLOR'] = Configuration::get(self::PREFIX . 'ACCEPT_TEXT_COLOR');
        $helper->fields_value[self::PREFIX . 'DECLINE_TEXT_COLOR'] = Configuration::get(self::PREFIX . 'DECLINE_TEXT_COLOR');
        $helper->fields_value[self::PREFIX . 'BTN_COLOR'] = Configuration::get(self::PREFIX . 'BTN_COLOR');
        $helper->fields_value[self::PREFIX . 'POSITION'] = Configuration::get(self::PREFIX . 'POSITION');

        return $helper->generateForm($fields_form);
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}
