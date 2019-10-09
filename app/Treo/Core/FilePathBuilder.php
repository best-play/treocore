<?php
/************************************************************************
 * This file is part of EspoCRM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * EspoCRM is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * EspoCRM is distributed in the hope that it will be useful,
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
 * these Appropriate Legal Notices must retain the display of the "EspoCRM" word.
 ************************************************************************/

declare(strict_types=1);

namespace Treo\Core;

use Espo\Core\Exceptions\Error;
use Espo\ORM\Metadata;
use Treo\Core\FileStorage\Storages\UploadDir;
use Treo\Core\Utils\File\Manager;
use Treo\Core\Utils\Random;
use Treo\Core\Utils\Util;

/**
 * Class FilePathBuilder
 * @package Treo\Core
 */
class FilePathBuilder
{
    const UPLOAD = 'upload';
    const LAST_CREATED = "lastCreated";

    /**
     * @var Container
     */
    protected $container;

    /**
     * DAMFilePathBuilder constructor.
     *
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return array
     */
    public static function folderPath()
    {
        return [
            "upload" => UploadDir::BASE_PATH,
        ];
    }

    /**
     * @param string $type
     * @param string|null $route
     * @return string
     * @throws Error
     *
     * This method use in migration DAM V3.21.0
     */
    public function createPath(string $type, ?string $route = null): string
    {
        $baseFolder = static::folderPath()[$type];
        $lastPath = $this->getFromFile($baseFolder, $route);

        if (!$lastPath) {
            $path = $this->init($type);
        } else {
            $path = $this->buildPath($type, $lastPath, $route);
        }
        $res = implode("/", $path);
        array_pop($path);
        $this->saveInFile($baseFolder, implode('/', $path), $route);

        return $res;
    }

    /**
     * @param string $baseFolder
     * @param null $type
     * @return string|null
     *
     */
    protected function getFromFile(string $baseFolder, ?string $type = null)
    {
        if (!file_exists($baseFolder . self::LAST_CREATED)) {
            return null;
        }

        $content = json_decode(file_get_contents($baseFolder . self::LAST_CREATED), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }

        if ($type) {
            return $content[$type] ?? null;
        }

        return $content;
    }

    /**
     * @param string      $baseFolder
     * @param string      $path
     * @param string|null $type
     * @return bool
     * @throws Error
     */
    protected function saveInFile(string $baseFolder, string $path, ?string $type = null)
    {
        $data = $this->getFromFile($baseFolder);

        if ($type) {
            $data[$type] = $path;
        } else {
            $data = $path;
        }

        return $this
            ->getFileManager()
            ->putContents($baseFolder . self::LAST_CREATED, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * @param string      $type
     * @param string      $path
     * @param string|null $subType
     * @return array
     * @throws Error
     */
    protected function buildPath(string $type, string $path, ?string $subType)
    {
        $basePath = static::folderPath()[$type];
        $folderInfo = $this->getMeta()->get(['app', 'fileStorage', $type]);
        $iter = 0;
        $backIter = 0;

        $basePath = $this->getPath($basePath, $subType);

        if (empty($path)) {
            throw new Error();
        }

        $pathEl = explode("/", $path);

        while (!$iter) {
            $count = Util::countItems($basePath . implode("/", $pathEl));

            if ($count < $folderInfo['maxFilesInFolder']) {
                $iter = $folderInfo['folderDepth'] - $backIter;
                break;
            }
            if (!$pathEl) {
                throw new Error("Folder limit");
            }
            array_pop($pathEl);

            $backIter++;
        }

        for ($iter; $iter <= $folderInfo['folderDepth']; $iter++) {
            while ($iter) {
                $folderName = Random::getString($folderInfo['folderNameLength']);
                if (is_dir($basePath . implode("/", $pathEl) . "/" . $folderName)) {
                    continue;
                }
                $pathEl[] = $folderName;
                break;
            }
        }


        return $pathEl;
    }

    /**
     * @param $path
     * @param $folder
     * @return string
     */
    protected function getPath($path, $folder)
    {
        if (!$folder) {
            return realpath($path) . "/";
        }

        return realpath($path) . "/{$folder}/";
    }

    /**
     * @param string $type
     * @return array
     */
    protected function init(string $type): array
    {
        $depth = $this->getMeta()->get(['app', 'fileStorage', $type, 'folderDepth']) ?? 3;
        $folderNameLength = $this->getMeta()->get(['app', 'fileStorage', $type, 'folderNameLength']) ?? 3;
        $path = [];

        for ($i = 1; $i < $depth; $i++) {
            $folderName = Random::getString($folderNameLength);
            $path[] = $folderName;
        }
        $path[] = Random::getString($folderNameLength);

        return $path;
    }

    /**
     * @return Metadata|null
     */
    protected function getMeta()
    {
        return $this->container->get('metadata');
    }

    /**
     * @return Manager
     */
    protected function getFileManager(): Manager
    {
        return $this->container->get("fileManager");
    }
}
