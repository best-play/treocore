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

namespace Espo\Modules\TreoCore\Console;

use Espo\Modules\TreoCore\Traits\ContainerTrait;
use Espo\Modules\TreoCore\Core\Utils\Config;
use Espo\Modules\TreoCore\Core\Utils\Metadata;

/**
 * AbtractConsole class
 *
 * @author r.ratsun@zinitsolutions.com
 */
abstract class AbstractConsole
{
    use ContainerTrait;

    /**
     * Run action
     *
     * @param array $data
     */
    abstract public function run(array $data): void;

    /**
     * Get console command description
     *
     * @return string
     */
    abstract public static function getDescription(): string;

    /**
     * Echo CLI message
     *
     * @param string $message
     * @param int    $status
     * @param bool   $stop
     */
    public static function show(string $message, int $status = 0, bool $stop = false): void
    {
        switch ($status) {
            // success
            case 1:
                echo "\033[0;32m{$message}\033[0m" . PHP_EOL;
                break;
            // error
            case 2:
                echo "\033[1;31m{$message}\033[0m" . PHP_EOL;
                break;
            // info
            case 3:
                echo "\033[0;36m{$message}\033[0m" . PHP_EOL;
                break;
            // default
            default:
                echo $message . PHP_EOL;
                break;
        }

        if ($stop) {
            die();
        }
    }

    /**
     * Array to table
     *
     * @param array $data
     * @param array $header
     *
     * @return string
     */
    public static function ArrayToTable(array $data, array $header = []): string
    {
        // prepare data
        $data = array_merge([$header], $data);
        foreach ($data as $row_key => $row) {
            $isHeader = (!empty($header) && $row_key == 0);
            foreach ($row as $cell_key => $cell) {
                // prepare color
                if ($isHeader) {
                    $color = '0;31';
                } else {
                    $color = (!empty($cell_key % 2)) ? '0;37' : '0;32';
                }

                // inject breaklines and color
                $data[$row_key][$cell_key] = '| ' . "\033[{$color}m{$cell}\033[0m";
            }
            $data[$row_key][] = '|';
        }

        // Find longest string in each column
        $columns = [];
        foreach ($data as $row_key => $row) {
            foreach ($row as $cell_key => $cell) {
                $length = strlen($cell);
                if (empty($columns[$cell_key]) || $columns[$cell_key] < $length) {
                    $columns[$cell_key] = $length;
                }
            }
        }

        // Output table, padding columns
        $table = '';
        foreach ($data as $row_key => $row) {
            foreach ($row as $cell_key => $cell) {
                $table .= str_pad($cell, $columns[$cell_key]) . '   ';
            }
            $table .= PHP_EOL;
        }

        return $table;
    }

    /**
     * Get config
     *
     * @return Config
     */
    protected function getConfig(): Config
    {
        return $this->getContainer()->get('config');
    }

    /**
     * Get metadata
     *
     * @return Metadata
     */
    protected function getMetadata(): Metadata
    {
        return $this->getContainer()->get('metadata');
    }

    /**
     * Get translated message
     *
     * @param string $label
     * @param string $category
     * @param string $scope
     * @param null   $requiredOptions
     *
     * @return string
     */
    protected function translate(string $label, string $category, string $scope, $requiredOptions = null): string
    {
        return $this->getContainer()->get('language')->translate($label, $category, $scope, $requiredOptions);
    }
}
