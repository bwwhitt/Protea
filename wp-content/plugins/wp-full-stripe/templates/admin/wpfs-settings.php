<div class="wrap">
    <div class="wpfs-page wpfs-page-settings">
        <?php include('partials/wpfs-header.php'); ?>
        <?php include('partials/wpfs-announcement.php'); ?>

        <?php
            $settingsItems = array();

            array_push( $settingsItems, array(
                'cssClasses'    => 'wpfs-illu-stripe',
                'url'           => $this->getAdminUrlBySlug( MM_WPFS_Admin_Menu::SLUG_SETTINGS_STRIPE ),
                'title'         => __( 'Stripe account', 'wp-full-stripe-admin' ),
                'description'   => __( 'Configure your Stripe API keys, and set up webhooks', 'wp-full-stripe-admin' )
            ) );
            array_push( $settingsItems, array(
                'cssClasses'    => 'wpfs-illu-form',
                'url'           => $this->getAdminUrlBySlug( MM_WPFS_Admin_Menu::SLUG_SETTINGS_FORMS ),
                'title'         => __( 'Forms', 'wp-full-stripe-admin' ),
                'description'   => __( 'Set global settings & styles for your payment forms', 'wp-full-stripe-admin' )
            ) );
            array_push( $settingsItems, array(
                'cssClasses'    => 'wpfs-illu-email',
                'url'           => $this->getAdminUrlBySlug( MM_WPFS_Admin_Menu::SLUG_SETTINGS_EMAIL_NOTIFICATIONS ),
                'title'         => __( 'Email notifications', 'wp-full-stripe-admin' ),
                'description'   => __( 'Customize and align your e-mails to your brand', 'wp-full-stripe-admin' )
            ) );
            array_push( $settingsItems, array(
                'cssClasses'    => 'wpfs-illu-security',
                'url'           => $this->getAdminUrlBySlug( MM_WPFS_Admin_Menu::SLUG_SETTINGS_SECURITY ),
                'title'         => __( 'Security', 'wp-full-stripe-admin' ),
                'description'   => __( 'Keep your payment forms secure', 'wp-full-stripe-admin' )
            ) );
            array_push( $settingsItems, array(
                'cssClasses'    => 'wpfs-illu-customer-portal',
                'url'           => $this->getAdminUrlBySlug( MM_WPFS_Admin_Menu::SLUG_SETTINGS_CUSTOMER_PORTAL ),
                'title'         => __( 'Customer portal', 'wp-full-stripe-admin' ),
                'description'   => __( 'Configure how your customers can manage their cards, subscriptions, and invoices', 'wp-full-stripe-admin' )
            ) );
            array_push( $settingsItems, array(
                'cssClasses'    => 'wpfs-illu-wp',
                'url'           => $this->getAdminUrlBySlug( MM_WPFS_Admin_Menu::SLUG_SETTINGS_WORDPRESS_DASHBOARD ),
                'title'         => __( 'Wordpress dashboard', 'wp-full-stripe-admin' ),
                'description'   => __( 'Set your currency format preferences', 'wp-full-stripe-admin' )
            ) );

        ?>

        <div class="wpfs-list wpfs-list--hub">
            <?php foreach ( $settingsItems as $item ) { ?>
            <a class="wpfs-list__item" href="<?php echo $item['url']; ?>">
                <div class="<?php echo $item['cssClasses']; ?> wpfs-list__icon"></div>
                <div class="wpfs-list__text">
                    <div class="wpfs-list__title"><?php echo $item['title']; ?></div>
                    <div class="wpfs-list__desc"><?php echo $item['description']; ?></div>
                </div>
            </a>
            <?php } ?>
        </div>

        <?php include('partials/wpfs-settings-test-data.php'); ?>
    </div>
	<?php include( 'partials/wpfs-demo-mode.php' ); ?>
</div>
