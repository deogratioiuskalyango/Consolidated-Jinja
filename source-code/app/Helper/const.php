<?php

const ACTIVE = 1;
const DEACTIVATE = 0;
const CANCEL = 2;
// User Role Type
const USER_ROLE_OWNER = 1;
const USER_ROLE_TENANT = 2;
const USER_ROLE_MAINTAINER = 3;
const USER_ROLE_ADMIN = 4;
const USER_ROLE_TEAM_MEMBER = 5;
const USER_ROLE_SHAREHOLDER = 6;

// Shareholder & Governance constants
const SHAREHOLDER_STATUS_ACTIVE    = 1;
const SHAREHOLDER_STATUS_SUSPENDED = 0;

// Share classes
const SHARE_CLASS_ORDINARY     = 1;
const SHARE_CLASS_PREFERENCE   = 2;
const SHARE_CLASS_DEFERRED     = 3;

// Resolution / Vote
const RESOLUTION_STATUS_DRAFT   = 0;
const RESOLUTION_STATUS_OPEN    = 1;
const RESOLUTION_STATUS_CLOSED  = 2;
const RESOLUTION_STATUS_PASSED  = 3;
const RESOLUTION_STATUS_FAILED  = 4;

const VOTE_FOR     = 1;
const VOTE_AGAINST = 2;
const VOTE_ABSTAIN = 3;

// Financial approval
const APPROVAL_STATUS_PENDING  = 0;
const APPROVAL_STATUS_APPROVED = 1;
const APPROVAL_STATUS_REJECTED = 2;
const APPROVAL_STATUS_EXPIRED  = 3;

const APPROVAL_ACTION_APPROVE = 1;
const APPROVAL_ACTION_REJECT  = 2;

// Governance meetings
const MEETING_TYPE_AGM   = 1;
const MEETING_TYPE_BOARD = 2;
const MEETING_TYPE_EGM   = 3;
const MEETING_TYPE_OTHER = 4;

const MEETING_STATUS_SCHEDULED = 1;
const MEETING_STATUS_COMPLETED = 2;
const MEETING_STATUS_CANCELLED = 3;

const MEETING_MODE_IN_PERSON = 1;
const MEETING_MODE_VIRTUAL   = 2;
const MEETING_MODE_HYBRID    = 3;

// Documents
const DOC_STATUS_ACTIVE  = 1;
const DOC_STATUS_EXPIRED = 2;
const DOC_STATUS_DRAFT   = 0;

// Dividends
const DIVIDEND_STATUS_DECLARED = 1;
const DIVIDEND_STATUS_PAID     = 2;
const DIVIDEND_STATUS_CANCELLED= 3;

// Share transfers
const TRANSFER_STATUS_PENDING  = 0;
const TRANSFER_STATUS_APPROVED = 1;
const TRANSFER_STATUS_REJECTED = 2;

// Audit log actions
const AUDIT_LOGIN            = 'login';
const AUDIT_LOGOUT           = 'logout';
const AUDIT_VOTE             = 'vote';
const AUDIT_APPROVE          = 'approve';
const AUDIT_REJECT           = 'reject';
const AUDIT_DOCUMENT_VIEW    = 'document_view';
const AUDIT_DOCUMENT_DOWNLOAD= 'document_download';
const AUDIT_PROFILE_UPDATE   = 'profile_update';
const AUDIT_PASSWORD_CHANGE  = 'password_change';
const AUDIT_SHARE_TRANSFER   = 'share_transfer';

// Gateway
const GATEWAY_MODE_LIVE = 1;
const GATEWAY_MODE_SANDBOX = 2;

// User Status
const USER_STATUS_INACTIVE = 0;
const USER_STATUS_ACTIVE = 1;
const USER_STATUS_DELETED = 2;
const USER_STATUS_UNVERIFIED = 3;
const USER_STATUS_DELETION_REQUESTED = 4;

// Account Deletion Request Status
const DELETION_REQUEST_PENDING  = 0;
const DELETION_REQUEST_APPROVED = 1;
const DELETION_REQUEST_REJECTED = 2;

const OWNER_STATUS_ACTIVE = 1;
const OWNER_STATUS_INACTIVE = 0;

const KYC_STATUS_ACCEPTED = 1;
const KYC_STATUS_PENDING = 2;
const KYC_STATUS_REJECTED = 3;

