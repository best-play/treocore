<?php
/**
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2018 Zinit Solutions GmbH
 * Website: http://www.treopim.com
 *
 * TreoPIM as well as EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TreoPIM as well as EspoCRM is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with EspoCRM. If not, see http://www.gnu.org/licenses/.
 *
 * The interactive user interfaces in modified source and object code versions
 * of this program must display Appropriate Legal Notices, as required under
 * Section 5 of the GNU General Public License version 3.
 *
 * In accordance with Section 7(b) of the GNU General Public License version 3,
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word
 * and "TreoPIM" word.
 */

declare(strict_types=1);

namespace Espo\Modules\TreoCore\Services;

use Espo\Core\DataManager;
use Espo\Core\Services\Base;
use Espo\Core\Utils\Language;
use Espo\Core\Utils\File\Manager as FileManager;
use Espo\Core\Exceptions;
use Espo\Modules\TreoCore\Core\Utils\Metadata;
use Espo\Modules\TreoCore\Services\Composer as TreoComposer;

/**
 * ModuleManager service
 *
 * @author r.ratsun <r.ratsun@zinitsolutions.com>
 */
class ModuleManager extends Base
{
    /**
     * @var string
     */
    public static $gitServer = 'gitlab.zinit1.com';

    /**
     * @var string
     */
    protected $moduleJsonPath = 'custom/Espo/Custom/Resources/module.json';

    /**
     * @var array
     */
    protected $moduleRequireds = [];

    /**
     * @var string
     */
    protected $passwordSalt = '4fj-v#C&4k?H&MkC';

    /**
     * Construct
     */
    public function __construct(...$args)
    {
        parent::__construct(...$args);

        /**
         * Add dependencies
         */
        $this->addDependency('metadata');
        $this->addDependency('language');
        $this->addDependency('fileManager');
        $this->addDependency('dataManager');
        $this->addDependency('serviceFactory');
    }

    /**
     * Get composer user data
     *
     * @return array
     */
    public function getComposerUser(): array
    {
        // prepare result
        $result = [];

        // get auth data
        $authData = $this->getComposerService()->getAuthData();

        if (!empty($authData['http-basic'][self::$gitServer]) && is_array($authData['http-basic'][self::$gitServer])) {
            $result = $authData['http-basic'][self::$gitServer];
            $result['password'] = $this->passwordSalt;
        }

        return $result;
    }

    /**
     * Set composer user data
     *
     * @param string $username
     * @param string $password
     *
     * @return bool
     */
    public function setComposerUser(string $username, string $password): bool
    {
        // prepare result
        $result = false;

        if (empty($authData = $this->getComposerService()->getAuthData())
            || $password != $this->passwordSalt) {
            // prepare auth data
            $authData['http-basic'][self::$gitServer] = [
                'username' => $username,
                'password' => $password
            ];

            $result = $this->getComposerService()->setAuthData($authData);
        }

        return $result;
    }

    /**
     * Get list
     *
     * @return array
     */
    public function getList(): array
    {
        // prepare result
        $result = [
            'total' => 0,
            'list'  => []
        ];

        foreach ($this->getMetadata()->getAllModules() as $module) {
            if ($module != 'TreoCore') {
                // prepare item
                $item = [
                    "id"               => $module,
                    "name"             => $module,
                    "description"      => '',
                    "version"          => '-',
                    "availableVersion" => '-',
                    "required"         => [],
                    "isActive"         => $this->getMetadata()->isModuleActive($module),
                    "isSystem"         => false,
                    "isComposer"       => false
                ];

                // get current module package
                $package = $this->getComposerModuleService()->getModulePackage($module);

                if (!empty($package)) {
                    // get module packages
                    $packages = $this->getComposerModuleService()->getModulePackages($module);

                    // prepare item
                    $item['name'] = $this->translateModule($module, 'name');
                    $item['description'] = $this->translateModule($module, 'description');
                    $item['version'] = $this->prepareModuleVersion($package['version']);
                    $item['required'] = $this->getModuleRequireds($module);
                    $item['isSystem'] = !empty($this->getModuleConfigData("{$module}.isSystem"));
                    $item['isComposer'] = true;

                    if (isset($packages)) {
                        $item['availableVersion'] = $this->prepareModuleVersion($packages['version']);
                    }
                }

                // push
                $result['list'][] = $item;
            }
        }

        $result['total'] = count($result['list']);

        // sorting
        usort($result['list'], [$this, 'moduleListSort']);

        return $result;
    }

