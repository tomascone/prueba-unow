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

class ProductImporter extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'productimporter';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Tomas Carrasco';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Import Products from CSV');
        $this->description = $this->l('Module that allows you to import products from a CSV file.');

        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install();
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    /**
     * Get the content of the module configuration page.
     *
     * @return string HTML content for the module configuration page.
     */
    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submit_productimporter')) {
            if (isset($_FILES['import_csv']) && $_FILES['import_csv']['error'] == 0) {
                $csv = array_map('str_getcsv', file($_FILES['import_csv']['tmp_name']));
                $header = array_shift($csv);
                foreach ($csv as $row) {
                    $data = array_combine($header, $row);

                    //Create a new product instance
                    $product = new Product();
                    $product->name = [Configuration::get('PS_LANG_DEFAULT') => $data['Nombre']];
                    $product->reference = $data['Referencia'];
                    $product->ean13 = $data['EAN13'];
                    $product->wholesale_price = $data['Precio de coste'];
                    $product->price = $data['Precio de venta'];
                    $product->quantity = $data['Cantidad'];

                    $categories = explode(';', $data['Categorias']);

                    // Search or create the categories
                    $categories_ids = $this->ensureCategoriesExists(Configuration::get('PS_LANG_DEFAULT'), $categories, Configuration::get('PS_HOME_CATEGORY'));
                    $product->id_category_default = $categories_ids[0]; // Assign the first category as default

                    // Get or create the manufacturer
                    $id_manufacturer = Manufacturer::getIdByName($data['Marca']);
                    if (!$id_manufacturer) {
                        $manufacturer = new Manufacturer();
                        $manufacturer->name = $data['Marca'];
                        $manufacturer->active = 1;
                        $manufacturer->add();
                        $id_manufacturer = $manufacturer->id;
                    }

                    // Assign the manufacturer to the product
                    $product->id_manufacturer = $id_manufacturer;

                    $product->link_rewrite = [Configuration::get('PS_LANG_DEFAULT') => Tools::str2url($data['Nombre'])];
                    $product->save();
                    
                    // Assign categories to the product
                    $product->updateCategories($categories_ids);
                    
                    // Assign stock to the product
                    StockAvailable::setQuantity($product->id, 0, $data['Cantidad']);
                }
                $output .= $this->displayConfirmation('Products imported successfully.');
            } else {
                $output .= $this->displayError('Please upload a valid CSV file.');
            }
        }
        $output .= $this->renderForm();
        return $output;
    }

    /**
     * Create the form for the module configuration.
     */
    protected function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form[0]['form'] = [
            'legend' => [
                'title' => $this->l('Import Products'),
            ],
            'input' => [
                [
                    'type' => 'file',
                    'label' => $this->l('CSV File'),
                    'name' => 'import_csv',
                    'desc' => $this->l('Upload a CSV file with columns: name,reference,ean13,wholesale price,price,tax rate,quantity,categories,brand'),
                ],
            ],
            'submit' => [
                'title' => $this->l('Import'),
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
        $helper->submit_action = 'submit_productimporter';

        return $helper->generateForm($fields_form);
    }

    /**
     * Ensures the existence of categories in the database, creating them if they do not exist.
     *
     * @param int $idLang Language ID
     * @param array $categoriesNames Array of category names to ensure existence
     * @param int $defaultParent Default parent category ID if a new category is created
     * @return array Array of category IDs for the given names
     */
    public function ensureCategoriesExists($idLang, array $categoriesNames, $defaultParent)
    {
        // Search for existing categories by name
        $escaped_names = array_map('pSQL', $categoriesNames);
        $name_list = "'" . implode("','", $escaped_names) . "'";
        $sql = new DbQuery();
        $sql->select('cl.name, c.id_category');
        $sql->from('category', 'c');
        $sql->leftJoin('category_lang', 'cl', 'c.id_category = cl.id_category AND cl.id_lang = ' . (int)$idLang);
        $sql->where('cl.name IN (' . $name_list . ')');
        $existing = Db::getInstance()->executeS($sql);
        
        $result = [];
        
        // Map existing categories to their IDs
        foreach ($existing as $row) {
            $result[$row['name']] = (int)$row['id_category'];
        }

        // Create categories that do not exist
        foreach ($categoriesNames as $name) {
            if (!isset($result[$name])) {
                $category = new Category();
                $category->name = [$idLang => $name];
                $category->link_rewrite = [$idLang => Tools::str2url($name)];
                $category->id_parent = $defaultParent;
                $category->active = 1;
                $category->add();
                $result[$name] = (int)$category->id;
            }
        }
        return array_values($result);
    }
}
