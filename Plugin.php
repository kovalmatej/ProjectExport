<?php

namespace Kanboard\Plugin\ProjectExport;

use Kanboard\Core\Plugin\Base;
use Kanboard\Core\Translator;

class Plugin extends Base
{
    public function initialize()
    {
    }

    public function onStartup()
    {
        Translator::load($this->languageModel->getCurrentLanguage(), __DIR__.'/Locale');
    }

    public function getPluginName()
    {
        return 'ProjectExport';
    }

    public function getPluginDescription()
    {
        return t('Add option to export chosen data from project in form of HTML table.');
    }

    public function getPluginAuthor()
    {
        return 'Matej Kovaľ';
    }

    public function getPluginVersion()
    {
        return '1.0.0';
    }

    public function getPluginHomepage()
    {
        return 'https://github.com/kovalmatej/ProjectExport';
    }
}

