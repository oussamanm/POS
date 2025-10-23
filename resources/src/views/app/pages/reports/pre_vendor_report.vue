<template>
    <div class="main-content">
        <breadcumb page="Rapport des Pre-Vendeurs" :folder="$t('Reports')" />
        <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>

        <b-row class="justify-content-center mb-5" v-if="!isLoading">
            <!-- Pre-Vendeurs -->
            <b-col lg="3" md="6" sm="12">
                <b-form-group label="Pre-Vendeurs">
                    <v-select @input="Selected_PreVendeur" v-model="Filter_preVendeur" :reduce="label => label.value"
                        placeholder="Pre-Vendeurs"
                        :options="preVendeurs.map(preVendeurs => ({ label: preVendeurs.username, value: preVendeurs.id }))" />
                </b-form-group>
            </b-col>

            <!-- Date -->
            <b-col lg="3" md="6" sm="12" v-if="!isLoading">
                <b-form-group label="Date">
                    <date-range-picker v-model="dateRange" :startDate="startDate" :endDate="endDate"
                        @update="Submit_filter_dateRange" :locale-data="locale">
                        <template v-slot:input="picker" style="min-width: 350px;">
                            {{ picker.startDate.toJSON().slice(0, 10) }} - {{ picker.endDate.toJSON().slice(0, 10) }}
                        </template>
                    </date-range-picker>
                </b-form-group>
            </b-col>

        </b-row>

        <b-row v-if="!isLoading">
            <!-- ICON BG -->
            <b-col lg="3" md="6" sm="12">
                <b-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">
                    <i class="i-Full-Cart"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">{{ $t('Sales') }}</p>
                        <p class="text-primary text-24 line-height-1 mb-2">{{ total.sales }}</p>
                    </div>
                </b-card>
            </b-col>

            <b-col lg="3" md="6" sm="12">
                <b-card class="card-icon-bg card-icon-bg-primary o-hidden mb-30 text-center">
                    <i class="i-Full-Cart"></i>
                    <div class="content">
                        <p class="text-muted mt-2 mb-0">Total des Ventes ({{ total.sales }})</p>
                        <p class="text-primary text-24 line-height-1 mb-2">{{ total.totalSales }} DH</p>
                    </div>
                </b-card>
            </b-col>
        </b-row>

        <b-row v-if="!isLoading">

            <b-col md="12">
                <b-card no-body class="card mb-30" header-bg-variant="transparent ">
                    <b-tabs active-nav-item-class="nav nav-tabs" content-class="mt-3">

                        <!-- Sales Table -->
                        <b-tab :title="$t('Sales')">
                            <vue-good-table mode="remote" :columns="columns_sales" :totalRows="totalRows_sales"
                                :rows="sales" @on-page-change="PageChangeSales"
                                @on-per-page-change="onPerPageChangeSales" @on-search="onSearch_Sales" :search-options="{
                                    placeholder: $t('Search_this_table'),
                                    enabled: true,
                                }" :pagination-options="{
                    enabled: true,
                    mode: 'records',
                    nextLabel: 'next',
                    prevLabel: 'prev',
                }" styleClass="order-table vgt-table mt-2">
                                <div slot="table-actions" class="mt-2 mb-3">
                                    <b-button @click="Sales_PDF()" size="sm" variant="outline-success ripple m-1">
                                        <i class="i-File-Copy"></i> PDF
                                    </b-button>
                                </div>
                                <template slot="table-row" slot-scope="props">
                                    <div v-if="props.column.field == 'statut'">
                                        <span v-if="props.row.statut == 'completed'"
                                            class="badge badge-outline-success">{{ $t('complete') }}</span>
                                        <span v-else-if="props.row.statut == 'pending'"
                                            class="badge badge-outline-info">{{ $t('Pending') }}</span>
                                        <span v-else class="badge badge-outline-warning">{{ $t('Ordered') }}</span>
                                    </div>
                                    <div v-else-if="props.column.field == 'payment_status'">
                                        <span v-if="props.row.payment_status == 'paid'"
                                            class="badge badge-outline-success">{{ $t('Paid') }}</span>
                                        <span v-else-if="props.row.payment_status == 'partial'"
                                            class="badge badge-outline-primary">Partial</span>
                                        <span v-else class="badge badge-outline-warning">{{ $t('Unpaid') }}</span>
                                    </div>
                                    <div v-else-if="props.column.field == 'shipping_status'">
                                        <span v-if="props.row.shipping_status == 'ordered'"
                                            class="badge badge-outline-warning">{{ $t('Ordered') }}</span>

                                        <span v-else-if="props.row.shipping_status == 'packed'"
                                            class="badge badge-outline-info">{{ $t('Packed') }}</span>

                                        <span v-else-if="props.row.shipping_status == 'shipped'"
                                            class="badge badge-outline-secondary">{{ $t('Shipped') }}</span>

                                        <span v-else-if="props.row.shipping_status == 'delivered'"
                                            class="badge badge-outline-success">{{ $t('Delivered') }}</span>

                                        <span v-else-if="props.row.shipping_status == 'cancelled'"
                                            class="badge badge-outline-danger">{{ $t('Cancelled') }}</span>
                                    </div>
                                    <div v-else-if="props.column.field == 'Ref'">
                                        <router-link :to="'/app/sales/detail/' + props.row.id">
                                            <span class="ul-btn__text ml-1">{{ props.row.Ref }}</span>
                                        </router-link>
                                    </div>
                                </template>
                            </vue-good-table>
                        </b-tab>

                        <!-- Returns Sale Table -->
                        <b-tab :title="$t('SalesReturn')">
                            <vue-good-table mode="remote" :columns="columns_returns_sale"
                                :totalRows="totalRows_Return_sale" :rows="returns_sale"
                                @on-page-change="PageChangeReturn_Customer"
                                @on-per-page-change="onPerPageChangeReturn_Sale" :pagination-options="{
                                    enabled: true,
                                    mode: 'records',
                                    nextLabel: 'next',
                                    prevLabel: 'prev',
                                }" @on-search="onSearch_Return_Sale" :search-options="{
                    placeholder: $t('Search_this_table'),
                    enabled: true,
                }" styleClass="order-table vgt-table mt-2">
                                <div slot="table-actions" class="mt-2 mb-3">
                                    <b-button @click="Sale_Return_PDF()" size="sm" variant="outline-success ripple m-1">
                                        <i class="i-File-Copy"></i> PDF
                                    </b-button>
                                </div>
                                <template slot="table-row" slot-scope="props">
                                    <div v-if="props.column.field == 'statut'">
                                        <span v-if="props.row.statut == 'received'"
                                            class="badge badge-outline-success">{{ $t('Received') }}</span>
                                        <span v-else class="badge badge-outline-info">{{ $t('Pending') }}</span>
                                    </div>
                                    <div v-else-if="props.column.field == 'Ref'">
                                        <router-link :to="'/app/sale_return/detail/' + props.row.id">
                                            <span class="ul-btn__text ml-1">{{ props.row.Ref }}</span>
                                        </router-link>
                                    </div>
                                    <div v-else-if="props.column.field == 'sale_ref' && props.row.sale_id">
                                        <router-link :to="'/app/sales/detail/' + props.row.sale_id">
                                            <span class="ul-btn__text ml-1">{{ props.row.sale_ref }}</span>
                                        </router-link>
                                    </div>
                                </template>
                            </vue-good-table>
                        </b-tab>

                    </b-tabs>
                </b-card>
            </b-col>
        </b-row>

    </div>
