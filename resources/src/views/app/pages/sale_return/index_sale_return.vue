<template>
  <div class="main-content">
    <breadcumb :page="$t('SalesReturn')" :folder="$t('ListReturns')"/>

    <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>
    <div v-else>
      <vue-good-table
        mode="remote"
        :columns="columns"
        :totalRows="totalRows"
        :rows="sales_return"
        @on-page-change="onPageChange"
        @on-per-page-change="onPerPageChange"
        @on-sort-change="onSortChange"
        @on-search="onSearch"
        :search-options="{
        placeholder: $t('Search_this_table'),
        enabled: true,
      }"
        :select-options="{
          enabled: true ,
          clearSelectionText: '',
        }"
        @on-selected-rows-change="selectionChanged"
        :pagination-options="{
        enabled: true,
        mode: 'records',
        nextLabel: 'next',
        prevLabel: 'prev',
      }"
        :styleClass="showDropdown?'tableOne table-hover vgt-table full-height':'tableOne table-hover vgt-table non-height'"
      >
        <div slot="selected-row-actions">
          <button class="btn btn-danger btn-sm" @click="delete_by_selected()">{{$t('Del')}}</button>
        </div>
        <div slot="table-actions" class="mt-2 mb-3">
          <b-button variant="outline-info ripple m-1" size="sm" v-b-toggle.sidebar-right>
            <i class="i-Filter-2"></i>
            {{ $t("Filter") }}
          </b-button>
          <b-button @click="Sale_Return_PDF()" size="sm" variant="outline-success ripple m-1">
            <i class="i-File-Copy"></i> PDF
          </b-button>
          <vue-excel-xlsx
              class="btn btn-sm btn-outline-danger ripple m-1"
              :data="sales_return"
              :columns="columns"
              :file-name="'sales_return'"
              :file-type="'xlsx'"
              :sheet-name="'sales_return'"
              >
              <i class="i-File-Excel"></i> EXCEL
          </vue-excel-xlsx>

        </div>

        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'actions'">
            <div>
              <b-dropdown
                id="dropdown-left"
                variant="link"
                text="Left align"
                toggle-class="text-decoration-none"
                size="lg"
                no-caret
              >
                <template v-slot:button-content class="_r_btn border-0">
                  <span class="_dot _r_block-dot bg-dark"></span>
                  <span class="_dot _r_block-dot bg-dark"></span>
                  <span class="_dot _r_block-dot bg-dark"></span>
                </template>
                <b-navbar-nav>
                  <b-dropdown-item title="Show" :to="'/app/sale_return/detail/'+props.row.id">
                    <i class="nav-icon i-Eye font-weight-bold mr-2"></i>
                    {{$t('ReturnDetail')}}
                  </b-dropdown-item>
                </b-navbar-nav>

                <!-- <b-dropdown-item
                  title="Edit"
                  v-if="props.row.sale_id != null && currentUserPermissions.includes('Sale_Returns_edit')"
                  :to="'/app/sale_return/edit/'+props.row.id+'/'+props.row.sale_id"
                >
                  <i class="nav-icon i-Pen-2 font-weight-bold mr-2"></i>
                  {{$t('EditReturn')}}
                </b-dropdown-item> -->

                <!-- <b-dropdown-item
                  title="Edit"
                  v-if="props.row.sale_id == null && currentUserPermissions.includes('Sale_Returns_edit')"
                  :to="'/app/sale_return/edit/'+props.row.id"
                >
                  <i class="nav-icon i-Pen-2 font-weight-bold mr-2"></i>
                  {{$t('EditReturn')}}
                </b-dropdown-item> -->

                <b-dropdown-item title="PDF" @click="Return_PDF(props.row , props.row.id)">
                  <i class="nav-icon i-File-TXT font-weight-bold mr-2"></i>
                  {{$t('DownloadPdf')}}
                </b-dropdown-item>

                <b-dropdown-item
                  title="Delete"
                  v-if="currentUserPermissions.includes('Sale_Returns_delete')"
                  @click="Remove_Return(props.row.id)"
                >
                  <i class="nav-icon i-Close-Window font-weight-bold mr-2"></i>
                  {{$t('DeleteReturn')}}
                </b-dropdown-item>
              </b-dropdown>
            </div>
          </span>
          <div v-else-if="props.column.field == 'statut'">
            <span
              v-if="props.row.statut == 'received'"
              class="badge badge-outline-success"
            >{{$t('Received')}}</span>
            <span v-else class="badge badge-outline-info">{{$t('Pending')}}</span>
          </div>
           <div v-else-if="props.column.field == 'Ref'">
            <router-link
              :to="'/app/sale_return/detail/'+props.row.id"
            >
              <span class="ul-btn__text ml-1">{{props.row.Ref}}</span>
            </router-link>
          </div>
          <div v-else-if="props.column.field == 'sale_ref' && props.row.sale_id">
            <router-link
              :to="'/app/sales/detail/'+props.row.sale_id"
            >
              <span class="ul-btn__text ml-1">{{props.row.sale_ref}}</span>
            </router-link>
          </div>
        </template>
      </vue-good-table>
    </div>

    <!-- Sidebar Filter -->
    <b-sidebar id="sidebar-right" :title="$t('Filter')" bg-variant="white" right shadow>
      <div class="px-3 py-2">
        <b-row>
          <!-- date  -->
          <b-col md="12">
            <b-form-group :label="$t('date')">
              <b-form-input type="date" v-model="Filter_date"></b-form-input>
            </b-form-group>
          </b-col>

          <!-- Reference -->
          <b-col md="12">
            <b-form-group :label="$t('Reference')">
              <b-form-input label="Reference" :placeholder="$t('Reference')" v-model="Filter_Ref"></b-form-input>
            </b-form-group>
          </b-col>

          <!-- sale  -->
          <b-col md="12">
            <b-form-group :label="$t('Sale')">
              <v-select
                :reduce="label => label.value"
                :placeholder="$t('Choose_Sale_Ref')"
                v-model="Filter_sale"
                :options="sales.map(sales => ({label: sales.Ref, value: sales.id}))"
              />
            </b-form-group>
          </b-col>

          <!-- Customer  -->
          <b-col md="12">
            <b-form-group :label="$t('Customer')">
              <v-select
                :reduce="label => label.value"
                :placeholder="$t('Choose_Customer')"
                v-model="Filter_Client"
                :options="customers.map(customers => ({label: customers.name, value: customers.id}))"
              />
            </b-form-group>
          </b-col>

          <!-- warehouse -->
          <b-col md="12" class="">
            <b-form-group :label="$t('warehouse')">
              <v-select
                v-model="Filter_warehouse"
                :reduce="label => label.value"
                :placeholder="$t('Choose_Warehouse')"
                :options="warehouses.map(warehouses => ({label: warehouses.name, value: warehouses.id}))"
              />
            </b-form-group>
          </b-col>

          <!-- Status  -->
          <b-col md="12">
            <b-form-group :label="$t('Status')">
              <v-select
                v-model="Filter_status"
                :reduce="label => label.value"
                :placeholder="$t('Choose_Status')"
                :options="
                      [
                        {label: $t('Completed'), value: 'completed'},
                        {label: $t('Pending'), value: 'pending'},
                        {label: $t('Ordered'), value: 'ordered'},
                      ]"
              ></v-select>
            </b-form-group>
          </b-col>

          <b-col md="6" sm="12">
            <b-button
              @click="GET_Sales_Return(serverParams.page)"
              variant="primary ripple m-1"
              size="sm"
            >
              <i class="i-Filter-2"></i>
              {{ $t("Filter") }}
            </b-button>
          </b-col>
          <b-col md="6" sm="12">
            <b-button @click="Reset_Filter()" variant="danger ripple m-1" size="sm">
              <i class="i-Power-2"></i>
              {{ $t("Reset") }}
            </b-button>
          </b-col>
        </b-row>
      </div>
    </b-sidebar>
  </div>

