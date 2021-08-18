<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

final class Table
{
    public static function twoColumns(string $title, array $data): string
    {
        $rows = '';
        foreach ($data as $name => $val) {
            $row = <<< HTML
<tr>
    <td class="strong">$name</td>
    <td class="">$val</td>
</tr>
HTML;

            $rows .= $row;
        }

        $header = '';
        if (!empty($title)) {
            $header = Block::formSeparator($title, true);
        }

        return <<< HTML
<div class="responsive">
    $header
    <table class="table table-condensed">$rows</table>
</div>
HTML;
    }
}
