<div class="bs-docs-header" id="content" tabindex="-1">
    <div class="container">
        <h1><?= __('Git'); ?></h1>
    </div>
</div>

<div class="container bs-docs-container">
    <div class="bs-docs-section">
        <ul class="nav nav-tabs" role="tablist">
            <li <?= ($ROUTE === 'git-index') ? 'class="active"' : ''; ?>><a href="<?= route('/git'); ?>"><?= __('Status'); ?></a></li>
            <?php if ($ROUTE === 'git-commit') { ?><li class="active"><a href=""><?= __('Commit'); ?></a></li><?php } ?>
            <?php if ($ROUTE === 'git-diff') { ?><li class="active"><a href=""><?= __('Diff'); ?></a></li><?php } ?>
            <li <?= ($ROUTE === 'git-update') ? 'class="active"' : ''; ?>><a href="<?= route('/git/update'); ?>"><?= __('Update'); ?></a></li>
            <li <?= ($ROUTE === 'git-log') ? 'class="active"' : ''; ?>><a href="<?= route('/git/log'); ?>"><?= __('Log'); ?></a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane active">
                <?php template()->show('content'); ?>
            </div>
        </div>
    </div>
</div>