    /**
     * Get available modules for install
     *
     * @return array
     */
    public function getAvailableModulesList(): array
    {
        // prepare result
        $result = [
            'total' => 0,
            'list'  => []
        ];

        if (!empty($modules = $this->getComposerModuleService()->getModulePackages())) {
            // get current language
            $currentLang = $this->getLanguage()->getLanguage();

            foreach ($modules as $moduleId => $max) {
                if (empty($this->getComposerModuleService()->getModulePackage($moduleId))) {
                    // prepare name
                    $name = $moduleId;
                    if (!empty($max['extra']['name'][$currentLang])) {
                        $name = $max['extra']['name'][$currentLang];
                    } elseif ($max['extra']['name']['default']) {
                        $name = $max['extra']['name']['default'];
                    }

                    // prepare description
                    $description = '-';
                    if (!empty($max['extra']['description'][$currentLang])) {
                        $description = $max['extra']['description'][$currentLang];
                    } elseif ($max['extra']['description']['default']) {
                        $description = $max['extra']['description']['default'];
                    }

                    $result['list'][] = [
                        'id'          => $moduleId,
                        'version'     => $max['version'],
                        'name'        => $name,
                        'description' => $description
                    ];
                }
            }

            // prepare total
            $result['total'] = count($result['list']);
        }

        return $result;
    }

    /**
     * Update module activation
     *
     * @param string $moduleId
     *
     * @return bool
     * @throws Exceptions\Error
     */
    public function updateActivation(string $moduleId): bool
    {
        // prepare result
        $result = false;

        if ($this->isModuleChangeable($moduleId)) {
            // get config data
            $config = $this->getModuleConfigData($moduleId);

            // drop cache
            $this->getMetadata()->dropCache();

            // write to file
            $result = $this->updateModuleFile($moduleId, empty($config['disabled']));

            // rebuild DB
            if ($result && !empty($config['disabled'])) {
                $this->getDataManager()->rebuild();
            }
        }

        return $result;
    }

    /**
     * Install module
     *
     * @param string $id
     *
     * @return array
     */
    public function installModule(string $id): array
    {
        // prepare params
        $package = $this->getComposerModuleService()->getModulePackage($id);
        $packages = $this->getComposerModuleService()->getModulePackages($id);

        // validation
        if (empty($packages) || empty($packages)) {
            throw new Exceptions\Error($this->translateError('No such module'));
        }
        if (!empty($package)) {
            throw new Exceptions\Error($this->translateError('Such module is already installed'));
        }

        // run composer
        $result = $this
            ->getComposerService()
            ->update($packages['name'], $packages['version']);

        return $result;
    }

    /**
     * Update module
     *
     * @param string $id
     * @param string $version
     *
     * @return array
     */
    public function updateModule(string $id, string $version): array
    {
        // prepare params
        $package = $this->getComposerModuleService()->getModulePackage($id);
        $packages = $this->getComposerModuleService()->getModulePackages($id);

        // validation
        if (empty($packages)) {
            throw new Exceptions\Error($this->translateError('No such module'));
        }
        if (empty($package)) {
            throw new Exceptions\Error($this->translateError('Module was not installed'));
        }
        if ($this->prepareModuleVersion($package['version']) == $version) {
            throw new Exceptions\Error($this->translateError('Such module version already installed'));
        }
        if ($this->prepareModuleVersion($packages['version']) != $version) {
            throw new Exceptions\Error($this->translateError('No such module version'));
        }

        // run composer
        $result = $this
            ->getComposerService()
            ->update($packages[$version]['name'], $version);

        return $result;
    }

