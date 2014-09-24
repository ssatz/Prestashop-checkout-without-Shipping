<?php
/**
 * Created by PhpStorm.
 * User: Satz
 * Date: 9/23/14
 * Time: 10:27 PM
 */

class Auth extends AuthControllerCore {
    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        $this->context->smarty->assign('genders', Gender::getGenders());

        $this->assignDate();

        $this->assignCountries();

        $this->context->smarty->assign('newsletter', 1);

        $back = Tools::getValue('back');
        $key = Tools::safeOutput(Tools::getValue('key'));
        if (!empty($key))
            $back .= (strpos($back, '?') !== false ? '&' : '?').'key='.$key;
        if ($back == Tools::secureReferrer(Tools::getValue('back')))
            $this->context->smarty->assign('back', html_entity_decode($back));
        else
            $this->context->smarty->assign('back', Tools::safeOutput($back));

        if (Tools::getValue('display_guest_checkout'))
        {
            if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES'))
                $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
            else
                $countries = Country::getCountries($this->context->language->id, true);

            if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
            {
                // get all countries as language (xy) or language-country (wz-XY)
                $array = array();
                preg_match("#(?<=-)\w\w|\w\w(?!-)#", $_SERVER['HTTP_ACCEPT_LANGUAGE'], $array);
                if (!Validate::isLanguageIsoCode($array[0]) || !($sl_country = Country::getByIso($array[0])))
                    $sl_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            }
            else
                $sl_country = (int)Tools::getValue('id_country', Configuration::get('PS_COUNTRY_DEFAULT'));

            $this->context->smarty->assign(array(
                'inOrderProcess' => true,
                'PS_GUEST_CHECKOUT_ENABLED' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                'sl_country' => (int)$sl_country,
                'countries' => $countries
            ));
        }

        if (Tools::getValue('create_account'))
            $this->context->smarty->assign('email_create', 1);

        if (Tools::getValue('multi-shipping') == 1)
            $this->context->smarty->assign('multi_shipping', true);
        else
            $this->context->smarty->assign('multi_shipping', false);

        $this->assignAddressFormat();

        // Call a hook to display more information on form
        $this->context->smarty->assign(array(
            'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
            'HOOK_CREATE_ACCOUNT_TOP' => Hook::exec('displayCustomerAccountFormTop')
        ));

        // Just set $this->template value here in case it's used by Ajax
        $this->setTemplate(_PS_THEME_DIR_.'override/authentication.tpl');

        if ($this->ajax)
        {
            // Call a hook to display more information on form
            $this->context->smarty->assign(array(
                'PS_REGISTRATION_PROCESS_TYPE' => Configuration::get('PS_REGISTRATION_PROCESS_TYPE'),
                'genders' => Gender::getGenders()
            ));

            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors,
                'page' => $this->context->smarty->fetch($this->template),
                'token' => Tools::getToken(false)
            );
            die(Tools::jsonEncode($return));
        }
    }

} 