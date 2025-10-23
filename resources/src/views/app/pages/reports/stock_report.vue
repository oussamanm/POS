<template>
    <div class="main-content">
        <breadcumb :page="$t('stock_report')" :folder="$t('Reports')" />

        <div
            v-if="isLoading"
            class="loading_page spinner spinner-primary mr-3"
        ></div>

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
                    enabled: false,
                    mode: 'records',
                    nextLabel: 'next',
                    prevLabel: 'prev',
                }"
                styleClass="tableOne table-hover vgt-table mt-3"
            >
                <div
                    slot="table-actions"
                    class="mt-2 mb-3 quantity_alert_warehouse"
                >
                    <!-- warehouse -->
                    <b-form-group :label="$t('warehouse')">
                        <v-select
                            @input="Selected_Warehouse"
                            v-model="warehouse_id"
                            :reduce="(label) => label.value"
                            :placeholder="$t('Choose_Warehouse')"
                            :options="
                                warehouses.map((warehouses) => ({
                                    label: warehouses.name,
                                    value: warehouses.id,
                                }))
                            "
                        />
                    </b-form-group>
                </div>

                <div slot="table-actions" class="mt-2 mb-3">
                    <b-button
                        @click="stock_report_PDF()"
                        size="sm"
                        variant="outline-success ripple m-1"
                    >
                        <i class="i-File-Copy"></i> PDF
                    </b-button>
                    <vue-excel-xlsx
                        class="btn btn-sm btn-outline-danger ripple m-1"
                        :data="reports"
                        :columns="columns"
                        :file-name="'stock_report'"
                        :file-type="'xlsx'"
                        :sheet-name="'stock_report'"
                    >
                        <i class="i-File-Excel"></i> EXCEL
                    </vue-excel-xlsx>
                </div>

                <template slot="table-row" slot-scope="props">
                    <span v-if="props.column.field == 'actions'">
                        <router-link
                            title="Report"
                            :to="'/app/reports/detail_stock/' + props.row.id"
                        >
                            <b-button variant="primary">{{
                                $t("Reports")
                            }}</b-button>
                        </router-link>
                    </span>
                </template>
            </vue-good-table>
        </b-card>
    </div>
</template>

