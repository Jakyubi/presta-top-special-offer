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
    
    
    public function installDb(){ // TODO

    }
    
    public function getContent()
    {
        if(Tools::isSubmit('submitSettingsForm')){
            $text = Tools::getValue('SPECIALOFFERS_TEXT');
            $enabled = Tools::getValue('SPECIALOFFERS_ENABLE');
            Configuration::updateValue('SPECIALOFFERS_TEXT', $text, true);
            Configuration::updateValue('SPECIALOFFERS_ENABLE', $enabled);
        }

        if(Tools::isSubmit('submitStyleForm')){
            $textColor = Tools::getValue('SPECIALOFFERS_TEXT_COLOR');
            $bgColor = Tools::getValue('SPECIALOFFERS_BG_COLOR');

            Configuration::updateValue('SPECIALOFFERS_TEXT_COLOR', $textColor);
            Configuration::updateValue('SPECIALOFFERS_BG_COLOR', $bgColor);
        }

        $active_tab = 'settings';
        if (Tools::isSubmit('submitStyleForm')) {
            $active_tab = 'style';
        }

        $content = // WORK IN PROGRESS
            '
            <ul class="nav nav-tabs" role="tablist">
                <li class="'.($active_tab == 'settings' ? 'active' : '').'">
                    <a href="#tab-settings" data-toggle="tab">'.$this->l('Settings').'</a>
                </li>
                <li class="'.($active_tab == 'style' ? 'active' : '').'">
                    <a href="#tab-style" data-toggle="tab">'.$this->l('Style').'</a>
                </li>
            </ul>
            <div class="tab-content" >
                <div class="tab-pane '.($active_tab == 'settings' ? 'active' : '').'" id="tab-settings">
                    '.$this->displaySettingsForm().'
                </div>
                <div class="tab-pane '.($active_tab == 'style' ? 'active' : '').'" id="tab-style">
                    '.$this->displayStyleForm().'
                </div>
            </div>';

        return $content;

    }


    public function displaySettingsForm()
    {
        $form = [
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
                    [ // text input
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

        $helper = $this->getHelper();
        $helper->submit_action = 'submitSettingsForm';
        
        $helper->fields_value['SPECIALOFFERS_ENABLE'] = 
        Tools::getValue('SPECIALOFFERS_ENABLE', Configuration::get('SPECIALOFFERS_ENABLE'));


        $helper->fields_value['SPECIALOFFERS_TEXT'] =
        Tools::getValue('SPECIALOFFERS_TEXT', Configuration::get('SPECIALOFFERS_TEXT'));

        return $helper->generateForm([$form]);

    }

    public function displayStyleForm()
    {
        $form = [
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

        $helper = $this->getHelper();
        $helper->submit_action = 'submitStyleForm';

        $helper->fields_value['SPECIALOFFERS_TEXT_COLOR'] =
        Tools::getValue('SPECIALOFFERS_TEXT_COLOR', Configuration::get('SPECIALOFFERS_TEXT_COLOR'));

        $helper->fields_value['SPECIALOFFERS_BG_COLOR'] =
        Tools::getValue('SPECIALOFFERS_BG_COLOR', Configuration::get('SPECIALOFFERS_BG_COLOR'));

        return $helper->generateForm([$form]);
    }

    public function getHelper()
    {
        $helper = new HelperForm();
        //$helper->module = $this;
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');
        
        return $helper;
    }

    
    
    
    


}