// Order payment status
const ORDER_PAYMENT_STATUS_PENDING = 0;
const ORDER_PAYMENT_STATUS_PAID = 1;
const ORDER_PAYMENT_STATUS_CANCELLED = 2;

const DURATION_TYPE_MONTHLY = 1;
const DURATION_TYPE_YEARLY = 2;

//Property
const PROPERTY_TYPE_OWN = 1;
const PROPERTY_TYPE_LEASE = 2;
const PROPERTY_UNIT_TYPE_SINGLE = 1;
const PROPERTY_UNIT_TYPE_MULTIPLE = 2;

// Property Amenity
const PROPERTY_AMENITY_FIRE_SECURITY = 1;
const PROPERTY_AMENITY_ELECTRICITY = 2;
const PROPERTY_AMENITY_KITCHEN = 3;
const PROPERTY_AMENITY_GARAGE = 4;
const PROPERTY_AMENITY_SWIMMING_POOL = 5;
const PROPERTY_AMENITY_SECURITY_PRIVACY = 6;
const PROPERTY_AMENITY_ECO_FRIENDLY_ENERGY = 7;

// property advantage
const PROPERTY_ADVANTAGE_PETS_ALLOWED = 1;

//Property Unit
const PROPERTY_UNIT_RENT_TYPE_MONTHLY = 1;
const PROPERTY_UNIT_RENT_TYPE_YEARLY = 2;
const PROPERTY_UNIT_RENT_TYPE_CUSTOM = 3;

const LISTING_STATUS_ACTIVE = 1;
const LISTING_STATUS_DEACTIVATE = 2;
const LISTING_STATUS_CLOSED = 3;

const LISTING_CARD_TYPE_ONE = 1;

const LISTING_CONTACT_STATUS_PENDING = 1;
const LISTING_CONTACT_STATUS_VIEWED = 2;
const LISTING_CONTACT_STATUS_MAILED = 3;

const PROPERTY_STATUS_ACTIVE = 1;
const PROPERTY_STATUS_DEACTIVATE = 2;

const SEND_EMAIL_STATUS_ACTIVE = 1;
const SEND_EMAIL_STATUS_DEACTIVATE = 0;

const REMAINDER_STATUS_ACTIVE = 1;
const REMAINDER_STATUS_DEACTIVATE = 0;

const REMAINDER_EVERYDAY_STATUS_ACTIVE = 1;
const REMAINDER_EVERYDAY_STATUS_DEACTIVATE = 0;

const EMAIL_VERIFICATION_STATUS_ACTIVE = 1;
const EMAIL_VERIFICATION_STATUS_DEACTIVATE = 0;

//Message
const SOMETHING_WENT_WRONG = "Something went wrong! Please try again";
const CREATED_SUCCESSFULLY = "Created Successfully";
const UPDATED_SUCCESSFULLY = "Updated Successfully";
const STATUS_UPDATED_SUCCESSFULLY = "Status Updated Successfully";
const DELETED_SUCCESSFULLY = "Deleted Successfully";
const UPLOADED_SUCCESSFULLY = "Uploaded Successfully";
const DATA_FETCH_SUCCESSFULLY = "Data Fetch Successfully";
const SENT_SUCCESSFULLY = "Sent Successfully";
const PAY_SUCCESSFULLY = "Pay Successfully";
const REPLIED_SUCCESSFULLY = "Replied Successfully";
const VALIDATION_ERRORS = "Validation Errors";
const VERIFY_YOUR_EMAIL = "Verify Your Email";
const EMAIL_VERIFIED_SUCCESSFULLY = "Email Verified Successfully";
const LOGIN_SUCCESSFUL = "Login Successful";
const CANCELED_SUCCESSFULLY = "Canceled Successfully!";
const ASSIGNED_SUCCESSFULLY = "Assigned Successfully!";


// Property Step Active Class
const PROPERTY_INFORMATION_ACTIVE_CLASS = 1;
const LOCATION_ACTIVE_CLASS = 2;
const UNIT_ACTIVE_CLASS = 3;
const RENT_CHARGE_ACTIVE_CLASS = 4;
const IMAGE_ACTIVE_CLASS = 5;

//Expense
const EXPENSE_RESPONSIBILITY_TENANT = 1;
const EXPENSE_RESPONSIBILITY_OWNER = 2;

const FORM_STEP_ONE = 1;
const FORM_STEP_TWO = 2;
const FORM_STEP_THREE = 3;

