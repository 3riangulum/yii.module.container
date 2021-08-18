Install
---



Config Example
---
1. backend/config/params.php
```
    $config['params']['App.root'] = ROOT;

    'App.UI.sys.template_throwable' => '@backend/views/system/_exception-handler',
    'App.UI.sys.template_view'      => '@backend/views/system/_view_action',
    'App.UI.sys.template_notify'    => '@backend/views/system/_notify_action',
    'App.UI.sys.template_delete'    => '@backend/views/system/_delete_action',  
```
2. backend/config/params-local.php
```
    'App.IpWhiteList'        => [
        '127.0.0.1',
    ],
```
3. backend/config/main.php
```
    'components'        => [
        'access'    => ['class' => AccessBase::class],
        'panelGrid' => [
            'class'      => PanelGrid::class,
            'panelClass' => 'panel-default slim',
            'viewBegin'  => '@backend/views/panel/_grid_begin',
            'viewEnd'    => '@backend/views/panel/_grid_end',
        ],
        'panelBase' => [
            'class'      => PanelBase::class,
            'panelClass' => 'panel-default slim',
            'viewBegin'  => '@backend/views/panel/_popup_begin',
            'viewEnd'    => '@backend/views/panel/_popup_end',
        ],  
    ],
    'modules'           => [
        DomainModule::ID         => [
            'class'                 => DomainModule::class,
            'id'                    => DomainModule::ID,
            'moduleParentNamespace' => 'backend\modules',
        ],  
    ]  
```
