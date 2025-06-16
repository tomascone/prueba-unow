<?php


class CookieBannerSaveCookieModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $this->context->cookie->cookie_consent = Tools::getValue('consent');

        die(json_encode(['success' => true]));
    }
}