<template>
    <div class="main-content">
        <breadcumb :page="$t('SalesInvoice')" :folder="$t('Reports')" />

        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>

        <b-col md="12" class="text-center" v-if="!isLoading">
            <date-range-picker
                v-model="dateRange"
                :startDate="startDate"
                :endDate="endDate"
                @update="Submit_filter_dateRange"
                :locale-data="locale"
            >
                <template v-slot:input="picker" style="min-width: 350px">
                    {{ picker.startDate.toJSON().slice(0, 10) }} -
                    {{ picker.endDate.toJSON().slice(0, 10) }}
                </template>
            </date-range-picker>
        </b-col>

        <b-card class="wrapper" v-if="!isLoading">
            <vue-good-table
                mode="remote"
                :columns="columns"
                :totalRows="totalRows"
                :rows="rows"
                :group-options="{
                    enabled: true,
                    headerPosition: 'bottom',
                }"
                @on-page-change="onPageChange"
                @on-per-page-change="onPerPageChange"
                @on-sort-change="onSortChange"
                @on-search="onSearch"
                :search-options="{
                    placeholder: $t('Search_this_table'),
                    enabled: true,
                }"
                :pagination-options="{
                    enabled: true,
                    mode: 'records',
                    nextLabel: 'next',
                    prevLabel: 'prev',
                }"
                styleClass="table-hover tableOne vgt-table"
            >
                <div slot="table-actions" class="mt-2 mb-3">
                    <b-button
                        variant="outline-info ripple m-1"
                        size="sm"
                        v-b-toggle.sidebar-right
                    >
                        <i class="i-Filter-2"></i>
                        {{ $t("Filter") }}
                    </b-button>
                    <b-button
                        @click="Payment_PDF()"
                        size="sm"
                        variant="outline-success ripple m-1"
                    >
                        <i class="i-File-Copy"></i> PDF
                    </b-button>
                    <vue-excel-xlsx
                        class="btn btn-sm btn-outline-danger ripple m-1"
                        :data="payments"
                        :columns="columns"
                        :file-name="'payments'"
                        :file-type="'xlsx'"
                        :sheet-name="'payments'"
                    >
                        <i class="i-File-Excel"></i> EXCEL
                    </vue-excel-xlsx>
                </div>

                <template slot="table-row" slot-scope="props">
                    <span v-if="props.column.field == 'actions'">
                        <!-- <a v-b-tooltip.hover title="Applique stock" class="cursor-pointer" @click=""> -->
                        <a
                            v-b-tooltip.hover
                            title="ventes de paiement"
                            class="cursor-pointer"
                            @click="Show_Payments(props.row.id)"
                        >
                            <i class="i-Eye text-25 text-info"></i>
                        </a>

                        <!--
                <b-dropdown-item
                  @click="Show_Payments(props.row)"
                >
                  <i class="nav-icon i-Eye font-weight-bold mr-2"></i>
                  {{$t('Customer_details')}}
                </b-dropdown-item>
                 -->
                    </span>

                    <div v-else-if="props.column.field == 'payment_received'">
                        <span
                          v-if="props.row.payment_received == '1'"
                          class="badge badge-outline-success"
                        >
                            Encaissé
                        </span>

                        <span
                          v-else-if="props.row.payment_received == '0'"
                          class="badge badge-outline-warning"
                        >
                            Non Encaissé
                        </span>
                    </div>
                </template>
            </vue-good-table>
        </b-card>

        <!-- Sidebar Filter -->
        <b-sidebar
            id="sidebar-right"
            :title="$t('Filter')"
            bg-variant="white"
            right
            shadow
        >
            <div class="px-3 py-2">
                <b-row>
                    <!-- Reference -->
                    <b-col md="12">
                        <b-form-group :label="$t('Reference')">
                            <b-form-input
                                label="Reference"
                                :placeholder="$t('Reference')"
                                v-model="Filter_Ref"
                            ></b-form-input>
                        </b-form-group>
                    </b-col>

                    <!-- User -->
                    <b-col md="12">
                        <b-form-group :label="$t('User')">
                            <v-select
                                :reduce="(label) => label.value"
                                :placeholder="$t('Choose_Customer')"
                                v-model="Filter_user"
                                :options="
                                    users.map((user) => ({
                                        label: user.name,
                                        value: user.id,
                                    }))
                                "
                            />
                        </b-form-group>
                    </b-col>

                    <!-- Customers  -->
                    <b-col md="12">
                        <b-form-group :label="$t('Customer')">
                            <v-select
                                :reduce="(label) => label.value"
                                :placeholder="$t('Choose_Customer')"
                                v-model="Filter_client"
                                :options="
                                    clients.map((clients) => ({
                                        label: clients.name,
                                        value: clients.id,
                                    }))
                                "
                            />
                        </b-form-group>
                    </b-col>

                    <!-- Sale  -->
                    <!-- <b-col md="12">
                        <b-form-group :label="$t('Sale')">
                        <v-select
                            :reduce="label => label.value"
                            :placeholder="$t('PleaseSelect')"
                            v-model="Filter_sale"
                            :options="sales.map(sales => ({label: sales.Ref, value: sales.id}))"
                        />
                        </b-form-group>
                    </b-col> -->

                    <!-- Payment choice -->
                    <b-col md="12">
                        <b-form-group :label="$t('Paymentchoice')">
                            <v-select
                                v-model="Filter_Reg"
                                :reduce="(label) => label.value"
                                :placeholder="$t('PleaseSelect')"
                                :options="[
                                    { label: 'Cash', value: 'Cash' },
                                    { label: 'Cheque', value: 'cheque' },
                                    {label: 'Bank transfer',value: 'bank transfer',},
                                    {label: 'Credit card',value: 'credit card',},
                                    { label: 'Other', value: 'other' },
                                ]"
                            ></v-select>
                        </b-form-group>
                    </b-col>

                    <b-col md="12">
                        <b-form-group label="Encaissement">
                            <v-select
                                v-model="Filter_encaissement"
                                :reduce="(label) => label.value"
                                :placeholder="$t('PleaseSelect')"
                                :options="[
                                    { label: 'Encaissé', value: 1 },
                                    { label: 'Non Encaissé', value: 0 },
                                ]"
                            ></v-select>
                        </b-form-group>
                    </b-col>

                    <b-col md="6" sm="12">
                        <b-button
                            @click="Payments_Sales(serverParams.page)"
                            variant="primary ripple m-1"
                            size="sm"
                            block
                        >
                            <i class="i-Filter-2"></i>
                            {{ $t("Filter") }}
                        </b-button>
                    </b-col>
                    <b-col md="6" sm="12">
                        <b-button
                            @click="Reset_Filter()"
                            variant="danger ripple m-1"
                            size="sm"
                            block
                        >
                            <i class="i-Power-2"></i>
                            {{ $t("Reset") }}
                        </b-button>
                    </b-col>
                </b-row>
            </div>
        </b-sidebar>

        <!-- Modal Show Sales-->
        <b-modal
            hide-footer
            size="lg"
            id="Show_sales"
            title="Ventes de Paiement"
        >
            <b-row>
                <b-col lg="12" md="12" sm="12" class="mt-3">
                    <div class="table-responsive">
                        <table
                            class="table table-hover table-bordered table-md"
                        >
                            <thead>
                                <tr>
                                    <th scope="col">{{ $t("date") }}</th>
                                    <th scope="col">{{ $t("Reference") }}</th>
                                    <th scope="col">{{ $t("Amount") }}</th>
                                    <th scope="col">Montant Payé</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="sales.length <= 0">
                                    <td colspan="5">
                                        {{ $t("NodataAvailable") }}
                                    </td>
                                </tr>
                                <tr v-for="sale in sales">
                                    <td>{{ sale.date }}</td>
                                    <td>{{ sale.Ref }}</td>
                                    <td>{{ sale.GrandTotal }}</td>
                                    <td>{{ sale.paid_amount }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </b-col>
            </b-row>
        </b-modal>
    </div>
</template>

<script>
import NProgress from "nprogress";
import jsPDF from "jspdf";
import "jspdf-autotable";
import DateRangePicker from "vue2-daterange-picker";
//you need to import the CSS manually
import "vue2-daterange-picker/dist/vue2-daterange-picker.css";
import moment from "moment";

export default {
    metaInfo: {
        title: "Payment Sales",
    },
    components: { DateRangePicker },

    data() {
        return {
            isLoading: true,
            serverParams: {
                sort: {
                    field: "id",
                    type: "desc",
                },
                page: 1,
                perPage: 10,
            },
            limit: "10",
            search: "",
            totalRows: "",
            Filter_client: "",
            Filter_Ref: "",
            Filter_user: "",
            Filter_sale: "",
            Filter_Reg: "",
            Filter_encaissement: "",
            payments: [],
            clients: [],
            users: [],
            rows: [
                {
                    Reglement: "Total",
                    children: [],
                },
            ],
            sales: [],
            today_mode: true,
            startDate: "",
            endDate: "",
            dateRange: {
                startDate: "",
                endDate: "",
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
                firstDay: 1, //ISO first day of week - see moment documenations for details
            },
        };
    },

    computed: {
        columns() {
            return [
                {
                    label: this.$t("date"),
                    field: "date",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Reference"),
                    field: "Ref",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                // {
                //   label: this.$t("Sale"),
                //   field: "Ref_Sale",
                //   tdClass: "text-left",
                //   thClass: "text-left"
                // },
                {
                    label: this.$t("Customer"),
                    field: "client_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: "Utilisateur",
                    field: "user_name",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("ModePaiement"),
                    field: "Reglement",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: "Total",
                    field: "total_payment",
                    tdClass: "text-right",
                    thClass: "text-right",
                },
                {
                    label: "Note",
                    field: "notes",
                    type: "string",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false,
                },
                {
                    label: "Encaissement",
                    field: "payment_received",
                    html: true,
                    headerField: this.payment_received,
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: "Ventes de Paiement",
                    field: "actions",
                    tdClass: "text-right",
                    thClass: "text-right",
                    sortable: false,
                },
            ];
        },
    },
    methods: {
        sumCount(rowObj) {
            let sum = 0;
            for (let i = 0; i < rowObj.children.length; i++) {
                sum += rowObj.children[i].montant;
            }
            return sum;
        },

        //---- update Params Table
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },

        //---- Event Page Change
        onPageChange({ currentPage }) {
            if (this.serverParams.page !== currentPage) {
                this.updateParams({ page: currentPage });
                this.Payments_Sales(currentPage);
            }
        },

        //---- Event Per Page Change
        onPerPageChange({ currentPerPage }) {
            if (this.limit !== currentPerPage) {
                this.limit = currentPerPage;
                this.updateParams({ page: 1, perPage: currentPerPage });
                this.Payments_Sales(1);
            }
        },

        //---- Event on Sort Change
        onSortChange(params) {
            let field = "";
            if (params[0].field == "Ref_Sale") {
                field = "sale_id";
            } else {
                field = params[0].field;
            }
            this.updateParams({
                sort: {
                    type: params[0].type,
                    field: field,
                },
            });
            this.Payments_Sales(this.serverParams.page);
        },

        //---- Event on Search
        onSearch(value) {
            this.search = value.searchTerm;
            this.Payments_Sales(this.serverParams.page);
        },

        //------ Reset Filter
        Reset_Filter() {
            this.search = "";
            this.Filter_client = "";
            this.Filter_Ref = "";
            this.Filter_user = "";
            this.Filter_sale = "";
            this.Filter_Reg = "";
            this.Filter_encaissement = "";
            this.Payments_Sales(this.serverParams.page);
        },

        //---------------------------------------- Set To Strings-------------------------\\
        setToStrings() {
            // Simply replaces null values with strings=''
            if (this.Filter_client === null) this.Filter_client = "";
            else if (this.Filter_sale === null) this.Filter_sale = "";
        },

        //------------------------ Payment Sales PDF -----------------------\\
        Payment_PDF() {
            var self = this;

            let pdf = new jsPDF("p", "pt");
            let columns = [
                { title: this.$t("Date"), dataKey: "date" },
                { title: this.$t("Ref"), dataKey: "Ref" },
                //{ title: this.$t("Sale"), dataKey: "Ref_Sale" },
                { title: this.$t("Client"), dataKey: "client_name" },
                { title: this.$t("ModePaiement"), dataKey: "Reglement" },
                { title: this.$t("Amount"), dataKey: "montant" },
            ];

            // Calculate the total amount
            let totalAmount = self.payments.reduce((total, payment) => {
                return total + payment.montant;
            }, 0);

            pdf.autoTable(columns, self.payments);
            pdf.text("Paiements Liste des Ventes", 40, 25);
            pdf.setFontSize(15);
            pdf.text(
                `Total des Paiements des Ventes: ${totalAmount} DH`,
                40,
                pdf.autoTable.previous.finalY + 50
            ); // Adjust the Y position as needed

            pdf.save("Paiements_Liste_des_Ventes.pdf");
        },

        //----------------------------- Submit Date Picker -------------------\\
        Submit_filter_dateRange() {
            var self = this;
            self.startDate = self.dateRange.startDate.toJSON().slice(0, 10);
            self.endDate = self.dateRange.endDate.toJSON().slice(0, 10);
            self.Payments_Sales(1);
        },

        get_data_loaded() {
            var self = this;
            if (self.today_mode) {
                let today = new Date();

                self.startDate = today.getFullYear();
                self.endDate = new Date().toJSON().slice(0, 10);

                self.dateRange.startDate = today.getFullYear();
                self.dateRange.endDate = new Date().toJSON().slice(0, 10);
            }
        },

        //-------------------------------- Get All Payments Sales ---------------------\\
        Payments_Sales(page) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            this.setToStrings();
            this.get_data_loaded();

            axios
                .get(
                    "payment_sale?page=" +
                        page +
                        "&Ref=" +
                        this.Filter_Ref +
                        "&user_id=" +
                        this.Filter_user +
                        "&client_id=" +
                        this.Filter_client +
                        "&sale_id=" +
                        this.Filter_sale +
                        "&Reglement=" +
                        this.Filter_Reg +
                        "&payment_received=" +
                        this.Filter_encaissement +
                        "&SortField=" +
                        this.serverParams.sort.field +
                        "&SortType=" +
                        this.serverParams.sort.type +
                        "&search=" +
                        this.search +
                        "&limit=" +
                        this.limit +
                        "&to=" +
                        this.endDate +
                        "&from=" +
                        this.startDate
                )

                .then((response) => {
                    this.payments = response.data.payments;
                    this.clients = response.data.clients;
                    this.users = response.data.users;
                    //   this.sales = response.data.sales;
                    this.totalRows = response.data.totalRows;
                    this.rows[0].children = this.payments;
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.isLoading = false;
                    this.today_mode = false;
                })
                .catch((response) => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    setTimeout(() => {
                        this.isLoading = false;
                        this.today_mode = false;
                    }, 500);
                });
        },

        //----------------------------------- Show Details Client -------------------------------\\
        Show_Payments(id) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            this.sales = [];
            this.Get_Payments(id);
        },

        //----------------------------------------- Get All Payments  -------------------------------\\
        Get_Payments(id) {
            axios
                .get("get_sales_by_payment/" + id)
                .then((response) => {
                    console.log("---------- sales : ", response.data.data);
                    this.sales = response.data.data;
                    setTimeout(() => {
                        // Complete the animation of the  progress bar.
                        NProgress.done();
                        this.$bvModal.show("Show_sales");
                    }, 500);
                })
                .catch(() => {
                    // Complete the animation of the  progress bar.
                    setTimeout(() => NProgress.done(), 500);
                });
        },
    },

    //----------------------------- Created function-------------------\\
    created: function () {
        this.Payments_Sales(1);
    },
};
</script>