</template>


<script>
import { mapActions, mapGetters } from "vuex";
import moment from 'moment';

import jsPDF from "jspdf";
import "jspdf-autotable";
import DateRangePicker from 'vue2-daterange-picker'
import 'vue2-daterange-picker/dist/vue2-daterange-picker.css'

export default {
    components: {
        DateRangePicker,
    },
    metaInfo: {
        // if no subcomponents specify a metaInfo.title, this title will be used
        title: "Rapport des Pre-Vendeurs"
    },
    data() {
        return {
            today_mode: true,
            startDate: "",
            endDate: "",
            dateRange: {
                startDate: "",
                endDate: ""
            },
            locale: {
                //separator between the two ranges apply
                Label: "Apply",
                cancelLabel: "Cancel",
                weekLabel: "W",
                customRangeLabel: "Custom Range",
                daysOfWeek: moment.weekdaysMin(),
                //array of days - see moment documenations for details
                monthNames: moment.monthsShort(), //array of month names - see moment documenations for details
                firstDay: 1 //ISO first day of week - see moment documenations for details
            },

            preVendeurs: [],

            Stock_Count: {},
            Stock_value: {},

            totalRows_sales: "",
            totalRows_Return_sale: "",
            totalSummary: "",

            limit_returns_Sale: "10",
            limit_sales: "10",

            search_sale: "",
            search_return_Sale: "",

            sales_page: 1,
            Return_sale_page: 1,

            isLoading: true,
            Filter_preVendeur: "",

            sales: [],
            returns_sale: [],


            payments: [],
            selectedRef: [],
            totalSummaryPayments: "",

            total: {
                sales: "",
                ReturnSale: ""
            }
        };
    },

    computed: {
        ...mapGetters(["currentUser"]),

        columns_sales() {
            return [
                {
                    label: this.$t("Reference"),
                    field: "Ref",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: "Date",
                    field: "date",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("Customer"),
                    field: "client_name",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("warehouse"),
                    field: "warehouse_name",
                    tdClass: "text-left",
                    thClass: "text-left"
                },

                {
                    label: this.$t("Total"),
                    field: "GrandTotal",
                    type: "decimal",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("Paid"),
                    field: "paid_amount",
                    type: "decimal",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("Due"),
                    field: "due",
                    type: "decimal",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("Status"),
                    field: "statut",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("PaymentStatus"),
                    field: "payment_status",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: "Statut de Livraison",
                    field: "shipping_status",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
            ];
        },
        columns_returns_sale() {
            return [
                {
                    label: this.$t("Reference"),
                    field: "Ref",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("Customer"),
                    field: "client_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("Sale_Ref"),
                    field: "sale_ref",
                    tdClass: "text-left",
                    thClass: "text-left"
                },
                {
                    label: this.$t("warehouse"),
                    field: "warehouse_name",
                    tdClass: "text-left",
                    thClass: "text-left"
                },

                {
                    label: this.$t("Total"),
                    field: "GrandTotal",
                    type: "decimal",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                },
                {
                    label: this.$t("Status"),
                    field: "statut",
                    html: true,
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false
                }
            ];
        },

    },

    methods: {
        //----------------------------------------- Sales Return PDF -----------------------\\
        Sale_Return_PDF() {
            var self = this;

            let pdf = new jsPDF("p", "pt");
            let columns = [
                { title: "Ref", dataKey: "Ref" },
                { title: "Client", dataKey: "client_name" },
                { title: "sale_ref", dataKey: "sale_ref" },
                { title: "Warehouse", dataKey: "warehouse_name" },
                { title: "Total", dataKey: "GrandTotal" },
                { title: "Paid", dataKey: "paid_amount" },
                { title: "Due", dataKey: "due" },
                { title: "Status", dataKey: "statut" },
                { title: "Status Payment", dataKey: "payment_status" }
            ];
            pdf.autoTable(columns, self.returns_sale);
            pdf.text("Sales Return List", 40, 25);
            pdf.save("Sales Return.pdf");
        },

        //----------------------------------- Sales PDF ------------------------------\\
        Sales_PDF() {
            var self = this;
            let pdf = new jsPDF("p", "pt");
            let columns = [
                { title: "Ref", dataKey: "Ref" },
                { title: "Client", dataKey: "client_name" },
                { title: "Warehouse", dataKey: "warehouse_name" },
                { title: "Status", dataKey: "statut" },
                { title: "Total", dataKey: "GrandTotal" },
                { title: "Paid", dataKey: "paid_amount" },
                { title: "Due", dataKey: "due" },
                { title: "Status Payment", dataKey: "payment_status" },
                { title: "Shipping Status", dataKey: "shipping_status" }
            ];
            pdf.autoTable(columns, self.sales);
            pdf.text("Liste des ventes", 40, 25);
            pdf.save("Sale_List.pdf");
        },

        //------------------------------Formetted Numbers -------------------------\\
        formatNumber(number, dec) {
            const value = (typeof number === "string"
                ? number
                : number.toString()
            ).split(".");
            if (dec <= 0) return value[0];
            let formated = value[1] || "";
            if (formated.length > dec)
                return `${value[0]}.${formated.substr(0, dec)}`;
            while (formated.length < dec) formated += "0";
            return `${value[0]}.${formated}`;
        },

        //---------------------- Event Select PreVendeur ------------------------------\\
        Selected_PreVendeur(value) {
            this.isLoading = true;
            if (value === null) {
                this.Filter_preVendeur = "";
            }
            this.Get_Reports();

            this.Get_Sales(1);
            this.Get_Returns_Sale(1);

            setTimeout(() => {
                this.isLoading = false;
            }, 1000);
        },


        //------------------------------ Show Reports -------------------------\\
        Get_Reports() {
            axios
                .get("report/prevendeur_report?preVendeur_id=" + this.Filter_preVendeur + "&to=" + this.endDate + "&from=" + this.startDate)
                .then(response => {
                    this.total = response.data.data;
                    if (response.data.preVendeurs.length)
                    {
                        this.preVendeurs = response.data.preVendeurs;
                        if (this.Filter_preVendeur == "")
                        {
                            this.Filter_preVendeur = response.data.preVendeurs[0].id;
                            this.Selected_PreVendeur()
                        }
                    }
                })
                .catch(response => { });
        },

        //--------------------------- Sales Event Page Change  -------------\\
        PageChangeSales({ currentPage }) {
            if (this.sales_page !== currentPage) {
                this.Get_Sales(currentPage);
            }
        },

        //--------------------------- Limit Page Sales -------------\\
        onPerPageChangeSales({ currentPerPage }) {
            if (this.limit_sales !== currentPerPage) {
                this.limit_sales = currentPerPage;
                this.Get_Sales(1);
            }
        },

        onSearch_Sales(value) {
            this.search_sale = value.searchTerm;
            this.Get_Sales(1);
        },

        //--------------------------- Get sales By warehouse -------------\\
        Get_Sales(page) {
            axios
                .get(
                    "report/sales_user?page=" +
                    page +
                    "&limit=" +
                    this.limit_sales +
                    "&user_id=" +
                    this.Filter_preVendeur +
                    "&search=" +
                    this.search_sale +
                    "&to=" +
                    this.endDate +
                    "&from=" +
                    this.startDate
                )
                .then(response => {
                    this.sales = response.data.sales;
                    this.totalRows_sales = response.data.totalRows;
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                })
                .catch(response => {
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },


        //--------------------------- Event Page Change -------------\\
        PageChangeReturn_Customer({ currentPage }) {
            if (this.Return_sale_page !== currentPage) {
                this.Get_Returns_Sale(currentPage);
            }
        },

        PageChangeStock({ currentPage }) {
            if (this.Stock_page !== currentPage) {
                this.Get_Stock(currentPage);
            }
        },

        //--------------------------- Limit Page Returns Sale -------------\\
        onPerPageChangeReturn_Sale({ currentPerPage }) {
            if (this.limit_returns_Sale !== currentPerPage) {
                this.limit_returns_Sale = currentPerPage;
                this.Get_Returns_Sale(1);
            }
        },

        onSearch_Return_Sale(value) {
            this.search_return_Sale = value.searchTerm;
            this.Get_Returns_Sale(1);
        },

        //--------------------------- Get Returns Sale By warehouse -------------\\
        Get_Returns_Sale(page) {
            axios
                .get(
                    "report/returns_sale_user?page=" +
                    page +
                    "&limit=" +
                    this.limit_returns_Sale +
                    "&user_id=" +
                    this.Filter_preVendeur +
                    "&search=" +
                    this.search_return_Sale +
                    "&to=" +
                    this.endDate +
                    "&from=" +
                    this.startDate
                )
                .then(response => {
                    this.returns_sale = response.data.returns_sale;
                    this.totalRows_Return_sale = response.data.totalRows;
                })
                .catch(response => { });
        },

        //------ Toast
        makeToast(variant, msg, title) {
            this.$root.$bvToast.toast(msg, {
                title: title,
                variant: variant,
                solid: true
            });
        },


        //----------------------------- oussama Submit Date Picker -------------------\\
        Submit_filter_dateRange() {
            var self = this;

            self.startDate = self.dateRange.startDate.toJSON().slice(0, 10);
            self.endDate = self.dateRange.endDate.toJSON().slice(0, 10);

            // Load data
            this.isLoading = true;

            this.Get_Reports();

            this.Get_Sales(1);
            this.Get_Returns_Sale(1);

            setTimeout(() => {
                this.isLoading = false;
            }, 1000);

        },

        get_date_loaded() {
            var self = this;
            if (self.today_mode) {
                let today = new Date()

                self.startDate = today.getFullYear();
                self.endDate = new Date().toJSON().slice(0, 10);

                self.dateRange.startDate = today.getFullYear();
                self.dateRange.endDate = new Date().toJSON().slice(0, 10);

            }
        }


    }, //end Methods

    //----------------------------- Created function------------------- \\

    created: function () {

        this.Get_Reports();
        this.Get_Sales(1);
        // this.Get_Returns_Sale(1);

        this.get_date_loaded();
    }
};
</script>