const TENANT_STATUS_ACTIVE = 1;
const TENANT_STATUS_INACTIVE = 2;
const TENANT_STATUS_DRAFT = 3;
const TENANT_STATUS_CLOSE = 4;

const RENT_TYPE_MONTHLY = 1;
const RENT_TYPE_YEARLY = 2;
const RENT_TYPE_CUSTOM = 3;
//Invoice
const INVOICE_STATUS_PENDING = 0;
const INVOICE_STATUS_UNPAID  = 0;   // alias for INVOICE_STATUS_PENDING
const INVOICE_STATUS_PAID = 1;
const INVOICE_STATUS_OVER_DUE = 2;

// Property Unit status
const UNIT_STATUS_VACANT   = 1;
const UNIT_STATUS_OCCUPIED = 2;

const INVOICE_RECURRING_TYPE_MONTHLY = 1;
const INVOICE_RECURRING_TYPE_YEARLY = 2;
const INVOICE_RECURRING_TYPE_CUSTOM = 3;

const NOTICE_STATUS_VIEW = 1;
const NOTICE_STATUS_PENDING = 0;

const NOTIFICATION_TYPE_MULTIPLE = 1;
const NOTIFICATION_TYPE_SINGLE = 2;

const MAINTENANCE_REQUEST_STATUS_COMPLETE = 1;
const MAINTENANCE_REQUEST_STATUS_INPROGRESS = 2;
const MAINTENANCE_REQUEST_STATUS_PENDING = 3;

const TICKET_STATUS_OPEN = 1;
const TICKET_STATUS_INPROGRESS = 2;
const TICKET_STATUS_CLOSE = 3;
const TICKET_STATUS_REOPEN = 4;
const TICKET_STATUS_RESOLVED = 5;

const TICKET_PRIORITY_LOW    = 1;
const TICKET_PRIORITY_MEDIUM = 2;
const TICKET_PRIORITY_HIGH   = 3;
const TICKET_PRIORITY_URGENT = 4;

const TAX_TYPE_FIXED = 0;
const TAX_TYPE_PERCENTAGE = 1;

const TYPE_FIXED = 0;
const TYPE_PERCENTAGE = 1;

//Gateway Name
const PAYPAL = 'paypal';
const STRIPE = 'stripe';
const RAZORPAY = 'razorpay';
const INSTAMOJO = 'instamojo';
const MOLLIE = 'mollie';
const PAYSTACK = 'paystack';
const SSLCOMMERZ = 'sslcommerz';
const MERCADOPAGO = 'mercadopago';
const FLUTTERWAVE = 'flutterwave';
const BINANCE = 'binance';
const ALIPAY = 'alipay';
const BANK = 'bank';
const CASH = 'cash';
const WALLET = 'wallet';
const COINBASE = 'coinbase';
const PAYTM = 'paytm';
const MAXICASH = 'maxicash';
const IYZIPAY = 'iyzipay';
const BITPAY = 'bitpay';
const ZITOPAY = 'zitopay';
const PAYHERE = 'payhere';
const CINETPAY = 'cinetpay';
const VOGUEPAY = 'voguepay';
const TOYYIBPAY = 'toyyibpay';
const PAYMOB = 'paymob';
const AUTHORIZE = 'authorize';
const XENDIT = 'xendit';
const PADDLE = 'paddle';
const PESAPAL = 'pesapal';

// email templates
const EMAIL_TEMPLATE_CUSTOM = 1;
const EMAIL_TEMPLATE_INVOICE = 2;
const EMAIL_TEMPLATE_REMINDER = 3;
const EMAIL_TEMPLATE_SIGN_UP = 4;
const EMAIL_TEMPLATE_SUBSCRIPTION_SUCCESS = 5;
const EMAIL_TEMPLATE_THANK_YOU = 6;
const EMAIL_TEMPLATE_EMAIL_VERIFY = 7;
const EMAIL_TEMPLATE_WELCOME = 8;
const EMAIL_TEMPLATE_LISTING_REPLY = 9;
const EMAIL_TEMPLATE_LISTING_CONTACT = 10;


// target audience
const TARGET_AUDIENCE_PROPERTY = 1;
const TARGET_AUDIENCE_USER = 2;
const TARGET_AUDIENCE_CUSTOM = 3;

// history status
const SMS_STATUS_DELIVERED = 1;
const SMS_STATUS_PENDING = 2;
const SMS_STATUS_FAILED = 3;

