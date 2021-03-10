<?php

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix' => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace' => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('country', 'CountryCrudController');
    Route::crud('region', 'RegionCrudController');
    Route::crud('province', 'ProvinceCrudController');
    Route::crud('commune', 'CommuneCrudController');
    Route::crud('currency', 'CurrencyCrudController');
    Route::crud('businessactivity', 'BusinessActivityCrudController');
    Route::crud('tax', 'TaxCrudController');
    Route::crud('invoicetype', 'InvoiceTypeCrudController');
    Route::crud('unit', 'UnitCrudController');
    Route::crud('company', 'CompanyCrudController');
    Route::crud('branch', 'BranchCrudController');
    Route::crud('attributemodule', 'AttributeModuleCrudController');
    Route::crud('attributefamily', 'AttributeFamilyCrudController');
    Route::crud('attribute', 'AttributeCrudController');
    Route::crud('attributegroup', 'AttributeGroupCrudController');
    Route::crud('customer', 'CustomerCrudController');
    Route::crud('companyuser', 'CompanyUserCrudController');
    Route::crud('branchcompany', 'BranchCompanyCrudController');
    Route::crud('branchuser', 'BranchUserCrudController');
    Route::get('set_current_branch/{branch_id}', function ($branch_id) {
        $user = backpack_user()->set_current_branch($branch_id);

        return redirect()->route('backpack.dashboard');
    })->name('set_current_branch');
    Route::crud('customersegment', 'CustomerSegmentCrudController');
    Route::crud('bank', 'BankCrudController');
    Route::crud('bankaccounttype', 'BankAccountTypeCrudController');
    Route::crud('contacttype', 'ContactTypeCrudController');
    Route::crud('sellercategory', 'SellerCategoryCrudController');
    Route::crud('seller', 'SellerCrudController');
    Route::crud('productbrand', 'ProductBrandCrudController');
    Route::crud('productcategory', 'ProductCategoryCrudController');
    Route::crud('producttype', 'ProductTypeCrudController');
    Route::crud('productclass', 'ProductClassCrudController');
    Route::crud('productclassattribute', 'ProductClassAttributeCrudController');
    Route::crud('product', 'ProductCrudController');
    Route::post('product/bulk-approve', 'ProductCrudController@bulkApprove')->name('products.bulk-approve');
    Route::post('product/bulk-reject', 'ProductCrudController@bulkReject')->name('products.bulk-reject');
    Route::post('product/bulk-delete', 'ProductCrudController@bulkDelete')->name('products.bulk-delete');
    Route::crud('productinventorysource', 'ProductInventorySourceCrudController');
    Route::crud('productinventory', 'ProductInventoryCrudController');
    Route::crud('shippingmethod', 'ShippingMethodCrudController');
    Route::crud('paymentmethod', 'PaymentMethodCrudController');

    // Quotations
    Route::crud('quotation', 'QuotationCrudController');
    Route::get('quotation/{id}/export', 'QuotationCrudController@exportPDF');
    Route::get('quotation/{id}/duplicate', 'QuotationCrudController@duplicate');
    Route::post('quotation/addresses', 'QuotationCrudController@addresses');
    Route::get('report/quotations', 'Report\QuotationsReportController@index')->name('report.quotations');
    Route::get('report/quotations/load-data', 'Report\QuotationsReportController@loadData')->name('report.quotations.load-data');

    //CHART routes
    Route::get('charts/daily-sales', 'Charts\DailySalesChartController@response')->name('charts.daily-sales.index');
    Route::get('charts/most-purchased-product-categories', 'Charts\MostPurchasedProductCategoriesChartController@response')->name('charts.most-purchased-product-categories.index');
    Route::get('charts/most-purchased-products', 'Charts\MostPurchasedProductsChartController@response')->name('charts.most-purchased-products.index');

    /** invoice **/
    Route::crud('invoice', 'InvoiceCrudController');
    Route::get('order/{order}/to-invoice', 'OrderCrudController@toInvoice')->name('order.invoice');
    Route::get('quotation/{quotation}/to-invoice', 'QuotationCrudController@toInvoice');
    Route::get('invoice/{invoice}/send-temporary-document', 'ManageInvoiceCrudController@sendTemporaryDocument')->name('invoice.generate-temp-document');
    Route::get('invoice/{invoice}/create-real-document', 'ManageInvoiceCrudController@createRealDocument');
    Route::get('invoice/{invoice}/generate-temp-and-real-document', 'ManageInvoiceCrudController@generateTemporalAndRealDocument')->name('invoice.generate-temp-real-document');
    Route::get('invoice/{invoice}/update-dte-status', 'ManageInvoiceCrudController@updateDteStatus')->name('dte.update.status');
    Route::get('invoice/{invoice}/delete-temporary-document', 'ManageInvoiceCrudController@deleteTemporaryDocument');
    Route::get('invoice/{invoice}/to-manage', 'ManageInvoiceCrudController@index');
    Route::get('invoice/{invoice}/get-pdf', 'ManageInvoiceCrudController@getPDF')->name('invoice.get-pdf');
    Route::get('invoice/{invoice}/issue-credit-note', 'ManageInvoiceCrudController@issueCreditNote');
    Route::get('invoice-type/{invoiceType}/get-code', 'InvoiceTypeCrudController@getCode');
    Route::crud('payments', 'PaymentsCrudController');
    Route::get('get-data-invoice', 'InvoiceCrudController@getDataInvoice');

    // API routes
    // dashboard
    Route::post('api/invoice/salesbyperiod', 'InvoiceCrudController@salesByPeriod');
    Route::post('api/quotation/bystatus', 'QuotationCrudController@byStatus');
    Route::post('api/quotation/generalStatus', 'QuotationCrudController@generalStatus');
    Route::post('api/product/top_table_dashboard', 'ProductCrudController@getTopTableDashboard');
    Route::post('api/customer/top_table_dashboard', 'CustomerCrudController@getTopTableDashboard');
    Route::get('charts/top-customers-in-period', 'Charts\TopCustomersInPeriodChartController@response')->name('charts.top-customers-in-period.index');
    ///
    Route::get('api/productclass/get', 'ProductClassCrudController@searchProductClasses');
    Route::get('api/productclassattributes/get', 'ProductClassAttributeCrudController@searchConfigurableAttributes');
    Route::get('api/products/getBySeller', 'ProductCrudController@getProductBySeller');
    Route::get('api/products/get-by-current-company', 'ProductCrudController@getProductByCurrentCompany');
    Route::post('api/getPlans', 'PlansCrudController@getPlans');
    Route::post('api/getPlanById', 'PlansCrudController@getPlanById');
    Route::get('/api/timeblocks/get-by-service', 'TimeBlockCrudController@getTimeblocksByService');
    Route::crud('order', 'OrderCrudController');
    Route::crud('faqanswer', 'FaqAnswerCrudController');
    Route::crud('faqtopic', 'FaqTopicCrudController');
    Route::crud('communeshippingmethod', 'CommuneShippingMethodCrudController');

    Route::crud('plans', 'PlansCrudController');
    Route::get('payment/subscription/{id}', 'Payments\WebPayPlusController@subscriptionPayment')->name('payment.subscription');
    Route::get('report/sales', 'Report\SalesReportController@index')->name('report.sales');

    Route::crud('banners', 'BannersCrudController');
    Route::crud('customersupport', 'CustomerSupportCrudController');
    Route::crud('pos', 'PosCrudController');
    Route::get('posorder/{id}', 'PosCrudController@posOrder')->name('pos.order');;

    Route::crud('customerattendance', 'CustomerAttendanceCrudController');
    Route::crud('service', 'ServiceCrudController');
    Route::crud('timeblock', 'TimeBlockCrudController');

    Route::crud('reservationrequest', 'ReservationRequestCrudController');

    // Inventory
    Route::crud('inventory', 'InventoryCrudController');
    Route::get('/inventory/mass-receptions', 'InventoryCrudController@massReceptionsView')->name('inventory.mass-receptions');
    Route::get('/inventory/mass-receptions/generate-template', 'InventoryCrudController@generateExcelTemplate')->name('inventory.mass-receptions.generate-template');
    Route::post('/inventory/mass-receptions/preview', 'InventoryCrudController@massReceptionsPreview')->name('inventory.mass-receptions.preview');
    Route::post('/inventory/mass-receptions/store', 'InventoryCrudController@massReceptionsStore')->name('inventory.mass-receptions.store');
    Route::get('/inventory/mass-receptions/result', 'InventoryCrudController@massReceptionsResult')->name('inventory.mass-receptions.result');

    //Route::get('admin/payment/subscription/test/{id}', 'App\Http\Controllers\Admin\Payments\WebPayPlusController@subscriptionTestPayment')->name('payment.test.detail');
    Route::crud('transaction', 'TransactionCrudController');
    Route::get('api/transaction/get-documents-by-company', 'TransactionCrudController@getDocumentsByCompany');
    Route::crud('bankaccount', 'BankAccountCrudController');
    Route::crud('transactiontype', 'TransactionTypeCrudController');
    Route::crud('accountingaccount', 'AccountingAccountCrudController');
    Route::crud('transactiondetail', 'TransactionDetailCrudController');
    Route::crud('accountingaccounttype', 'AccountingAccountTypeCrudController');

    // Price List
    Route::crud('pricelist', 'PriceListCrudController');
    Route::put('pricelist/api/{id}', 'PriceListCrudController@updatePriceList')->name('price-list.api.update');
    Route::get('pricelist/api/{id}/products', 'PriceListCrudController@getProducts')->name('price-list.api.products');
}); // this should be the absolute last line of this file
