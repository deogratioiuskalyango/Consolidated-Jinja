<?php

return [
    [
        'name' => 'E-Wallet',
        'flag' => 'e-wallet.index',
        'parent_flag' => 'plugins.ecommerce',
    ],
    [
        'name' => 'View Wallets',
        'flag' => 'e-wallet.wallets.index',
        'parent_flag' => 'e-wallet.index',
    ],
    [
        'name' => 'Adjust Balance',
        'flag' => 'e-wallet.wallets.adjust',
        'parent_flag' => 'e-wallet.wallets.index',
    ],
    [
        'name' => 'View Transactions',
        'flag' => 'e-wallet.transactions.index',
        'parent_flag' => 'e-wallet.index',
    ],
    [
        'name' => 'View Top-ups',
        'flag' => 'e-wallet.topups.index',
        'parent_flag' => 'e-wallet.index',
    ],
    [
        'name' => 'Complete Top-ups',
        'flag' => 'e-wallet.topups.complete',
        'parent_flag' => 'e-wallet.topups.index',
    ],
    [
        'name' => 'Cancel Top-ups',
        'flag' => 'e-wallet.topups.cancel',
        'parent_flag' => 'e-wallet.topups.index',
    ],
    [
        'name' => 'View Withdrawals',
        'flag' => 'e-wallet.withdrawals.index',
        'parent_flag' => 'e-wallet.index',
    ],
    [
        'name' => 'Approve Withdrawals',
        'flag' => 'e-wallet.withdrawals.approve',
        'parent_flag' => 'e-wallet.withdrawals.index',
    ],
    [
        'name' => 'Reject Withdrawals',
        'flag' => 'e-wallet.withdrawals.reject',
        'parent_flag' => 'e-wallet.withdrawals.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'e-wallet.settings',
        'parent_flag' => 'e-wallet.index',
    ],
    [
        'name' => 'License',
        'flag' => 'e-wallet.license',
        'parent_flag' => 'e-wallet.index',
    ],
];
