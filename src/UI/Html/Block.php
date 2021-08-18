<?php

namespace Triangulum\Yii\ModuleContainer\UI\Html;

use Throwable;
use Yii;
use yii\helpers\Html;

final class Block
{
    public static function formSeparator(
        $name = '',
        $fat = false,
        $icon = null,
        string $class = '',
        bool $doBr = false
    ): string {
        $elClass = $fat ? ' fat' : '';
        $elClass .= $class ? ' ' . $class : '';
        $br = $doBr ? '<br>' : '';

        return <<<HTML
<div class="form-separator $elClass"><span>$icon $name</span></div>$br
HTML;
    }

    public static function error(string $msg): string
    {
        $data = <<<EOT
        
<div class="alert alert-danger" role="alert">
    <div class="white-border text-center">
        <h4>$msg</h4>
    </div>
</div>

EOT;

        return $data;
    }

    public static function success(string $msg): string
    {
        $data = <<<EOT
        
<div class="alert alert-success" role="alert">
    <div class="white-border text-center">
        <h4>$msg</h4>
    </div>
</div>

EOT;

        return $data;
    }

    public static function displayError(Throwable $t)
    {
        Yii::error([
            $t->getMessage(),
            $t->getTraceAsString(),
        ]);

        $msg = self::hideRootPath($t->getMessage());

        $template =
            <<< EOD
            <br>
           <div class="alert alert-danger" role="alert">
                <div class="white-border">
                    <h4>$msg</h4>
                </div>
            </div>
EOD;

        echo $template;
    }

    public static function showErrorList(string $title = 'Error', array $messageList = []): string
    {
        $errTitle = Html::encode($title);
        $messageContent = '';
        foreach ($messageList as $msg) {
            $messageContent .= '<h4>' . Html::encode(self::hideRootPath($msg)) . '</h4>';
        }

        return <<<HTML
<div class="alert alert-danger background-red-dark" role="alert">
    <div class="white-border text-center color-white">
        <h3>$errTitle</h3>
        $messageContent
    </div>
</div>

HTML;
    }

    public static function showSuccessList(string $title = 'Success', array $messageList = []): string
    {
        $okTitle = Html::encode($title);
        $messageContent = '';
        foreach ($messageList as $msg) {
            $messageContent .= '<h4>' . Html::encode(self::hideRootPath($msg)) . '</h4>';
        }

        return <<<HTML
<div class="alert alert-success background-green" role="alert">
    <div class="white-border text-center">
        <h3>$okTitle</h3>
        $messageContent
    </div>
</div>

HTML;
    }

    public static function notice(string $msg): string
    {
        $data = <<<EOT
<div class="alert alert-warning" role="alert">
    <div class="alert-warning-border text-center">
        <h5>$msg</h5>
    </div>
</div>
EOT;

        return $data;
    }

    public static function groupAddonRight(string $addon): string
    {
        return <<<HTML
{label}
<div class="input-group">
    {input}
    <div class="input-group-addon">
        $addon
    </div>
</div>
{hint}
{error}
HTML;
    }

    private static function hideRootPath(string $content): string
    {
        return str_replace(Yii::$app->params['App.root'], '', $content);
    }
}
