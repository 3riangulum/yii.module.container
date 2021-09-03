<?php

/* @var $this yii\web\View */
/* @var $config array */ ?>

<ul class="nav nav-tabs" role="tablist">
    <?php foreach ($config as $data) : ?>
        <li role="presentation" class="<?php echo($data['active'] ? 'active' : '') ?>">
            <a href="#<?php echo $data['href'] ?>" aria-controls="info" role="tab" data-toggle="tab" class="<?php echo 'nav-tab-' . $data['href']; ?> text-center"><?php echo $data['title'] ?></a>
        </li>
    <?php endforeach; ?>
</ul>
