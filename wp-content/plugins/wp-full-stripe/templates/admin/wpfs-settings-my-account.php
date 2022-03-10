<?php
/** @var $backLinkUrl */
/** @var $view MM_WPFS_Admin_CustomerPortalView */
/** @var $myAccountData */
?>
<div class="wrap">
    <div class="wpfs-page wpfs-page-settings-my-account">
        <?php include('partials/wpfs-header-with-back-link.php'); ?>
        <?php include('partials/wpfs-announcement.php'); ?>

        <form <?php $view->formAttributes(); ?>>
            <input id="<?php $view->action()->id(); ?>" name="<?php $view->action()->name(); ?>" value="<?php $view->action()->value(); ?>" <?php $view->action()->attributes(); ?>>
            <div class="wpfs-form__cols">
                <div class="wpfs-form__col">
                    <div class="wpfs-form-block">
                        <div class="wpfs-form-group">
                            <label class="wpfs-form-label"><?php $view->cancelSubscriptions()->label(); ?></label>
                            <div class="wpfs-form-check-list">
                                <div class="wpfs-form-check">
                                    <?php $options = $view->cancelSubscriptions()->options(); ?>
                                    <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $myAccountData->cancelSubscriptions == $options[0]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
                                </div>
                                <div class="wpfs-form-check">
                                    <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $myAccountData->cancelSubscriptions == $options[1]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="wpfs-form-group" id="wpfs-when-cancel-subscriptions-row" style="<?php echo $myAccountData->cancelSubscriptions == '0' ? 'display: none;' : ''; ?>">
                            <label class="wpfs-form-label"><?php $view->whenCancelSubscriptions()->label(); ?></label>
                            <div class="wpfs-form-check-list">
                                <div class="wpfs-form-check">
                                    <?php $options = $view->whenCancelSubscriptions()->options(); ?>
                                    <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $myAccountData->whenCancelSubscriptions == $options[0]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
                                </div>
                                <div class="wpfs-form-check">
                                    <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $myAccountData->whenCancelSubscriptions == $options[1]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="wpfs-form-group">
                            <label class="wpfs-form-label"><?php $view->updowngradeSubscriptions()->label(); ?></label>
                            <div class="wpfs-form-check-list">
                                <div class="wpfs-form-check">
                                    <?php $options = $view->updowngradeSubscriptions()->options(); ?>
                                    <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $myAccountData->updowngradeSubscriptions == $options[0]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
                                </div>
                                <div class="wpfs-form-check">
                                    <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $myAccountData->updowngradeSubscriptions == $options[1]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="wpfs-form-group">
                            <label class="wpfs-form-label"><?php $view->showInvoices()->label(); ?></label>
                            <div class="wpfs-form-check-list">
                                <div class="wpfs-form-check">
                                    <?php $options = $view->showInvoices()->options(); ?>
                                    <input id="<?php $options[0]->id(); ?>" name="<?php $options[0]->name(); ?>" <?php $options[0]->attributes(); ?> value="<?php $options[0]->value(); ?>" <?php echo $myAccountData->showInvoices == $options[0]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[0]->id(); ?>"><?php $options[0]->label(); ?></label>
                                </div>
                                <div class="wpfs-form-check">
                                    <input id="<?php $options[1]->id(); ?>" name="<?php $options[1]->name(); ?>" <?php $options[1]->attributes(); ?> value="<?php $options[1]->value(); ?>" <?php echo $myAccountData->showInvoices == $options[1]->value(false) ? 'checked' : ''; ?>>
                                    <label class="wpfs-form-check-label" for="<?php $options[1]->id(); ?>"><?php $options[1]->label(); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wpfs-form-actions">
                        <button class="wpfs-btn wpfs-btn-primary wpfs-button-loader" type="submit"><?php esc_html_e( 'Save settings', 'wp-full-stripe-admin' ); ?></button>
                        <a href="<?php echo $backLinkUrl; ?>" class="wpfs-btn wpfs-btn-text"><?php esc_html_e( 'Cancel', 'wp-full-stripe-admin' ); ?></a>
                    </div>
                </div>
                <div class="wpfs-form__col">
                    <div class="wpfs-inline-message wpfs-inline-message--info wpfs-inline-message--w448">
                        <div class="wpfs-inline-message__inner">
                            <div class="wpfs-inline-message__title"><?php esc_html_e( 'What is the Customer portal?', 'wp-full-stripe-admin' ); ?></div>
                            <p><?php esc_html_e( 'Customer portal is a page on your website where customers can update their card, upgrade/downgrade subscriptions, cancel subscriptions, and download invoices. ', 'wp-full-stripe-admin' ); ?></p>
                            <p>
                                <a class="wpfs-btn wpfs-btn-link" href="https://paymentsplugin.com/kb/creating-manage-subscriptions-page" target="_blank"><?php esc_html_e( 'Learn more about Customer portal', 'wp-full-stripe-admin' ); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div id="wpfs-success-message-container"></div>
    </div>
	<?php include( 'partials/wpfs-demo-mode.php' ); ?>
</div>

<script type="text/template" id="wpfs-success-message">
    <div class="wpfs-floating-message__inner">
        <div class="wpfs-floating-message__message"><%- successMessage %></div>
        <button class="wpfs-btn wpfs-btn-icon js-hide-flash-message">
            <span class="wpfs-icon-close"></span>
        </button>
    </div>
</script>
