/*
 *  @author    TuanHa
 *  @copyright Copyright (c) 2025 Tuan Ha <https://www.tuanha.dev/>
 *
 */

define([
    'jquery'
], function ($) {
    'use strict';

    return function (config) {
        return {
            init() {
                // Initialize admin bar data for templates
                window.thAdminBarData = {
                    isLoggedIn: false,
                    shouldShow: false,
                    userId: null,
                    userName: null,
                    isLoading: true,
                    visible: false,

                    // Initialize the component
                    init() {
                        this.checkAdminStatus();
                        this.startPeriodicRefresh();
                    },

                    // Check admin authentication status
                    checkAdminStatus() {
                        this.isLoading = true;

                        fetch(config.statusUrl, {
                            method: 'GET',
                            credentials: 'same-origin'
                        }).then(response => response.json()).then(data => {
                            if (data.success) {
                                this.isLoggedIn = data.isLoggedIn;
                                this.shouldShow = data.shouldShow;
                                this.userId = data.userId;
                                this.userName = data.userName;
                                this.visible = data.shouldShow && data.isLoggedIn;
                            } else {
                                this.isLoggedIn = false;
                                this.shouldShow = false;
                                this.visible = false;
                            }
                            this.isLoading = false;
                        }).catch(error => {
                            console.error('TH Admin Bar: Status check failed:', error);
                            this.isLoading = false;
                        });
                    },

                    // Toggle admin bar visibility
                    toggle() {
                        this.visible = !this.visible;
                    },

                    // Hide admin bar
                    hide() {
                        this.visible = false;
                    },

                    // Show admin bar
                    show() {
                        this.visible = true;
                    },

                    // Start periodic refresh to extend cookie lifetime
                    startPeriodicRefresh() {
                        // Refresh every 5 minutes (300000 ms) if admin bar is visible
                        setInterval(() => {
                            if (this.visible && this.isLoggedIn) {
                                this.refreshAdminSession();
                            }
                        }, 300000); // 5 minutes
                    },

                    // Refresh admin session to extend cookie
                    refreshAdminSession() {
                        fetch(config.refreshUrl || '/thadminbar/auth/refresh/', {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).then(response => response.json()).then(data => {
                            if (data.success) {
                                console.log('TH Admin Bar: Session refreshed successfully');
                            } else {
                                console.warn('TH Admin Bar: Failed to refresh session');
                            }
                        }).catch(error => {
                            console.error('TH Admin Bar: Refresh failed:', error);
                        });
                    }
                };
            }
        };
    };
});
