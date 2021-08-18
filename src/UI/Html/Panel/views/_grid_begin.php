<?php

/**
 * @var $title      string
 * @var $panelClass string
 * @var $resetLink  array
 */ ?>

<div class="panel <?php echo $panelClass ?>">

    <div class="panel-heading">

        <table width="100%">
            <tbody>
            <tr>
                <td width="33.33%" class="uppercase text-strong"><?php echo $title ?></td>
                <td width="33.33%">
                    <div class="text-center text-strong">
                        <?php echo($resetLink ?? ''); ?>
                    </div>
                </td>
                <td width="33.33%" class="page-summary text-strong"></td>
            </tr>
            </tbody>
        </table>

    </div>

    <div class="panel-body">
        <div class="table-responsive">
