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
 * @package    Voucher
 * @subpackage View
 * @copyright  Copyright (c) 2012, shopware AG (http://www.shopware.de)
 * @version    $Id$
 * @author shopware AG
 */

//{namespace name=backend/voucher/view/voucher}

/**
 * Shopware UI - Voucher detail main window.
 *
 * Displays all Detail Voucher Information
 */
//{block name="backend/voucher/view/voucher/window"}
Ext.define('Shopware.apps.Voucher.view.voucher.Window', {
	extend: 'Enlight.app.Window',
    title: '{s name=window/detail_title}Voucher configuration{/s}',
    alias: 'widget.voucher-voucher-window',
    border: false,
    autoShow: true,
    layout: 'border',
    height: 630,
    width: 905,
    modal: true,
    /**
     * Display no footer button for the detail window
     * @boolean
     */
    footerButton:false,

    /**
     * Initializes the component and builds up the main interface
     *
     * @return void
     */
    initComponent: function() {
        var me = this;
        me.items = [
            {
                xtype:'tabpanel',
                region:'center',
                items:me.getTabs()
            }
        ];
        me.callParent(arguments);
    },
    /**
     * Creates the tabs for the tab panel of the window.
     * Contains the detail form which is used to display the customer data for an existing customer
     * or to create a new customer.
     * Can contains additionally an second tab which displays the customer orders and a chart which
     * displays the orders grouped by the order year and month
     */
    getTabs:function () {
        return [
            {
                xtype:'voucher-voucher-base_configuration',
                record:this.record,
                taxStore: this.taxStore
            },
            {
                xtype:'voucher-code-list',
                codeStore: this.codeStore,
                disabled:true
            }
        ];
    }
});
//{/block}