<script>
import NProgress from "nprogress";
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
    metaInfo: {
        title: "Stock Report",
    },
    data() {
        return {
            isLoading: true,
            serverParams: {
                sort: {
                    field: "code",
                    type: "desc",
                },
                page: 1,
                perPage: 10,
            },
            limit: "10",
            search: "",
            totalRows: "",
            reports: [],
            report: {},
            warehouses: [],
            rows: [
                {
                    statut: "Total",
                    children: [],
                },
            ],
            warehouse_id: "",
        };
    },

    computed: {
        columns() {
            return [
                {
                    label: this.$t("ProductCode"),
                    field: "code",
                    tdClass: "text-left",
                    thClass: "text-left",
                },
                {
                    label: this.$t("Name_product"),
                    field: "name",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false,
                },
                {
                    label: this.$t("Categorie"),
                    field: "category",
                    tdClass: "text-left",
                    thClass: "text-left",
                    sortable: false,
                },
                {
                    label: this.$t("Current_stock"),
                    field: "quantity",
                    tdClass: "text-right",
                    thClass: "text-right",
                    sortable: false,
                },
                {
                    label: "Stock Valeur en DH (Approximatif)",
                    field: "stock_value",
                    tdClass: "text-right",
                    thClass: "text-right",
                    sortable: false,
                    headerField: this.sumCount,
                },

                {
                    label: this.$t("Action"),
                    field: "actions",
                    html: true,
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
                console.log("stock_value: ",rowObj.children[i].stock_value);
                sum += rowObj.children[i].stock_value;
            }

            return sum.toFixed(2);
        },

        //----------------------------------- Sales PDF ------------------------------\\
        stock_report_PDF() {
            var self = this;

            let pdf = new jsPDF("p", "pt");

            let columns = [
                { title: "Code", dataKey: "code" },
                { title: "Produit", dataKey: "name" },
                { title: "Quantity", dataKey: "quantity" },
                { title: "Stock Valeur", dataKey: "stock_value" },
            ];

            let selected_depot = "";
            let date_now = new Date().toLocaleDateString();

            if (this.warehouse_id != "") {
                const tmp = parseInt(this.warehouse_id);
                const tmp_warehouse = this.warehouses.find(
                    (item) => item.id == tmp
                );

                selected_depot = tmp_warehouse.name;
            } else selected_depot = "Tous les Entrepôts";

            if (Array.isArray(self.reports) && self.reports.length > 0) {
                // Add event listener for the `didDrawPage` event
                pdf.autoTable({
                    head: [columns],
                    body: self.reports.map((item) => [
                        item.code,
                        item.name,
                        item.quantity,
                        item.stock_value,
                    ]), // Map data to array format expected by autoTable
                    startY: 110,
                    didDrawPage: function (data) {
                        // Add header information
                        if (data.pageNumber === 1) {
                            pdf.text("Stock report", 40, 25);
                            pdf.setFontSize(12);
                            pdf.text("Magasin : " + selected_depot, 40, 45);
                            pdf.text("Date: " + date_now, 40, 65);
                        }

                        // Add page number to the footer
                        pdf.setFontSize(10);
                        const pageHeight = pdf.internal.pageSize.getHeight();
                        const footerPosition = pageHeight - 20; // Adjust this value to move the footer up or down
                        pdf.text(
                            `Page ${data.pageNumber}`,
                            data.settings.margin.left,
                            footerPosition
                        );
                    },
                });
            }

            //  pdf.autoTable(columns, self.reports);
            pdf.save("Stock_report.pdf");
        },

        //---- update Params Table
        updateParams(newProps) {
            this.serverParams = Object.assign({}, this.serverParams, newProps);
        },

        //---- Event Page Change
        onPageChange({ currentPage }) {
            if (this.serverParams.page !== currentPage) {
                this.updateParams({ page: currentPage });
                this.Get_Stock_Report(currentPage);
            }
        },

        //---- Event Per Page Change
        onPerPageChange({ currentPerPage }) {
            if (this.limit !== currentPerPage) {
                this.limit = currentPerPage;
                this.updateParams({ page: 1, perPage: currentPerPage });
                this.Get_Stock_Report(1);
            }
        },

        //---- Event on Sort Change
        onSortChange(params) {
            this.updateParams({
                sort: {
                    type: params[0].type,
                    field: params[0].field,
                },
            });
            this.Get_Stock_Report(this.serverParams.page);
        },

        //---- Event on Search

        onSearch(value) {
            this.search = value.searchTerm;
            this.Get_Stock_Report(this.serverParams.page);
        },

        //------------------------------Formetted Numbers -------------------------\\
        formatNumber(number, dec) {
            const value = (
                typeof number === "string" ? number : number.toString()
            ).split(".");
            if (dec <= 0) return value[0];
            let formated = value[1] || "";
            if (formated.length > dec)
                return `${value[0]}.${formated.substr(0, dec)}`;
            while (formated.length < dec) formated += "0";
            return `${value[0]}.${formated}`;
        },

        //---------------------- Event Select Warehouse ------------------------------\\
        Selected_Warehouse(value) {
            if (value === null) {
                this.warehouse_id = "";
            }
            this.Get_Stock_Report(1);
        },

        //--------------------------- Get Customer Report -------------\\

        Get_Stock_Report(page) {
            // Start the progress bar.
            NProgress.start();
            NProgress.set(0.1);
            axios
                .get(
                    "report/stock?vendor=1&page=" +
                        page +
                        "&SortField=" +
                        this.serverParams.sort.field +
                        "&SortType=" +
                        this.serverParams.sort.type +
                        "&warehouse_id=" +
                        this.warehouse_id +
                        "&search=" +
                        this.search +
                        "&limit=-1"
                )
                .then((response) => {
                    this.reports = response.data.report;
                    this.totalRows = response.data.totalRows;
                    this.warehouses = response.data.warehouses;

                    this.rows[0].children = this.reports;

                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    this.isLoading = false;
                })
                .catch((response) => {
                    // Complete the animation of theprogress bar.
                    NProgress.done();
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 500);
                });
        },
    }, //end Methods

    //----------------------------- Created function------------------- \\

    created: function () {
        this.Get_Stock_Report(1);
    },
};
</script>
