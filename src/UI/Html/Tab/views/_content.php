<?php

/* @var $this yii\web\View */
/* @var $config array */ ?>

<div class="tab-content">
    <?php foreach ($config as $data) : ?>
        <?php $data['params']['navTabClass'] = 'nav-tab-' . $data['href']; ?>
        <div role="tabpanel" class="tab-pane fade <?php echo($data['active'] ? 'in active' : '') ?>" id="<?php echo $data['href'] ?>">
            <?php echo $this->render($data['view'], $data['params']) ?>
        </div>
    <?php endforeach; ?>
</div>
