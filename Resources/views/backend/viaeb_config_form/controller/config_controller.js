/**
 * $Id: $
 */

Ext.define('Shopware.apps.viaebConfigForm.controller.ConfigController', {

    /**
     * Extend from the standard ExtJS 4 controller
     * @string
     */
    extend: 'Ext.app.Controller',

    requestUrl: '{url controller="viaebResetShopConnection" action="reset"}',
    snippets: {
        growlTitle: '{s name=growlMessage/title}Verbindung zurücksetzen{/s}',
        growlMessageStart: '{s name=growlMessage/start}Starte Vorgang{/s}',
        growlMessageSuccess: '{s name=growlMessage/success}Vorgang erfolgreich{/s}',
        growlMessageFailureTimeout: '{s name=growlMessage/timeout}Timeout: Server nicht erreichbar{/s}',
        growlMessageFailureServer: '{s name=growlMessage/serverFailure}Fehler: Bei der Ausführung des Vorgangs ist ein Fehler aufgetreten{/s}',
        growlModule: '{s name=growlMessage/module}viaebResetShopConnection{/s}',
    },

    init: function () {
        const me = this;

        console.log('dbg1');

        me.configWindow = me.getView('ConfigWindow').create();

        me.control({
            'window[id=config_window]': {
                // 'saveAfterbuyConfig': me.saveAfterbuyConfig,
                'saveAfterbuyConfig': function (form) {
                    console.log('received event');
                    me.saveAfterbuyConfig(form);
                },
            }
        })
    },

    saveAfterbuyConfig: function (form) {
        // The getForm() method returns the Ext.form.Basic instance:
        console.log('dbg');

        const me = this;

        // The getForm() method returns the Ext.form.Basic instance:
        form = form.getForm();

        if (form.isValid()) {
            // Submit the Ajax request and handle the response
            form.submit({
                success: function(form, action) {
                    Shopware.Notification.createGrowlMessage(
                        '{s namespace="backend/afterbuy" name="success"}Erfolg{/s}',
                        '{s namespace="backend/afterbuy" name="saveConnection"}Verbindungsdaten erfolgreich gespeichert{/s}',
                        'Afterbuy Conncetor'
                    );
                },
                failure: function(form, action) {
                    Shopware.Notification.createGrowlMessage(
                        '{s namespace="backend/afterbuy" name="error"}Fehler{/s}',
                        '{s namespace="backend/afterbuy" name="saveConnectionError"}Verbindungsdaten konnten nicht gespeichert werden!{/s}',
                        'Afterbuy Conncetor'
                    );
                }
            });
        }
    },
    //
    // saveAfterbuyConfig_: function () {
    //     const me = this;
    //
    //     Shopware.Notification.createGrowlMessage(
    //         me.snippets.growlTitle,
    //         me.snippets.growlMessageStart
    //     );
    //
    //     me.configWindow.close();
    //
    //     Ext.Ajax.request({
    //         url: me.requestUrl,
    //         method: 'POST',
    //         timeout: 180000,
    //         success: function (resp) {
    //             me.onSuccess(resp, me);
    //         },
    //         failure: function (resp) {
    //             me.onFailure(resp, me)
    //         },
    //     });
    // },

    onSuccess: function (resp, me) {
        const response = JSON.parse(resp.responseText);

        let message = '';

        if (response.success) {
            message = me.snippets.growlMessageSuccess;
        } else {
            message = me.snippets.growlMessageFailureServer + ':<br>';
            for (let index = 0; index < response['total']; index++) {
                message += response['data'][index] + '<br>';
            }
        }

        Shopware.Notification.createGrowlMessage(
            me.snippets.growlTitle,
            message
        );
    },

    onFailure: function (resp, me) {
        Shopware.Notification.createGrowlMessage(
            me.snippets.growlTitle,
            me.snippets.growlMessageFailureTimeout
        );
    },
});