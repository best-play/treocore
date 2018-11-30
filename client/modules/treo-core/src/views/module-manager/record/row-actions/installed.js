/*
 * This file is part of EspoCRM and/or TreoPIM.
 *
 * EspoCRM - Open Source CRM application.
 * Copyright (C) 2014-2018 Yuri Kuznetsov, Taras Machyshyn, Oleksiy Avramenko
 * Website: http://www.espocrm.com
 *
 * TreoPIM is EspoCRM-based Open Source Product Information Management application.
 * Copyright (C) 2017-2018 TreoLabs GmbH
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

Espo.define('treo-core:views/module-manager/record/row-actions/installed', 'views/record/row-actions/default',
    Dep => Dep.extend({

        disableActions: false,

        setup() {
            Dep.prototype.setup.call(this);

            this.listenTo(this.model.collection, 'disableActions', (disableActions) => {
                this.disableActions = disableActions;
                this.reRender();
            });
        },

        getActionList() {
            let list = [];
            if (!this.disableActions) {
                if (this.model.get('isComposer')) {
                    if (!this.model.get('status')) {
                        let versions = this.model.get('versions');
                        if (versions && versions.length) {
                            list.push({
                                action: 'installModule',
                                label: 'updateModule',
                                data: {
                                    id: this.model.id,
                                    mode: 'update'
                                }
                            });
                        }
                        let checkRequire = this.model.collection.every(model => !(model.get('required') || []).includes(this.model.get('id')));
                        if (checkRequire && !this.model.get('isSystem')) {
                            list.push({
                                action: 'removeModule',
                                label: 'removeModule',
                                data: {
                                    id: this.model.id
                                }
                            });
                        }
                    } else {
                        list.push({
                            action: 'cancelModule',
                            label: 'cancelModule',
                            data: {
                                id: this.model.id,
                                status: this.model.get('status')
                            }
                        });
                    }
                }
            }
            return list;
        },

    })
);
