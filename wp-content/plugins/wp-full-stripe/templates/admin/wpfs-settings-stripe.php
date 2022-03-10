<?php
    /** @var $backLinkUrl */
    /** @var $view MM_WPFS_Admin_ConfigureStripeAccountView */
    /** @var $stripeData */
?>
<div class="wrap">
    <div class="wpfs-page wpfs-page-settings-configure-stripe-account">
        <?php include('partials/wpfs-header-with-back-link.php'); ?>
        <?php include('partials/wpfs-announcement.php'); ?>

        <form <?php $view->formAttributes(); ?>>
            <input id="<?php $view->action()->id(); ?>" name="<?php $view->action()->name(); ?>" value="<?php $view->action()->value(); ?>" <?php $view->action()->attributes(); ?>>
            <div class="wpfs-form__cols">
                <div class="wpfs-form__col">
                    <div class="wpfs-form-block">
                        <div class="wpfs-form-block__title"><?php esc_html_e( 'Test API keys', 'wp-full-stripe-admin' ); ?></div>
                        <div class="wpfs-form-group">
                            <label for="<?php $view->testPublishableKey()->id(); ?>" class="wpfs-form-label"><?php $view->testPublishableKey()->label(); ?></label>
                            <input id="<?php $view->testPublishableKey()->id(); ?>" name="<?php $view->testPublishableKey()->name(); ?>" type="text" value="<?php echo esc_html( $stripeData->testPublishableKey ); ?>" class="wpfs-form-control">
                        </div>
                        <div class="wpfs-form-group">
                            <label for="<?php $view->testSecretKey()->id(); ?>" class="wpfs-form-label"><?php $view->testSecretKey()->label(); ?></label>
                            <input id="<?php $view->testSecretKey()->id(); ?>" name="<?php $view->testSecretKey()->name(); ?>"  type="text" value="<?php echo esc_html( $stripeData->testSecretKey ); ?>" class="wpfs-form-control">
                        </div>
                    </div>
                    <div class="wpfs-form-block">
                        <div class="wpfs-form-block__title"><?php esc_html_e( 'Live API keys', 'wp-full-stripe-admin' ); ?></div>
                        <div class="wpfs-form-group">
                            <label for="<?php $view->livePublishableKey()->id(); ?>" class="wpfs-form-label"><?php $view->livePublishableKey()->label(); ?></label>
                            <input id="<?php $view->livePublishableKey()->id(); ?>" name="<?php $view->livePublishableKey()->name(); ?>" type="text" value="<?php echo esc_html( $stripeData->livePublishableKey ); ?>" class="wpfs-form-control">
                        </div>
                        <div class="wpfs-form-group">
                            <label for="<?php $view->liveSecretKey()->id(); ?>" class="wpfs-form-label"><?php $view->liveSecretKey()->label(); ?></label>
                            <input id="<?php $view->liveSecretKey()->id(); ?>" name="<?php $view->liveSecretKey()->name(); ?>" type="text" value="<?php echo esc_html( $stripeData->liveSecretKey ); ?>" class="wpfs-form-control">
                        </div>
                    </div>
                    <div class="wpfs-form-actions">
                        <button class="wpfs-btn wpfs-btn-primary wpfs-button-loader" type="submit"><?php esc_html_e( 'Save settings', 'wp-full-stripe-admin' ); ?></button>
                        <a href="<?php echo $backLinkUrl; ?>" class="wpfs-btn wpfs-btn-text"><?php esc_html_e( 'Cancel', 'wp-full-stripe-admin' ); ?></a>
                    </div>
                </div>
                <div class="wpfs-form__col">
                    <div class="wpfs-form-block">
                        <div class="wpfs-form-block__title"><?php esc_html_e( 'API mode', 'wp-full-stripe-admin' ); ?></div>
                        <div class="wpfs-form-group">
                            <div class="wpfs-typo-body wpfs-typo-body--gunmetal"><?php esc_html_e( 'Build your integration in test mode, and switch to live mode when you\'re ready.', 'wp-full-stripe-admin' ); ?></div>
                        </div>
                        <div class="wpfs-form-group">
                            <label class="wpfs-toggler">
                                <span><?php esc_html_e( 'Test', 'wp-full-stripe-admin' ); ?></span>
                                <input id="<?php $view->apiMode()->id(); ?>" name="<?php $view->apiMode()->name(); ?>" value="<?php echo MM_WPFS::STRIPE_API_MODE_LIVE; ?>" type="checkbox" <?php echo $stripeData->apiMode === MM_WPFS::STRIPE_API_MODE_LIVE ? 'checked' : ''; ?>>
                                <span class="wpfs-toggler__switcher"></span>
                                <span><?php esc_html_e( 'Live', 'wp-full-stripe-admin' ); ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="wpfs-form-block">
                        <div class="wpfs-form-block__title">Webhooks</div>
                        <div class="wpfs-webhook">
                            <div class="wpfs-status-bullet <?php echo $stripeData->liveEventStyle; ?> wpfs-webhook__bullet">
                                <strong><?php echo $stripeData->liveEventTitle; ?></strong>
                            </div>
                            <div class="wpfs-webhook__last-action"><?php echo $stripeData->liveEventDescription; ?></div>
                            <div class="wpfs-status-bullet <?php echo $stripeData->testEventStyle; ?> wpfs-webhook__bullet">
                                <strong><?php echo $stripeData->testEventTitle; ?></strong>
                            </div>
                            <div class="wpfs-webhook__last-action"><?php echo $stripeData->testEventDescription; ?></div>
                            <div class="wpfs-webhook__inner">
                                <a class="wpfs-btn wpfs-btn-link wpfs-btn-link--sm wpfs-webhook__info-toggler js-webhook-info-toggler" href="" data-closed-text="<?php esc_html_e( 'Show webhook info', 'wp-full-stripe-admin' ); ?>" data-opened-text="<?php esc_html_e( 'Hide webhook info', 'wp-full-stripe-admin' ); ?>">
                                    <span><?php esc_html_e( 'Show webhook info', 'wp-full-stripe-admin' ); ?></span>
                                    <span class="wpfs-icon-chevron wpfs-webhook__chevron"></span>
                                </a>
                                <div class="wpfs-inline-message wpfs-inline-message--info wpfs-webhook__inline-message">
                                    <div class="wpfs-inline-message__inner">
                                        <div class="wpfs-inline-message__title"><?php esc_html_e( 'Webhook URL', 'wp-full-stripe-admin' ); ?></div>
                                        <p class="wpfs-webhook__word-break-all">
                                            <?php echo $stripeData->webHookUrl; ?>
                                            <br>
                                            <a class="wpfs-btn wpfs-btn-link js-copy-webhook-url" href="" data-webhook-url="<?php echo esc_html( $stripeData->webHookUrl); ?>"><?php esc_html_e( 'Copy to clipboard', 'wp-full-stripe-admin' ); ?></a>
                                        </p>
                                        <div class="wpfs-inline-message__title"><?php esc_html_e( 'Webhook URL (legacy)', 'wp-full-stripe-admin' ); ?></div>
                                        <p class="wpfs-webhook__word-break-all">
                                            <?php echo $stripeData->webHookUrlLegacy; ?>
                                            <br>
                                            <a class="wpfs-btn wpfs-btn-link js-copy-webhook-url" href="" data-webhook-url="<?php echo esc_html( $stripeData->webHookUrlLegacy); ?>"><?php esc_html_e( 'Copy to clipboard', 'wp-full-stripe-admin' ); ?></a>
                                        </p>
                                        <p>
                                            <?php
                                                $kbUrl = 'https://paymentsplugin.com/kb/setting-up-webhooks-wp-full-stripe';

                                                echo sprintf( __( 'For more information on configuring and testing webhooks, please refer to the <a class="wpfs-btn wpfs-btn-link" href="%s" target="_blank">Setting up webhooks</a> article in our Knowledge Base.', 'wp-full-stripe-admin' ), $kbUrl );
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
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
