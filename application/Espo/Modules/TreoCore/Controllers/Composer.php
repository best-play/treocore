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

namespace Espo\Modules\TreoCore\Controllers;

use Espo\Core\Controllers\Base;
use Espo\Core\Exceptions;
use Espo\Core\Utils\Json;
use Espo\Modules\TreoCore\Services\Composer as ComposerService;
use Slim\Http\Request;

/**
 * Composer controller
 *
 * @author r.ratsun@zinitsolutions.com
 */
class Composer extends Base
{
    /**
     * @var string
     */
    protected $gitHost = 'gitlab.zinit1.com';

    /**
     * @var string
     */
    protected $authType = 'http-basic';

    /**
     * @var ComposerService
     */
    protected $composerService = null;

    /**
     * @ApiDescription(description="Get git auth data")
     * @ApiMethod(type="GET")
     * @ApiRoute(name="/Composer/gitAuth")
     * @ApiReturn(sample="{
     *     'username': 'test',
     *     'password': 'qwerty'
     * }")
     *
     * @return array
     * @throws Exceptions\Forbidden
     * @throws Exceptions\BadRequest
     * @throws Exceptions\NotFound
     */
    public function actionGetGitAuthData($params, $data, Request $request): array
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Exceptions\Forbidden();
        }

        if (!$request->isGet()) {
            throw new Exceptions\BadRequest();
        }

        // prepare result
        $result = [];

        // get auth data
        $authData = $this->getComposerService()->getAuthData();

        // prepare result
        if (!empty($authData[$this->authType][$this->gitHost])) {
            $result = $authData[$this->authType][$this->gitHost];
        }

        return $result;
    }

    /**
     * @ApiDescription(description="Set git auth data")
     * @ApiMethod(type="PUT")
     * @ApiRoute(name="/Composer/gitAuth")
     * @ApiBody(sample="{
     *     'username': 'test',
     *     'password': 'qwerty'
     * }")
     * @ApiReturn(sample="true")
     *
     * @return bool
     * @throws Exceptions\Forbidden
     * @throws Exceptions\BadRequest
     * @throws Exceptions\NotFound
     */
    public function actionSetGitAuthData($params, $data, Request $request): bool
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Exceptions\Forbidden();
        }

        if (!$request->isPut()) {
            throw new Exceptions\BadRequest();
        }
        // prepare data
        $data = Json::decode(Json::encode($data), true);

        if (!empty($data['username']) && !empty($data['password'])) {
            // get auth data
            $authData = $this->getComposerService()->getAuthData();
            $authData[$this->authType][$this->gitHost]['username'] = $data['username'];
            $authData[$this->authType][$this->gitHost]['password'] = $data['password'];

            return $this->getComposerService()->setAuthData($authData);
        }

        throw new Exceptions\NotFound();
    }

    /**
     * @ApiDescription(description="Call composer update command")
     * @ApiMethod(type="POST")
     * @ApiRoute(name="/Composer/update")
     * @ApiReturn(sample="{
     *     'status': 'true',
     *     'output': 'some text from composer'
     * }")
     *
     * @return array
     * @throws Exceptions\Forbidden
     * @throws Exceptions\BadRequest
     * @throws Exceptions\NotFound
     */
    public function actionUpdate($params, $data, Request $request): array
    {
        if (!$this->getUser()->isAdmin()) {
            throw new Exceptions\Forbidden();
        }

        if (!$request->isPost()) {
            throw new Exceptions\BadRequest();
        }

        return $this->getComposerService()->runUpdate();
    }

    /**
     * @return ComposerService
     */
    protected function getComposerService(): ComposerService
    {
        if (is_null($this->composerService)) {
            $this->composerService = $this->getService('Composer');
        }

        return $this->composerService;
    }
}
