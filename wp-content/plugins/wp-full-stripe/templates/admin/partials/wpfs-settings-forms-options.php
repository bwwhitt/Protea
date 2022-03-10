<?php
/** @var $view MM_WPFS_Admin_FormsOptionsView */
/** @var $formsOptions */
?>
<form <?php $view->formAttributes(); ?>>
    <input id="<?php $view->action()->id(); ?>" name="<?php $view->action()->name(); ?>" value="<?php $view->action()->value(); ?>" <?php $view->action()->attributes(); ?>>
    <div class="wpfs-form__cols">
        <div class="wpfs-form__col">
            <div class="wpfs-form-block">
                <div class="wpfs-form-group">
                    <label class="wpfs-form-label"><?php $view->fillInEmailForLoggedInUsers()->label(); ?></label>
                    <div class="wpfs-form-check-list">
                        <div class="wpfs-form-check">
                            <?php $options = $view->fillInEmailForLoggedInUsers()->options(); ?>
                            <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $formsOptions->fillInEmailForUsers == $options[0]->value(false) ? 'checked' : ''; ?>>
                            <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
                        </div>
                        <div class="wpfs-form-check">
                            <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $formsOptions->fillInEmailForUsers == $options[1]->value(false) ? 'checked' : ''; ?>>
                            <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="wpfs-form-actions">
                <button class="wpfs-btn wpfs-btn-primary wpfs-button-loader" type="submit"><?php esc_html_e( 'Save settings', 'wp-full-stripe-admin' ); ?></button>
            </div>
        </div>
    </div>
</form>
