<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

final class RowCol
{
    public static function two(array $left = [], array $right = []): void
    {
        $leftElements = trim(implode(' ', $left));

        $rightElements = trim(implode(' ', $right));
        if (empty($leftElements) && empty($rightElements)) {
            return;
        }

        echo <<<HTML
<div class="padding-lateral-10 margin-5px">
    <div class="row">
        <div class="col-md-12">
            <div class="pull-left">$leftElements</div>
            <div class="pull-right">$rightElements</div>
        </div>  
    </div>
    
</div>
<hr class="hr-slim">
HTML;
    }
}