const MAIL_STATUS_DELIVERED = 1;
const MAIL_STATUS_PENDING = 2;
const MAIL_STATUS_FAILED = 3;

// user type
const USER_TYPE_TENANT = 1;
const USER_TYPE_MAINTAINER = 2;

// package rules
const RULES_MAINTAINER = 1;
const RULES_PROPERTY = 2;
const RULES_TENANT = 3;
const RULES_INVOICE = 4;
const RULES_AUTO_INVOICE = 5;
const RULES_PLAN_REMAINING_DAYS = 6;
const RULES_UNIT = 7;

const PACKAGE_DURATION_TYPE_MONTHLY = 1;
const PACKAGE_DURATION_TYPE_YEARLY = 2;

const PACKAGE_TYPE_DEFAULT = 0;
const PACKAGE_TYPE_PROPERTY = 1;
const PACKAGE_TYPE_UNIT = 2;
const PACKAGE_TYPE_TENANT = 3;

const LINK_SAAS_ADDON = "https://codecanyon.net/item/zaiproty-property-management-saas-addon/45346185?s_rank=18";
const LINK_MAIN_SCRIPT = "https://codecanyon.net/item/zaiproty-property-management-laravel-script/43413718";
const LISTING_ADDON = "https://codecanyon.net/item/zaiproty-agreementdocument-signing-addon/46668990?s_rank=15";

// ─── Governance Rules Engine ───────────────────────────────────────────────

// Share Class Codes (A-F)
const SHARE_CLASS_CODE_A = 'A'; // Founder
const SHARE_CLASS_CODE_B = 'B'; // Ordinary
const SHARE_CLASS_CODE_C = 'C'; // Preference
const SHARE_CLASS_CODE_D = 'D'; // Non-Voting
const SHARE_CLASS_CODE_E = 'E'; // Employee/ESOP
const SHARE_CLASS_CODE_F = 'F'; // Strategic Investor

// Governance Levels
const GOVERNANCE_LEVEL_BASIC     = 1;
const GOVERNANCE_LEVEL_ENHANCED  = 2;
const GOVERNANCE_LEVEL_EXECUTIVE = 3;

// Vesting Frequency
const VESTING_FREQUENCY_MONTHLY   = 1;
const VESTING_FREQUENCY_QUARTERLY = 2;
const VESTING_FREQUENCY_ANNUAL    = 3;

// Permission Keys (string constants)
const PERM_VOTE                    = 'vote';
const PERM_APPROVE_EXPENSES        = 'approve_expenses';
const PERM_VIEW_FINANCIAL_REPORTS  = 'view_financial_reports';
const PERM_APPROVE_ACQUISITIONS    = 'approve_acquisitions';
const PERM_APPOINT_DIRECTORS       = 'appoint_directors';
const PERM_ACCESS_AUDIT_LOGS       = 'access_audit_logs';
const PERM_RECEIVE_DIVIDENDS       = 'receive_dividends';
const PERM_VIEW_BOARD_REPORTS      = 'view_board_reports';
const PERM_CREATE_RESOLUTIONS      = 'create_resolutions';
const PERM_VETO_RESOLUTIONS        = 'veto_resolutions';
const PERM_TRIGGER_EMERGENCY_VOTE  = 'trigger_emergency_vote';
const PERM_APPROVE_SHARE_TRANSFERS = 'approve_share_transfers';
const PERM_ONBOARD_SHAREHOLDERS    = 'onboard_shareholders';
const PERM_VIEW_ANALYTICS          = 'view_analytics';
const PERM_ACCESS_CONFIDENTIAL     = 'access_confidential_reports';
const PERM_ACCESS_RISK_REPORTS     = 'access_risk_reports';
const PERM_VOTE_ACQUISITIONS       = 'vote_acquisitions';
const PERM_VOTE_DIVIDENDS          = 'vote_dividends';
const PERM_VOTE_LIQUIDATION        = 'vote_liquidation';
const PERM_VIEW_ESOP               = 'view_esop_reports';
const PERM_VIEW_INVESTMENT         = 'view_investment_reports';

