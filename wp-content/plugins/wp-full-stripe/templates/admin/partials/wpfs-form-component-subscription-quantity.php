<?php
/** @var $view MM_WPFS_Admin_SubscriptionFormView */
/** @var $form */
/** @var $data */
?>
<div class="wpfs-form-group">
    <label class="wpfs-form-label"><?php $view->allowSubscriptionQuantity()->label(); ?></label>
    <div class="wpfs-form-check-list">
        <div class="wpfs-form-check">
            <?php $options = $view->allowSubscriptionQuantity()->options(); ?>
            <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $form->allowMultipleSubscriptions == $options[0]->value(false) ? 'checked' : ''; ?>>
            <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
        </div>
        <div class="wpfs-form-check">
            <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $form->allowMultipleSubscriptions == $options[1]->value(false) ? 'checked' : ''; ?>>
            <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
        </div>
    </div>
</div>
<div id="subscription-maximum-plan-quantity" class="wpfs-form-group" style="<?php echo $form->allowMultipleSubscriptions == 0 ? 'display: none;' : ''; ?>">
    <label for="<?php $view->subscriptionMaximumQuantity()->id(); ?>" class="wpfs-form-label"><?php $view->subscriptionMaximumQuantity()->label(); ?></label>
    <input id="<?php $view->subscriptionMaximumQuantity()->id(); ?>" name="<?php $view->subscriptionMaximumQuantity()->name(); ?>" <?php $view->subscriptionMaximumQuantity()->attributes(); ?> value="<?php echo $form->maximumQuantityOfSubscriptions; ?>">
    <div class="wpfs-form-help"><?php esc_html_e( 'Enter 0 (zero) if there is no limit', 'wp-full-stripe-admin' ); ?></div>
</div>
