import CreditForm from "../views/commercials/creditForm.json";
import DebitForm from "../views/commercials/debitForm.json";
import SalesForm from "../views/commercials/salesForm.json";
import ReceivableForm from "../views/commercials/receivableForm.json";
import PayableForm from "../views/commercials/payableForm.json";
import PurchaseForm from "../views/commercials/purchaseForm.json";
import InventoryForm from "../views/commercials/inventoryForm.json";
import FixedAssetForm from "../views/commercials/fixedAssetForm.json";
import ImpexForm from "../views/commercials/impexForm.json";

import CycleForm from "../views/configs/cycleForm.json";
import DocumentForm from "../views/configs/documentForm.json";
import RateForm from "../views/configs/rateForm.json";

import MoneyMovementDebitForm from "../views/commercials/moneyMovementDebitForm.json";
import MoneyMovementForm from "../views/commercials/moneyMovementForm.json";
import JournalTemplateForm from "../views/accounts/templateForm.json";
import openingBalanceForm from "../views/accounts/openingBalanceForm.json";
import closingBalanceForm from "../views/accounts/closingBalanceForm.json";
import budgetForm from "../views/accounts/budgetForm.json";

import FourZeroFour from "../views/404";
import DashBoard from "../views/index";
import SearchResult from "../views/searchResult";
import Form from "../views/form";
import FormList from "../views/formList";
import List from "../views/list";

const Commercial = () => import("../views/commercials/index");
const SalesUpload = () => import("../views/commercials/salesUpload");

const Accounting = () => import("../views/accounts/index");
const JournalList = () => import("../views/accounts/journalList");
const JournalForm = () => import("../components/journalForm");

const ChartList = () => import("../views/accounts/chartList");
const ChartForm = () => import("../views/accounts/chartForm");

const Config = () => import("../views/configs/index");
const DocumentList = () => import("../views/configs/documentList");
const RateList = () => import("../views/configs/rateList");
const VersionList = () => import("../views/configs/versionList");
const VersionForm = () => import("../views/configs/versionForm");

const CommercialReports = () => import("../views/commercials/reports");
const AccountingReports = () => import("../views/accounts/reports");

