{{#each panelList}}
    <div class="panel panel-{{#if style}}{{style}}{{else}}default{{/if}} panel-{{name}}{{#if hidden}} hidden{{/if}}{{#if sticked}} sticked{{/if}}" data-name="{{name}}">
        <div class="panel-heading">
            <div class="pull-right btn-group">
                {{#unless notRefreshable}}
                <button type="button" class="btn btn-default btn-sm action" data-action="refresh" data-panel="{{name}}" title="{{translate 'clickToRefresh' category='messages'}}">
                    <span class="fas fa-sync"></span>
                </button>
                {{/unless}}
                {{#if buttonList}}
                    {{#each buttonList}}
                    <button type="button" class="btn btn-{{#if ../../style}}{{../../style}}{{else}}default{{/if}} btn-sm action{{#if hidden}} hidden{{/if}}" data-action="{{action}}" data-panel="{{../../name}}" {{#each data}} data-{{hyphen @key}}="{{./this}}"{{/each}} title="{{#if title}}{{translate title scope=../../scope}}{{/if}}">{{#if html}}{{{html}}}{{else}}{{translate label scope=../../../../scope}}{{/if}}</button>
                    {{/each}}
                {{/if}}
                {{#if actionList}}
                    <button type="button" class="btn btn-{{#if ../style}}{{../style}}{{else}}default{{/if}} btn-sm dropdown-toggle" data-toggle="dropdown">
                        <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        {{#each actionList}}
                        {{#if this}}
                        <li><a {{#if link}}href="{{link}}"{{else}}href="javascript:"{{/if}} class="action{{#if hidden}} hidden{{/if}}" data-panel="{{../../../name}}" {{#if action}} data-action={{action}}{{/if}}{{#each data}} data-{{hyphen @key}}="{{./this}}"{{/each}}>{{#if html}}{{{html}}}{{else}}{{translate label scope=../../../../../scope}}{{/if}}</a></li>
                        {{else}}
                        <li class="divider"></li>
                        {{/if}}
                        {{/each}}
                    </ul>
                {{/if}}
            </div>
            <h4 class="panel-title">
            {{#unless notRefreshable}}
            <span style="cursor: pointer;" class="action" title="{{translate 'clickToRefresh' category='messages'}}" data-action="refresh" data-panel="{{name}}">
            {{/unless}}
            {{#if titleHtml}}
                {{{titleHtml}}}
            {{else}}
                {{title}}
            {{/if}}
            {{#unless notRefreshable}}
            </span>
            <span class="collapser glyphicon {{#if expanded}}glyphicon-menu-up{{else}}glyphicon-menu-down{{/if}}" data-action="collapsePanel" data-panel="{{name}}"></span>
            {{/unless}}
            </h4>
        </div>
        <div class="panel-body{{#if isForm}} panel-body-form{{/if}} panel-collapse collapse {{#if expanded}}in{{/if}}" data-name="{{name}}">
            {{{var name ../this}}}
        </div>
    </div>
{{/each}}