    /**
     * Delete module
     *
     * @param string $id
     *
     * @return array
     */
    public function deleteModule(string $id): array
    {
        // prepare result
        $result = [];

        if ($this->isModuleChangeable($id)) {
            // prepare params
            $package = $this->getComposerModuleService()->getModulePackage($id);
            $packages = $this->getComposerModuleService()->getModulePackages($id);

            // validation
            if (empty($package) || empty($packages)) {
                throw new Exceptions\Error($this->translateError('No such module'));
            }

            // update modules file
            $this->updateModuleFile($id, true);

            // prepare modules diff
            $beforeDelete = TreoComposer::getTreoModules();

            // run composer
            $result = $this->getComposerService()->delete($packages['name']);

            if (empty($result['status'])) {
                // prepare modules diff
                $afterDelete = TreoComposer::getTreoModules();

                // delete treo dirs
                TreoComposer::deleteTreoModule(array_diff($beforeDelete, $afterDelete));
            }
        }

        return $result;
    }

    /**
     * Is module changable?
     *
     * @param string $moduleId
     *
     * @return bool
     * @throws Exceptions\Error
     */
    protected function isModuleChangeable(string $moduleId): bool
    {
        // is system module ?
        if (!empty($this->getModuleConfigData("{$moduleId}.isSystem"))) {
            throw new Exceptions\Error(
                $this
                    ->getLanguage()
                    ->translate('isSystem', 'exceptions', 'ModuleManager')
            );
        }

        // checking requireds
        if ($this->hasRequireds($moduleId)) {
            throw new Exceptions\Error(
                $this
                    ->getLanguage()
                    ->translate('hasRequiredsDelete', 'exceptions', 'ModuleManager')
            );
        }

        return true;
    }

    /**
     * Update module file
     *
     * @param string $moduleId
     * @param bool   $isDisabled
     *
     * @return bool
     */
    protected function updateModuleFile(string $moduleId, bool $isDisabled): bool
    {
        // prepare data
        $data = [];

        // reload modules
        $this->getMetadata()->init(true);

        foreach ($this->getMetadata()->getAllModules() as $module) {
            if (!in_array($module, ['Crm', 'TreoCore'])) {
                $data[$module] = [
                    'order'    => $this->createModuleLoadOrder($module),
                    'disabled' => !in_array($module, $this->getMetadata()->getModuleList())
                ];

                if ($module == $moduleId) {
                    $data[$module]['disabled'] = $isDisabled;
                }
            }
        }

        return $this->getFileManager()->putContentsJson($this->moduleJsonPath, $data);
    }

    /**
     * Create module load order
     *
     * @param string $moduleId
     *
     * @return int
     */
    protected function createModuleLoadOrder(string $moduleId): int
    {
        // prepare result
        $result = 5100;

        /**
         * For requireds
         */
        if (!empty($requireds = $this->getModuleRequireds($moduleId))) {
            foreach ($requireds as $require) {
                $requireMax = $this->createModuleLoadOrder($require);
                if ($requireMax > $result) {
                    $result = $requireMax;
                }
            }

            $result = $result + 10;
        }

        /**
         * For extends
         */
        if (!empty($extends = $this->getModuleConfigData($moduleId)['extends'])) {
            foreach ($extends as $extend) {
                $extendMax = $this->createModuleLoadOrder($extend);
                if ($extendMax > $result) {
                    $result = $extendMax;
                }
            }

            $result = $result + 10;
        }

        return $result;
    }

    /**
     * Get module requireds
     *
     * @param string $moduleId
     *
     * @return array
     */
    protected function getModuleRequireds(string $moduleId): array
    {
        if (!isset($this->moduleRequireds[$moduleId])) {
            // prepare result
            $this->moduleRequireds[$moduleId] = [];

            if (!empty($package = $this->getComposerModuleService()->getModulePackage($moduleId))) {
                if (!empty($composerRequire = $package['require']) && is_array($composerRequire)) {
                    // get treo modules
                    $treoModule = TreoComposer::getTreoModules();

                    foreach ($composerRequire as $key => $version) {
                        if (preg_match_all("/^(" . TreoComposer::TREODIR . "\/)(.*)$/", $key, $matches)) {
                            if (!empty($matches[2][0])) {
                                $this->moduleRequireds[$moduleId][] = array_flip($treoModule)[$matches[2][0]];
                            }
                        }
                    }
                }
            }
        }

        return $this->moduleRequireds[$moduleId];
    }