export default [
    //This will cause 404 Errors to be redirected to proper site.
    {
        path: "",
        component: FourZeroFour
    },
    {
        path: "/:taxPayer/:cycle/",
        component: DashBoard,
        name: "taxPayer",
        meta: {
            url: "index",
            title: "Dashboard",
            description: "Some description",
            img: "/img/apps/dashboard.svg"
        }
    },
    {
        path: "/:taxPayer/:cycle/search/q={q}",
        component: SearchResult,
        name: "searchResult",
        meta: {
            url: "search",
            title: "Search",
            description: "",
            img: "/img/apps/search.svg"
        }
    },
    {
        path: "/:taxPayer/:cycle/commercial/",
        component: Commercial,
        name: "commercialMenu",
        meta: {
            title: "Dashboard",
            description: "Some description",
            img: "/img/apps/sales.svg"
        },
        children: [
            {
                path: "sales",
                component: List,
                name: "salesList",
                meta: {
                    apiUrl: "sales",
                    title: "commercial.salesBook",
                    description: "Some description",
                    img: "/img/apps/sales.svg",
                    columns: [
                        {
                            key: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner_name",
                            label: "commercial.customer",
                            formatter: (value, key, item) => {
                                return (
                                    item.partner_name.substring(0, 15) + "..."
                                );
                            },
                            sortable: true
                        },
                        {
                            key: "number",
                            label: "commercial.number",
                            sortable: true
                        },
                        {
                            key: "total",
                            label: "general.total",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.details.reduce(function(sum, row) {
                                        return sum + new Number(row["value"]);
                                    }, 0)
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: "upload",
                        component: SalesUpload,
                        name: "salesUpload",
                        meta: {
                            title: "commercial.salesInvoice",
                            img: "/img/apps/sales.svg"
                        }
                    },
                    {
                        path: ":id",
                        component: Form,
                        name: "salesForm",
                        meta: SalesForm
                    }
                ]
            },
            {
                path: "credit-notes",
                component: List,
                name: "creditList",
                meta: {
                    title: "commercial.creditBook",
                    description: "Some description",
                    img: "/img/apps/credit-note.svg",
                    columns: [
                        {
                            key: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner_name",
                            label: "commercial.customer",
                            formatter: (value, key, item) => {
                                return (
                                    item.partner_name.substring(0, 18) + "..."
                                );
                            },
                            sortable: true
                        },
                        {
                            key: "number",
                            label: "commercial.number",
                            sortable: true
                        },
                        {
                            key: "total",
                            label: "general.total",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.details.reduce(function(sum, row) {
                                        return sum + new Number(row["value"]);
                                    }, 0)
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "creditForm",
                        meta: CreditForm
                    }
                ]
            },
            {
                path: "purchases",
                component: List,
                name: "purchaseList",
                meta: {
                    title: "commercial.purchaseBook",
                    description: "Some description",
                    img: "/img/apps/purchase-v1.svg",
                    columns: [
                        {
                            key: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner_name",
                            label: "commercial.supplier",
                            sortable: true
                        },
                        {
                            key: "number",
                            label: "commercial.number",
                            sortable: true
                        },
                        {
                            key: "total",
                            label: "general.total",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.details.reduce(function(sum, row) {
                                        return sum + new Number(row["value"]);
                                    }, 0)
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "purchaseForm",
                        meta: PurchaseForm
                    }
                ]
            },
            {
                path: "debit-notes",
                component: List,
                name: "debitList",
                meta: {
                    title: "commercial.debitBook",
                    description: "Some description",
                    img: "/img/apps/credit-note.svg",
                    columns: [
                        {
                            key: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner_name",
                            label: "commercial.customer",
                            sortable: true
                        },
                        {
                            key: "number",
                            label: "commercial.number",
                            sortable: true
                        },
                        {
                            key: "total",
                            label: "general.total",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.details.reduce(function(sum, row) {
                                        return sum + new Number(row["value"]);
                                    }, 0)
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "debitForm",
                        meta: DebitForm
                    }
                ]
            },
            {
                path: "fixed-assets",
                component: List,
                name: "fixedAssetList",
                meta: {
                    title: "commercial.fixedAssets",
                    description: "Some description",
                    img: "/img/apps/fixed-asset.svg",
                    columns: [
                        {
                            key: "purchase_date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(
                                    item.purchase_date
                                ).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "serial",
                            label: "commercial.serial",
                            sortable: true
                        },
                        {
                            key: "name",
                            label: "commercial.name",
                            sortable: true
                        },
                        {
                            key: "current_value",
                            label: "commercial.value",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.current_value
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "fixedAssetForm",
                        meta: FixedAssetForm
                    }
                ]
            },
            {
                path: "money-transfers",
                component: Form,
                name: "moneyTransferForm",
                meta: MoneyMovementForm
                
            },
            {
                path: "money-movements",
                component: List,
                name: "moneyMovementList",
                meta: {
                    title: "commercial.moneyMovements",
                    description: "Some description",
                    img: "/img/apps/account-payable.svg",
                    columns: [
                        {
                            key: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "chart.name",
                            label: "commercial.account",
                            sortable: true
                        },
                        {
                            key: "comment",
                            label: "general.comment",
                            sortable: true
                        },
                        {
                            key: "currency.code",
                            label: "general.currency",
                            sortable: true
                        },
                        {
                            key: "debit",
                            label: "commercial.debit",
                            sortable: true
                        },
                        {
                            key: "credit",
                            label: "commercial.credit",
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "moneyMovementForm",
                        meta: MoneyMovementDebitForm
                    },
                    

                ]
            },
            {
                path: "inventories",
                component: List,
                name: "inventoryList",
                meta: {
                    title: "commercial.inventories",
                    description: "Some description",
                    img: "/img/apps/inventory.svg",
                    columns: [
                        {
                            key: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "start_date",
                            label: "commercial.startDate",
                            formatter: (value, key, item) => {
                                return new Date(
                                    item.start_date
                                ).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "end_date",
                            label: "commercial.endDate",
                            formatter: (value, key, item) => {
                                return new Date(
                                    item.end_date
                                ).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "inventory_value",
                            label: "commercial.value",
                            sortable: true
                        },
                        {
                            key: "comments",
                            label: "commercial.comment",
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "inventoryForm",
                        meta: InventoryForm
                    }
                ]
            },
            {
                path: "accounts-receivable",
                component: List,
                name: "receivableList",
                meta: {
                    title: "commercial.accountReceivables",
                    description: "Some description",
                    img: "/img/apps/account-receivable.svg",
                    columns: [
                        {
                            key: "date",
                            format: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner",
                            label: "commercial.customer",
                            formatter: (value, key, item) => {
                                return item.partner.substring(0, 15) + "...";
                            },
                            sortable: true
                        },
                        {
                            key: "number",
                            label: "commercial.number",
                            sortable: true
                        },
                        {
                            key: "credit",
                            label: "commercial.payment",
                            formatter: (value, key, item) => {
                                return new Number(item.credit).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "balance",
                            format: "numeric",
                            label: "commercial.balance",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.balance
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "receivableForm",
                        meta: ReceivableForm
                    }
                ]
            },
            {
                path: "accounts-payable",
                component: List,
                name: "payableList",
                meta: {
                    title: "commercial.accountPayables",
                    description: "Some description",
                    img: "/img/apps/account-payable.svg",
                    columns: [
                        {
                            key: "date",
                            format: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner",
                            label: "commercial.supplier",
                            formatter: (value, key, item) => {
                                return item.partner.substring(0, 15) + "...";
                            },
                            sortable: true
                        },
                        {
                            key: "number",
                            label: "commercial.number",
                            sortable: true
                        },
                        {
                            key: "payment",
                            label: "commercial.payment",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.payment
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "balance",
                            label: "commercial.balance",
                            formatter: (value, key, item) => {
                                return new Number(
                                    item.balance
                                ).toLocaleString();
                            },
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "payableForm",
                        meta: PayableForm
                    }
                ]
            },
            {
                path: "impexes",
                component: List,
                name: "impexList",
                meta: {
                    title: "commercial.impex",
                    description: "Some description",
                    img: "/img/apps/impex.svg",
                    columns: [
                        {
                            key: "date",
                            format: "date",
                            label: "commercial.date",
                            formatter: (value, key, item) => {
                                return new Date(item.date).toLocaleDateString();
                            },
                            sortable: true
                        },
                        {
                            key: "partner",
                            label: "commercial.supplier",
                            formatter: (value, key, item) => {
                                return item.partner.substring(0, 15) + "...";
                            },
                            sortable: true
                        },
                        {
                            key: "code",
                            label: "commercial.code",
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "impexForm",
                        meta: ImpexForm
                    }
                ]
            }
        ]
    },
    {
        path: "/:taxPayer/:cycle/accounting/",
        component: Accounting,
        name: "accountingMenu",
        meta: {
            title: "Accounting",
            description: "All your accounting data is here"
        },
        children: [
            {
                path: "journal-templates",
                component: List,
                name: "journalTemplateList",
                meta: {
                    title: "accounting.journal",
                    description: "Some description",
                    img: "/img/apps/journals.svg",
                    columns: [
                        {
                            key: "name",
                            sortable: true
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "journalTemplateForm",
                        meta: JournalTemplateForm
                    }
                ]
            },
            {
                path: "journals",
                component: JournalList,
                name: "journalList",
                meta: {
                    title: "accounting.journal",
                    description: "Some description",
                    img: "/img/apps/journals.svg"
                },
                children: [
                    {
                        path: ":id",
                        component: JournalForm,
                        name: "journalForm",
                        meta: {
                            title: "accounting.journal",
                            img: "/img/apps/journals.svg"
                        }
                    }
                ]
            },

            {
                path: "opening-balance",
                component: FormList,
                name: "openingBalanceForm",
                meta: openingBalanceForm
            },
            {
                path: "closing-balance",
                component: FormList,
                name: "closingBalanceForm",
                meta: closingBalanceForm
            },
            {
                path: "budget",
                component: FormList,
                name: "budgetForm",
                meta: budgetForm
            },
            {
                path: "charts",
                component: ChartList,
                name: "chartList",
                meta: {
                    title: "accounting.chartOfAccounts",
                    description: "Some description",
                    img: "/img/apps/chart-of-accounts.svg"
                },
                children: [
                    {
                        path: ":id",
                        component: ChartForm,
                        name: "chartForm",
                        meta: {
                            title: "Chart Form",
                            img: "/img/apps/chart-of-accounts.svg"
                        }
                    }
                ]
            }
        ]
    },
    {
        path: "/:taxPayer/:cycle/config/",
        component: Config,
        name: "configMenu",
        meta: {
            title: "Dashboard",
            description: "Some description",
            img: "/img/apps/sales.svg"
        },
        children: [
            {
                path: "chart-versions",
                component: VersionList,
                name: "versionList",
                meta: {
                    title: "accounting.chartVersion",
                    description: "Some description",
                    img: "/img/apps/sales.svg"
                },
                children: [
                    {
                        path: ":id",
                        component: VersionForm,
                        name: "versionForm",
                        meta: {
                            title: "Version Form"
                        }
                    }
                ]
            },
            {
                path: "cycles",
                component: List,
                name: "cycleList",
                meta: {
                    title: "accounting.fiscalYear",
                    description: "Some description",
                    img: "/img/apps/sales.svg",
                    columns: [
                        {
                            key: "chart_version.name",
                            sortable: true
                        },
                        {
                            key: "year",
                            sortable: false
                        },
                        {
                            key: "actions",
                            label: "",
                            sortable: false
                        }
                    ]
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "cycleForm",
                        meta: CycleForm
                    }
                ]
            },
            {
                path: "documents",
                component: DocumentList,
                name: "documentList",
                meta: {
                    title: "commercial.documents",
                    description: "Some description",
                    img: "/img/apps/sales.svg"
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "documentForm",
                        meta: DocumentForm
                    }
                ]
            },
            {
                path: "rates",
                component: RateList,
                name: "rateList",
                meta: {
                    title: "commercial.exchangeRates",
                    description: "Some description",
                    img: "/img/apps/sales.svg"
                },
                children: [
                    {
                        path: ":id",
                        component: Form,
                        name: "rateForm",
                        meta: RateForm
                    }
                ]
            }
        ]
    },
    {
        path: "/:taxPayer/:cycle/commercial/reports",
        component: CommercialReports,
        name: "commercialReports",
        meta: {
            title: "Commercial Reports",
            description: "All your accounting data is here",
            img: "/img/apps/sales.svg"
        }
    },
    {
        path: "/:taxPayer/:cycle/accounting/reports",
        component: AccountingReports,
        name: "accountingReports",
        meta: {
            title: "Accounting Reports",
            description: "All your accounting data is here",
            img: "/img/apps/sales.svg"
        }
    }
];
