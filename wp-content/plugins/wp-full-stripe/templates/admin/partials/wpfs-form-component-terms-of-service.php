<?php
    /** @var $view MM_WPFS_Admin_FormView */
    /** @var $form */
    /** @var $data */
?>
<div class="wpfs-form-group">
    <label class="wpfs-form-label"><?php $view->showTermsOfService()->label(); ?></label>
    <div class="wpfs-form-check-list">
        <div class="wpfs-form-check">
            <?php $options = $view->showTermsOfService()->options(); ?>
            <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $form->showTermsOfUse == $options[0]->value(false) ? 'checked' : ''; ?>>
            <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
        </div>
        <div class="wpfs-form-check">
            <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $form->showTermsOfUse == $options[1]->value(false) ? 'checked' : ''; ?>>
            <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
        </div>
    </div>
</div>
<div class="wpfs-form-group wpfs-tos-section" style="<?php echo $form->showTermsOfUse == $options[1]->value(false) ? '' : 'display: none'; ?>">
    <label for="" class="wpfs-form-label"><?php $view->termsOfServiceLabel()->label(); ?></label>
    <input id="<?php $view->termsOfServiceLabel()->id(); ?>" name="<?php $view->termsOfServiceLabel()->name(); ?>" class="wpfs-form-control" type="text" value="<?php echo esc_html( $form->termsOfUseLabel ); ?>">
</div>
<div class="wpfs-form-group wpfs-tos-section" style="<?php echo $form->showTermsOfUse == $options[1]->value(false) ? '' : 'display: none'; ?>">
    <label for="" class="wpfs-form-label"><?php $view->termsOfServiceErrorMessage()->label(); ?></label>
    <input id="<?php $view->termsOfServiceErrorMessage()->id(); ?>" name="<?php $view->termsOfServiceErrorMessage()->name(); ?>" class="wpfs-form-control" type="text" value="<?php echo $form->termsOfUseNotCheckedErrorMessage; ?>">
</div>