// Resolution Types (for allowed_resolution_types filtering)
const RESOLUTION_TYPE_FINANCIAL      = 1;
const RESOLUTION_TYPE_BOARD          = 2;
const RESOLUTION_TYPE_ACQUISITION    = 3;
const RESOLUTION_TYPE_DIVIDEND       = 4;
const RESOLUTION_TYPE_SHARE_TRANSFER = 5;
const RESOLUTION_TYPE_POLICY         = 6;
const RESOLUTION_TYPE_DIRECTOR       = 7;
const RESOLUTION_TYPE_OTHER          = 8;

// Audit actions (governance engine additions)
const AUDIT_SHARE_CLASS_CHANGE  = 'share_class_change';
const AUDIT_PERMISSION_CHANGE   = 'permission_change';
const AUDIT_GOVERNANCE_RULE     = 'governance_rule';
const AUDIT_VESTING_EVENT       = 'vesting_event';
const AUDIT_DELEGATION          = 'vote_delegation';

// ── Accountant Role ─────────────────────────────────────────────────────────
const USER_ROLE_ACCOUNTANT = 7;

// Accountant Status
const ACCOUNTANT_STATUS_ACTIVE    = 1;
const ACCOUNTANT_STATUS_SUSPENDED = 0;

// Rent Collection Payment Methods
const PAYMENT_METHOD_CASH         = 1;
const PAYMENT_METHOD_BANK         = 2;
const PAYMENT_METHOD_MTN_MOMO     = 3;
const PAYMENT_METHOD_AIRTEL_MONEY = 4;
const PAYMENT_METHOD_CHEQUE       = 5;
const PAYMENT_METHOD_CARD         = 6;

// Rent Collection Types
const COLLECTION_TYPE_RENT          = 1;
const COLLECTION_TYPE_SECURITY_DEPOSIT = 2;
const COLLECTION_TYPE_UTILITY       = 3;
const COLLECTION_TYPE_PENALTY       = 4;
const COLLECTION_TYPE_ADVANCE       = 5;
const COLLECTION_TYPE_PARTIAL       = 6;

// Rent Collection Status
const COLLECTION_STATUS_PENDING   = 0;
const COLLECTION_STATUS_CONFIRMED = 1;
const COLLECTION_STATUS_REVERSED  = 2;

// Expense Categories
const EXPENSE_CAT_MAINTENANCE   = 1;
const EXPENSE_CAT_UTILITIES     = 2;
const EXPENSE_CAT_CONTRACTOR    = 3;
const EXPENSE_CAT_SALARY        = 4;
const EXPENSE_CAT_INSURANCE     = 5;
const EXPENSE_CAT_LEGAL         = 6;
const EXPENSE_CAT_MARKETING     = 7;
const EXPENSE_CAT_OTHER         = 8;

// Expense Status
const EXPENSE_STATUS_PENDING  = 0;
const EXPENSE_STATUS_APPROVED = 1;
const EXPENSE_STATUS_REJECTED = 2;
const EXPENSE_STATUS_PAID     = 3;

// Reconciliation
const RECON_TYPE_MTN_MOMO    = 1;
const RECON_TYPE_AIRTEL_MONEY= 2;
const RECON_TYPE_BANK        = 3;
const RECON_TYPE_CASH        = 4;

const RECON_STATUS_MATCHED   = 1;
const RECON_STATUS_UNMATCHED = 2;
const RECON_STATUS_DUPLICATE = 3;
const RECON_STATUS_SUSPICIOUS= 4;

// Financial Report Types
const REPORT_TYPE_WEEKLY     = 1;
const REPORT_TYPE_MONTHLY    = 2;
const REPORT_TYPE_QUARTERLY  = 3;
const REPORT_TYPE_ANNUAL     = 4;
const REPORT_TYPE_CUSTOM     = 5;

const REPORT_STATUS_DRAFT     = 0;
const REPORT_STATUS_PUBLISHED = 1;

// Financial Audit Actions
const AUDIT_PAYMENT_RECORDED  = 'payment_recorded';
const AUDIT_PAYMENT_REVERSED  = 'payment_reversed';
const AUDIT_EXPENSE_CREATED   = 'expense_created';
const AUDIT_EXPENSE_APPROVED  = 'expense_approved';
const AUDIT_EXPENSE_REJECTED  = 'expense_rejected';
const AUDIT_REPORT_GENERATED  = 'report_generated';
const AUDIT_REPORT_VIEWED     = 'report_viewed';
const AUDIT_RECONCILIATION    = 'reconciliation';
const AUDIT_BALANCE_ADJUSTED  = 'balance_adjusted';