    /**
     * Is module has requireds
     *
     * @param string $moduleId
     *
     * @return bool
     */
    protected function hasRequireds(string $moduleId): bool
    {
        // prepare result
        $result = false;

        // get module list
        $moduleList = $this->getMetadata()->getModuleList();

        // get module requireds
        $moduleRequireds = $this->getModuleRequireds($moduleId);

        // is module requireds by another modules
        if (empty($this->getModuleConfigData("{$moduleId}.disabled"))) {
            foreach ($moduleList as $module) {
                // get config
                $config = $this->getModuleConfigData($module);

                // get module requireds
                $requireds = $this->getModuleRequireds($module);

                if (isset($requireds) && in_array($moduleId, $requireds)) {
                    // prepare result
                    $result = true;

                    break;
                }
            }
        } elseif (!empty($moduleRequireds)) {
            // is module has own requireds
            foreach ($moduleRequireds as $module) {
                if (!in_array($module, $moduleList)) {
                    // prepare result
                    $result = true;

                    break;
                }
            }
        }

        return $result;
    }

    /**
     * Translate field
     *
     * @param string $module
     * @param string $key
     *
     * @return string
     */
    protected function translateModule(string $module, string $key): string
    {
        // prepare result
        $result = '';

        // get language
        $lang = $this->getLanguage()->getLanguage();

        // get module packages
        $package = $this->getComposerModuleService()->getModulePackage($module);

        if (!empty($translate = $package['extra'][$key][$lang])) {
            $result = $translate;
        } elseif (!empty($translate = $package['extra'][$key]['default'])) {
            $result = $translate;
        }

        return $result;
    }

    /**
     * Translate error
     *
     * @param string $key
     *
     * @return string
     */
    protected function translateError(string $key): string
    {
        return $this->getLanguage()->translate($key, 'exceptions', 'ModuleManager');
    }

    /**
     * Prepare module version
     *
     * @param string $version
     *
     * @return string
     */
    protected function prepareModuleVersion(string $version): string
    {
        return str_replace('v', '', $version);
    }

    /**
     * Get module config data
     *
     * @param string $key
     *
     * @return mixed
     */
    protected function getModuleConfigData(string $key)
    {
        return $this->getMetadata()->getModuleConfigData($key);
    }

    /**
     * Get DataManager
     *
     * @return DataManager
     */
    protected function getDataManager(): DataManager
    {
        return $this->getInjection('dataManager');
    }

    /**
     * Get ComposerModule service
     *
     * @return ComposerModule
     */
    protected function getComposerModuleService(): ComposerModule
    {
        return $this->getInjection('serviceFactory')->create('ComposerModule');
    }

    /**
     * Get metadata
     *
     * @return Metadata
     */
    protected function getMetadata(): Metadata
    {
        return $this->getInjection('metadata');
    }

    /**
     * Get language
     *
     * @return Language
     */
    protected function getLanguage(): Language
    {
        return $this->getInjection('language');
    }

    /**
     * Get File Manager
     *
     * @return FileManager
     */
    protected function getFileManager(): FileManager
    {
        return $this->getInjection('fileManager');
    }

    /**
     * Get Composer service
     *
     * @return Composer
     */
    protected function getComposerService(): Composer
    {
        return $this->getInjection('serviceFactory')->create('Composer');
    }

    /**
     * Module list sort
     *
     * @param array $a
     * @param array $b
     *
     * @return int
     */
    private static function moduleListSort(array $a, array $b): int
    {
        // prepare params
        $a = $a['name'];
        $b = $b['name'];

        if ($a == $b) {
            return 0;
        }

        return ($a < $b) ? -1 : 1;
    }
}
