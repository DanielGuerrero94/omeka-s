<?php
namespace Omeka\Installation\Task;

use Omeka\Installation\Installer;
use Omeka\Module;
use Omeka\Service\Paginator;

class AddDefaultSettingsTask implements TaskInterface
{
    protected $defaultSettings = [
        'version' => Module::VERSION,
        'pagination_per_page' => Paginator::PER_PAGE,
    ];

    public function perform(Installer $installer)
    {
        $vars = $installer->getVars('Omeka\Installation\Task\AddDefaultSettingsTask');
        $this->defaultSettings['administrator_email'] = $vars['administrator_email'];
        $this->defaultSettings['installation_title'] = $vars['installation_title'];
        $this->defaultSettings['time_zone'] = $vars['time_zone'];

        $settings = $installer->getServiceLocator()->get('Omeka\Settings');
        foreach ($this->defaultSettings as $id => $value) {
            $settings->set($id, $value);
        }
    }
}
