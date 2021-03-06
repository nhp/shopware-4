/**
 * Shopware 4.0
 * Copyright © 2012 shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 *
 * @category   Shopware
 * @package    UserManager
 * @subpackage View
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/emotion/view/detail}

/**
 * Shopware UI - Media Manager Main Window
 *
 * This file contains the business logic for the User Manager module. The module
 * handles the whole administration of the backend users.
 */
//{block name="backend/emotion/view/component/settings_window"}
Ext.define('Shopware.apps.Emotion.view.components.SettingsWindow', {
	extend: 'Enlight.app.Window',
    alias: 'widget.emotion-detail-settings-window',
    border: false,
    layout: 'fit',
    autoShow: true,
    height: 550,
    width: 725,
    stateful: true,
    stateId: 'emotion-detail-settings-window',

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;

        // Set the window title
        me.title = me.settings.component.get('name');

        // Build up the items
        me.items = [{
            xtype: me.settings.component.get('xType') || 'emotion-components-base',
            settings: me.settings
        }];

        // Build the action toolbar
        me.dockedItems = [{
            dock: 'bottom',
            xtype: 'toolbar',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: me.createActionButtons()
        }];

        me.callParent(arguments);
    },

    /**
     * Registers additional component events.
     */
    registerEvents: function() {
    	this.addEvents(
    		/**
    		 * Fired when the user clicks the save button to save the component settings
    		 *
    		 * @event
    		 * @param [object] The component form panel
    		 * @param [object] The component record
    		 */
    		'saveComponent'
    	);
    },

    createActionButtons: function() {
        var me = this;

        return ['->', {
            xtype: 'button',
            cls: 'secondary',
            text: '{s name=settings_window/cancel}Cancel{/s}',
            action: 'emotion-detail-settings-window-cancel',
            handler: function(button) {
                var win = button.up('window');
                win.destroy();
            }
        }, {
            xtype: 'button',
            cls: 'primary',
            text: '{s name=settings_window/save}Save{/s}',
            action: 'emotion-detail-settings-window-save',
            handler: function() {
                me.fireEvent('saveComponent', me, me.settings.record, me.settings.fields);
            }
        }];
    }
});
//{/block}