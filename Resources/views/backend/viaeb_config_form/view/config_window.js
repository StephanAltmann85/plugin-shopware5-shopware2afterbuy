Ext.define('Shopware.apps.viaebConfigForm.view.ConfigWindow', {
    extend: 'Enlight.app.Window',

    id: 'config_window',

    snippets: {
        title: '{s namespace="backend/viaebConfigForm" name="config_window_title"}Konfiguration{/s}',
    },

    height: 420,
    width: 500,
    border: true,
    layout: 'fit',
    autoShow: true,

    /**
     * The body padding is used in order to have a smooth side clearance.
     * @integer
     */
    bodyPadding: 1,

    /**
     * Disable window resize
     * @boolean
     */
    resizable: false,

    /**
     * Disables the maximize button in the window header
     * @boolean
     */
    maximizable: false,
    /**
     * Disables the minimize button in the window header
     * @boolean
     */
    minimizable: true,

    initComponent: function () {
        const me = this;

        me.registerEvents();

        me.title = me.snippets.title;

        me.form = me.createForm();

        me.items = me.form;

        me.callParent(arguments);
    },

    registerEvents: function () {
        this.addEvents(
            'saveAfterbuyConfig'
        );
    },

    createForm: function () {
        const me = this;

        return Ext.create('Ext.form.Panel', {
            url: '{url controller="viaebConfigForm" action="saveConnectionConfig"}',
            items: me.createTabPanel(),
            buttons: [
                me.createSubmitButton()
            ],
        });
    },

    createTabPanel: function () {
        const me = this;

        return Ext.create('Ext.tab.Panel', {
            layout: {
                type: 'vbox',
                align: 'center',
            },

            items: [
                me.createConnectionConfigPanel(),
                me.createGeneralConfigPanel(),
            ],
        });
    },

    createConnectionConfigPanel: function () {
        const me = this;

        return Ext.create('Ext.form.Panel', {
            title: '{s namespace="backend/viaebConfigForm" name="config_connection_title"}Verbindung{/s}',
            flex: 1,
            width: '100%',
            // value: me.snippets.infoText,
            htmlEncode: true,
            bodyPadding: 10,

            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    xtype: 'fieldset',
                    title: '{s namespace="backend/viaebConfigForm" name=connection_settings}Verbindungsdaten{/s}',
                    defaultType: 'textfield',
                    autoScroll: true,
                    flex: 1,
                    defaults: {
                        /*{if !{acl_is_allowed privilege=create} && !{acl_is_allowed privilege=update}}*/
                        readOnly: true,
                        /*{/if}*/
                        labelStyle: 'font-weight: 700; text-align: right;',
                        layout: 'anchor',
                        labelWidth: 130,
                        anchor: '100%'
                    },
                    items: me.getConnectionConfigFields(),
                }
            ],
        });
    },

    createGeneralConfigPanel: function () {
        const me = this;

        return Ext.create('Ext.form.Panel', {
            title: '{s namespace="backend/viaebConfigForm" name="config_general_title"}Allg. Einstellungen{/s}',
            flex: 1,
            width: '100%',
            htmlEncode: true,
            bodyPadding: 10,

            layout: {
                type: 'vbox',
                align: 'stretch'
            },
            items: [
                {
                    xtype: 'fieldset',
                    title: '{s namespace="backend/viaebConfigForm" name=general_settings}Einstellungen{/s}',
                    defaultType: 'textfield',
                    autoScroll: true,
                    flex: 1,
                    defaults: {
                        /*{if !{acl_is_allowed privilege=create} && !{acl_is_allowed privilege=update}}*/
                        readOnly: true,
                        /*{/if}*/
                        labelStyle: 'font-weight: 700; text-align: right;',
                        layout: 'anchor',
                        labelWidth: 130,
                        anchor: '100%'
                    },
                    items: me.getGeneralConfigFields(),
                }
            ],
        });
    },

    getConnectionConfigFields: function () {
        return [
            {
                xtype: 'textfield',
                fieldLabel: '{s namespace="backend/viaebConfigForm" name=label_user}Afterbuy User{/s}',
                name: 'userName',
                allowBlank: false,
                checkChangeBuffer: 300,
                value: '{config name="userName" namespace="viaebShopwareAfterbuy"}',
            },
            {
                fieldLabel: '{s namespace="backend/viaebConfigForm" name=label_userpw}User Password{/s}',
                name: 'userPassword',
                inputType: 'password',
                value: '{config name="userPassword" namespace="viaebShopwareAfterbuy"}',
            },
            {
                fieldLabel: '{s namespace="backend/viaebConfigForm" name=label_partnerid}Partner ID:{/s}',
                name: 'partnerId',
                value: '{config name="partnerId" namespace="viaebShopwareAfterbuy"}',
            },
            {
                fieldLabel: '{s namespace="backend/viaebConfigForm" name=label_partnerpw}Partner Pw:{/s}',
                name: 'partnerPassword',
                inputType: 'password',
                value: '{config name="partnerPassword" namespace="viaebShopwareAfterbuy"}',
            },
        ];
    },

    getGeneralConfigFields: function () {
        return [
            {
                fieldLabel: '{s namespace="backend/viaebConfigForm" name=testfield1_user}Testfield1 label{/s}',
                name: 'testField1',
                allowBlank: false,
                checkChangeBuffer: 300,
                // value: '{config name="testField1" namespace="viaebShopwareAfterbuy"}',
                value: 'q',
            },
        ];
    },

    createSubmitButton: function () {
        const me = this;

        return {
            text: 'Submit',
            cls: 'button primary',
            handler: function () {
                me.fireEvent('saveAfterbuyConfig', me.form);
            },
        };
    },

    // getConnectionConfigButtons: function () {
    //     return [
    //         {
    //             text: 'Test',
    //             cls: 'button secondary',
    //             handler: function () {
    //                 // The getForm() method returns the Ext.form.Basic instance:
    //                 const form = this.up('form').getForm();
    //                 if (form.isValid()) {
    //                     // Submit the Ajax request and handle the response
    //
    //
    //                     form.submit({
    //                         url: '{url controller="viaebConfigForm" action="testConnectionConfig"}',
    //                         success: function (form, action) {
    //                             Shopware.Notification.createGrowlMessage(
    //                                 '{s namespace="backend/afterbuy" name="success"}Erfolg{/s}',
    //                                 '{s namespace="backend/afterbuy" name="saveConnection"}Verbindungsdaten erfolgreich gespeichert{/s}',
    //                                 'Afterbuy Conncetor'
    //                             );
    //                         },
    //                         failure: function (form, action) {
    //                             Shopware.Notification.createGrowlMessage(
    //                                 '{s namespace="backend/afterbuy" name="error"}Fehler{/s}',
    //                                 '{s namespace="backend/afterbuy" name="saveConnectionError"}Verbindungsdaten konnten nicht gespeichert werden!{/s}',
    //                                 'Afterbuy Conncetor'
    //                             );
    //                         }
    //                     });
    //                 }
    //             }
    //         },
    //         {
    //             text: 'Submit',
    //             cls: 'button primary',
    //             type: 'submit',
    //             // id: 'abc_button',
    //             // handler: function () {
    //             //     const me = this;
    //             //
    //             //     console.log('fire event');
    //             //
    //             //     me.fireEvent('saveAfterbuyConfig', me.items);
    //             // }
    //         },
    //     ];
    // }
});