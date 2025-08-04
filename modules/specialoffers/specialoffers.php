<?php
if(!defined('_PS_VERSION_')){
    exit;
}

class SpecialOffers extends Module
{
    public function __construct()
    {
        $this->name = 'specialoffers';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'abc';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.0.0',
            'max' => '8.99.99',
        ];
        $this->bootstrap = true;
        $this->is_configurable = 1;

        parent::__construct();

        $this->displayName = $this->trans('Special offers', [], 'Modules.Specialoffers.Admin');
        $this->description = $this->trans('Description of module', [], 'Modules.Specialoffers.Admin');
        $this->confirmUninstall = $this->trans('Are you sure to uninstall?', [], 'Modules.Specialoffers.Admin');

        if(!Configuration::get('SPECIALOFFERS_NAME')){
            $this->warning = $this->trans('No name provided', [], 'Modules.Specialoffers.Admin');
        }
    }

    public function install()
    {
        return(
            parent::install()
            && $this->registerHook('displayBanner')
            && Configuration::updateValue('SPECIALOFFERS_NAME', 'Special offers')
        );
    }

    public function uninstall()
    {
        return(
            parent::uninstall()
            && Configuration::deleteByName('SPECIALOFFERS_NAME')
            );
    }
            

                

    public function hookDisplayBanner($params)
    {
        $enabled = (bool) Configuration::get('SPECIALOFFERS_ENABLE');
        if(!$enabled){
            return '';
        }
        
        $this->context->smarty->assign([
            'specialoffers_enable' => $enabled,
            'specialoffers_text_color' => Configuration::get('SPECIALOFFERS_TEXT_COLOR'),
            'specialoffers_bg_color' => Configuration::get('SPECIALOFFERS_BG_COLOR'),
            'specialoffers_text' => Configuration::get('SPECIALOFFERS_TEXT'),
        ]);
        
        return $this->display(__FILE__, 'views/templates/template.tpl');
        
        
    }
    
    

    
    public function getContent()
    {
        if(Tools::isSubmit('submit'.$this->name)){
            $enabled = Tools::getValue('SPECIALOFFERS_ENABLE');
            $textColor = Tools::getValue('SPECIALOFFERS_TEXT_COLOR');
            $bgColor = Tools::getValue('SPECIALOFFERS_BG_COLOR');
            $text = Tools::getValue('SPECIALOFFERS_TEXT');

            Configuration::updateValue('SPECIALOFFERS_ENABLE', $enabled);
            Configuration::updateValue('SPECIALOFFERS_TEXT_COLOR', $textColor);
            Configuration::updateValue('SPECIALOFFERS_BG_COLOR', $bgColor);
            Configuration::updateValue('SPECIALOFFERS_TEXT', $text, true);
        }
        return $this->displayForm();
    }

    public function displayForm()
    {
        $form_style = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Colors'),
                ],
                'input' => [
                    [ // text color
                        'type' => 'color',
                        'label' => $this->l('Text color'),
                        'name' => 'SPECIALOFFERS_TEXT_COLOR',
                    ],
                    [ // background color
                        'type' => 'color',
                        'label' => $this->l('Background color'),
                        'name' => 'SPECIALOFFERS_BG_COLOR',
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],

        ];


        $form_settings = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [ // on/off
                        'type' => 'switch',
                        'label' => $this->l('Enable module'),
                        'name' => 'SPECIALOFFERS_ENABLE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'textarea',
                        'label' => $this->l('Text to display'),
                        'name' => 'SPECIALOFFERS_TEXT',
                        'autoload_rte' => false,
                        'rows' => 10,
                        'cols' => 50,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];


        $helper = new HelperForm();

        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        $helper->fields_value['SPECIALOFFERS_ENABLE'] = 
        Tools::getValue('SPECIALOFFERS_ENABLE', Configuration::get('SPECIALOFFERS_ENABLE'));

        $helper->fields_value['SPECIALOFFERS_TEXT_COLOR'] =
        Tools::getValue('SPECIALOFFERS_TEXT_COLOR', Configuration::get('SPECIALOFFERS_TEXT_COLOR'));

        $helper->fields_value['SPECIALOFFERS_BG_COLOR'] =
        Tools::getValue('SPECIALOFFERS_BG_COLOR', Configuration::get('SPECIALOFFERS_BG_COLOR'));

        $helper->fields_value['SPECIALOFFERS_TEXT'] =
        Tools::getValue('SPECIALOFFERS_TEXT', Configuration::get('SPECIALOFFERS_TEXT'));

        return $helper->generateForm([$form_style, $form_settings]);

    }






}