</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";
import jsPDF from "jspdf";
import "jspdf-autotable";

export default {
  metaInfo: {
    title: "Sale Return"
  },

  data() {
    return {
      paymentProcessing: false,
      isLoading: true,
      serverParams: {
        sort: {
          field: "id",
          type: "desc"
        },
        page: 1,
        perPage: 10
      },
      selectedIds: [],
      search: "",
      totalRows: "",
      submitStatus: null,
      showDropdown: false,
      EditPaiementMode: false,
      Filter_Client: "",
      Filter_sale:"",
      Filter_status: "",
      Filter_Ref: "",
      Filter_date: "",
      Filter_warehouse: "",
      due:0,
      return_sale_due:'',
      sales_return: [],
      sale_return: {},
      customers: [],
      sales:[],
      warehouses: [],
      sale_return_id: "",
      factures: [],
      limit: "10",
      facture: {
        id: "",
        sale_return_id: "",
        date: "",
        Ref: "",
        montant: "",
        received_amount: "",
        Reglement: "",
        notes: ""
      },

    };
  },

   mounted() {
    this.$root.$on("bv::dropdown::show", bvEvent => {
      this.showDropdown = true;
    });
    this.$root.$on("bv::dropdown::hide", bvEvent => {
      this.showDropdown = false;
    });
  },

  computed: {
    ...mapGetters(["currentUserPermissions", "currentUser"]),
    columns() {
      return [
        {
          label: this.$t("date"),
          field: "date",
          tdClass: "text-left",
          thClass: "text-left"
        },
        {
          label: this.$t("Reference"),
          field: "Ref",
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
          label: this.$t("Sale_Ref"),
          field: "sale_ref",
          tdClass: "text-left",
          thClass: "text-left"
        },
        {
          label: this.$t("Status"),
          field: "statut",
          html: true,
          tdClass: "text-left",
          thClass: "text-left"
        },
        {
          label: this.$t("Total"),
          field: "GrandTotal",
          tdClass: "text-left",
          thClass: "text-left"
        },
        {
          label: this.$t("Action"),
          field: "actions",
          html: true,
          tdClass: "text-right",
          thClass: "text-right",
          sortable: false
        }
      ];
    }
  },

  methods: {


    //---- update Params Table
    updateParams(newProps) {
      this.serverParams = Object.assign({}, this.serverParams, newProps);
    },

    //---- Event Page Change
    onPageChange({ currentPage }) {
      if (this.serverParams.page !== currentPage) {
        this.updateParams({ page: currentPage });
        this.GET_Sales_Return(currentPage);
      }
    },

    //---- Event Per Page Change
    onPerPageChange({ currentPerPage }) {
      if (this.limit !== currentPerPage) {
        this.limit = currentPerPage;
        this.updateParams({ page: 1, perPage: currentPerPage });
        this.GET_Sales_Return(1);
      }
    },

    //---- Event Select Rows
    selectionChanged({ selectedRows }) {
      this.selectedIds = [];
      selectedRows.forEach((row, index) => {
        this.selectedIds.push(row.id);
      });
    },

    //---- Event Sort change
    onSortChange(params) {
      let field = "";
      if (params[0].field == "client_name") {
        field = "client_id";
      } else if (params[0].field == "warehouse_name") {
        field = "warehouse_id";
      } else if (params[0].field == "sale_ref") {
        field = "sale_id";
      } else {
        field = params[0].field;
      }
      this.updateParams({
        sort: {
          type: params[0].type,
          field: field
        }
      });
      this.GET_Sales_Return(this.serverParams.page);
    },

    //---- Event Search
    onSearch(value) {
      this.search = value.searchTerm;
      this.GET_Sales_Return(this.serverParams.page);
    },


    //---------- keyup paid Amount

    Verified_paidAmount() {
      if (isNaN(this.facture.montant)) {
        this.facture.montant = 0;
      } else if (this.facture.montant > this.facture.received_amount) {
        this.makeToast(
          "warning",
          this.$t("Paying_amount_is_greater_than_Received_amount"),
          this.$t("Warning")
        );
        this.facture.montant = 0;
      }
      else if (this.facture.montant > this.due) {
        this.makeToast(
          "warning",
          this.$t("Paying_amount_is_greater_than_Grand_Total"),
          this.$t("Warning")
        );
        this.facture.montant = 0;
      }
    },

    //---------- keyup Received Amount

    Verified_Received_Amount() {
      if (isNaN(this.facture.received_amount)) {
        this.facture.received_amount = 0;
      }
    },

    //---Validate State Fields
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },

    //------ Toast
    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    },

    //------ Reset Filter
    Reset_Filter() {
      this.search = "";
      this.Filter_Client = "";
      this.Filter_sale = "";
      this.Filter_status = "";
      this.Filter_Ref = "";
      this.Filter_date = "";
      this.Filter_warehouse = "1";
      this.GET_Sales_Return(this.serverParams.page);
    },

    //---------------------------------------- Set To Strings-------------------------\\
    setToStrings() {
      // Simply replaces null values with strings=''s
      if (this.Filter_Client === null) {
        this.Filter_Client = "";
      } else if (this.Filter_warehouse === null) {
        this.Filter_warehouse = "1";
      } else if (this.Filter_status === null) {
        this.Filter_status = "";
      } else if (this.Filter_sale === null) {
        this.Filter_sale = "";
      }
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

    //----------------------------- Invoice Return PDF------------------------------\\
    Return_PDF(sale_return, id) {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);

       axios
        .get("return_sale_pdf/" + id, {
          responseType: "blob", // important
          headers: {
            "Content-Type": "application/json"
          }
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute(
            "download",
            "Return-Sale-" + sale_return.Ref + ".pdf"
          );
          document.body.appendChild(link);
          link.click();
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
        })
        .catch(() => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
        });
    },

    //------------------------ Payment Sale Return PDF ------------------------------\\
    Payment_Return_PDF(facture, id) {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);

       axios
        .get("payment_return_sale_pdf/" + id, {
          responseType: "blob", // important
          headers: {
            "Content-Type": "application/json"
          }
        })
        .then(response => {
          const url = window.URL.createObjectURL(new Blob([response.data]));
          const link = document.createElement("a");
          link.href = url;
          link.setAttribute("download", "Payment-" + facture.Ref + ".pdf");
          document.body.appendChild(link);
          link.click();
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
        })
        .catch(() => {
          // Complete the animation of the  progress bar.
          setTimeout(() => NProgress.done(), 500);
        });
    },

    //----------------------------------------- Sales Return PDF -----------------------\\
    Sale_Return_PDF() {
      var self = this;

      let pdf = new jsPDF("p", "pt");
      let columns = [
        { title: this.$t("Reference"), dataKey: "Ref" },
        { title: this.$t("Customer"), dataKey: "client_name" },
        { title: this.$t("Sale_Ref"), dataKey: "sale_ref" },
        { title: this.$t("Status"), dataKey: "statut" },
        { title: this.$t("Total"), dataKey: "GrandTotal" },
      ];

      pdf.autoTable(columns, self.sales_return);
      pdf.text("Liste des retours de ventes", 40, 25);
      pdf.save("Sales Return.pdf");
    },

    Number_Order_Payment() {
      axios
        .get("payment/returns_sale/Number/order")
        .then(({ data }) => (this.facture.Ref = data));
    },


    //--------------------- Get All Returns ------------------------\\
    GET_Sales_Return(page) {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
      this.setToStrings();
      axios
        .get(
          "returns/sale?page=" +
            page +
            "&Ref=" +
            this.Filter_Ref +
            "&date=" +
            this.Filter_date +
            "&sale_id=" +
            this.Filter_sale +
            "&client_id=" +
            this.Filter_Client +
            "&statut=" +
            this.Filter_status +
            "&warehouse_id=" +
            this.Filter_warehouse +
            "&SortField=" +
            this.serverParams.sort.field +
            "&SortType=" +
            this.serverParams.sort.type +
            "&search=" +
            this.search +
            "&limit=" +
            this.limit
        )
        .then(response => {
          this.sales_return = response.data.sale_Return;
          this.customers = response.data.customers;
          this.sales = response.data.sales;
          this.warehouses = response.data.warehouses;
          this.totalRows = response.data.totalRows;

          // Complete the animation of theprogress bar.
          NProgress.done();
          this.isLoading = false;
        })
        .catch(response => {
          // Complete the animation of theprogress bar.
          NProgress.done();
          setTimeout(() => {
            this.isLoading = false;
          }, 500);
        });
    },

    //---------------------  Remove Sale Return ------------------------\\
    Remove_Return(id) {
      this.$swal({
        title: this.$t("Delete.Title"),
        text: this.$t("Delete.Text"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: this.$t("Delete.cancelButtonText"),
        confirmButtonText: this.$t("Delete.confirmButtonText")
      }).then(result => {
        if (result.value) {
          // Start the progress bar.
          NProgress.start();
          NProgress.set(0.1);
          axios
            .delete("returns/sale/" + id)
            .then(() => {
              this.$swal(
                this.$t("Delete.Deleted"),
                this.$t("Delete.ReturnDeleted"),
                "success"
              );
              Fire.$emit("Delete_Return_sale");
            })
            .catch(() => {
              // Complete the animation of the  progress bar.
              setTimeout(() => NProgress.done(), 500);
              this.$swal(
                this.$t("Delete.Failed"),
                this.$t("Delete.Therewassomethingwronge"),
                "warning"
              );
            });
        }
      });
    },

    //---- Delete Sale Return by selection

    delete_by_selected() {
      this.$swal({
        title: this.$t("Delete.Title"),
        text: this.$t("Delete.Text"),
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        cancelButtonText: this.$t("Delete.cancelButtonText"),
        confirmButtonText: this.$t("Delete.confirmButtonText")
      }).then(result => {
        if (result.value) {
          // Start the progress bar.
          NProgress.start();
          NProgress.set(0.1);
          axios
            .post("returns/sale/delete/by_selection", {
              selectedIds: this.selectedIds
            })
            .then(() => {
              this.$swal(
                this.$t("Delete.Deleted"),
                this.$t("Delete.ReturnDeleted"),
                "success"
              );

              Fire.$emit("Delete_Return_sale");
            })
            .catch(() => {
              // Complete the animation of theprogress bar.
              setTimeout(() => NProgress.done(), 500);
              this.$swal(
                this.$t("Delete.Failed"),
                this.$t("Delete.Therewassomethingwronge"),
                "warning"
              );
            });
        }
      });
    },

  }, //End Methods

  //---------------------------------- Created Function -----------------------------\\
  created() {
    this.GET_Sales_Return(1);

    Fire.$on("Delete_Return_sale", () => {
      setTimeout(() => {
        this.GET_Sales_Return(this.serverParams.page);
        // Complete the animation of the  progress bar.
        NProgress.done();
      }, 800);
    });
  }
};
</script>
