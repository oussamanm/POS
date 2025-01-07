<template>
  <div class="main-content">
    <breadcumb :page="$t('Achats Paiements')" :folder="$t('Achats Paiements')"/>

    <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>
    <b-card class="wrapper" v-if="!isLoading">
      <vue-good-table
        mode="remote"
        :columns="columns"
        :totalRows="totalRows"
        :rows="payments"
        @on-page-change="onPageChange"
        @on-per-page-change="onPerPageChange"
        @on-sort-change="onSortChange"
        @on-search="onSearch"
        :search-options="{
        enabled: false,
        placeholder: $t('Search_this_table'),  
      }"
        :select-options="{ 
          enabled: false ,
          clearSelectionText: '',
        }"
        @on-selected-rows-change="selectionChanged"
        :pagination-options="{
        enabled: true,
        mode: 'records',
        nextLabel: 'next',
        prevLabel: 'prev',
      }"
        styleClass="table-hover tableOne vgt-table"
      >
        <div slot="selected-row-actions">
          <button class="btn btn-danger btn-sm" @click="delete_by_selected()">{{$t('Del')}}</button>
        </div>
        <div slot="table-actions" class="mt-2 mb-3">
          <b-button
            @click="Pay_due()"
            class="btn-rounded"
            variant="btn btn-primary btn-icon m-1"
          >
            <i class="i-Add"></i>
            {{$t('Add')}}
          </b-button>
        </div>

        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'actions'">
            <a @click="Payment_Purchase_PDF(props.row, props.row.id)" title="Print" v-b-tooltip.hover>
              <i class="i-Billing text-25 text-info"></i>
            </a>
            <a @click="Edit_Payment(props.row)" title="Edit" v-b-tooltip.hover>
              <i class="i-Edit text-25 text-success"></i>
            </a>
            <a title="Delete" v-b-tooltip.hover @click="Remove_Payment(props.row.id)">
              <i class="i-Close-Window text-25 text-danger"></i>
            </a>
          </span>
        </template>
      </vue-good-table>
    </b-card>

    <!-- Modal Add Payment-->
    <validation-observer ref="Add_payment">
      <b-modal
        hide-footer
        size="lg"
        id="Add_Payment"
        :title="EditPaiementMode?$t('EditPayment'):$t('AddPayment')"
      >
        <b-form @submit.prevent="Submit_Payment">
          <b-row>
            <!-- date -->
            <b-col lg="4" md="12" sm="12">
              <validation-provider
                name="date"
                :rules="{ required: true}"
                v-slot="validationContext"
              >
                <b-form-group :label="$t('date')">
                  <b-form-input
                    label="date"
                    :state="getValidationState(validationContext)"
                    aria-describedby="date-feedback"
                    v-model="facture.date"
                    type="date"
                  ></b-form-input>
                  <b-form-invalid-feedback id="date-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

            <!-- Reference  -->
            <b-col lg="4" md="12" sm="12">
              <b-form-group :label="$t('Reference')">
                <b-form-input
                  disabled="disabled"
                  label="Reference"
                  :placeholder="$t('Reference')"
                  v-model="facture.Ref"
                ></b-form-input>
              </b-form-group>
            </b-col>

             <!-- Payment choice -->
            <b-col lg="4" md="12" sm="12">
              <validation-provider name="Payment choice" :rules="{ required: true}">
                <b-form-group slot-scope="{ valid, errors }" :label="$t('Paymentchoice')">
                  <v-select
                    :class="{'is-invalid': !!errors.length}"
                    :state="errors[0] ? false : (valid ? true : null)"
                    v-model="facture.Reglement"
                    @input="Selected_PaymentMethod"
                    :reduce="label => label.value"
                    :placeholder="$t('PleaseSelect')"
                    :options="
                          [
                              {label: $t('Cash'), value: 'Cash'},
                              {label: $t('Credit_card'), value: 'credit card'},
                              {label: $t('TPE'), value: 'tpe'},
                              {label: $t('Cheque'), value: 'cheque'},
                              {label: $t('Western Union'), value: 'Western Union'},
                              {label: $t('Bank_transfer'), value: 'bank transfer'},
                              {label: $t('Other'), value: 'other'},
                          ]"
                  ></v-select>
                  <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

             <!-- Received  Amount  -->
            <b-col lg="4" md="12" sm="12">
              <validation-provider
                name="Received Amount"
                :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                v-slot="validationContext"
              >
              <b-form-group :label="$t('Received_Amount')">
                <b-form-input
                  @keyup="Verified_Received_Amount(facture.received_amount)"
                  label="Received_Amount"
                  :placeholder="$t('Received_Amount')"
                  v-model.number="facture.received_amount"
                  :state="getValidationState(validationContext)"
                  aria-describedby="Received_Amount-feedback"
                ></b-form-input>
                <b-form-invalid-feedback
                  id="Received_Amount-feedback"
                >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
              </b-form-group>
            </validation-provider>
          </b-col>

            <!-- Paying Amount  -->
            <b-col lg="4" md="12" sm="12">
              <validation-provider
                name="Amount"
                :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                v-slot="validationContext"
              >
                <b-form-group :label="$t('Paying_Amount')">
                  <b-form-input
                   @keyup="Verified_paidAmount(facture.montant)"
                    label="Amount"
                    :placeholder="$t('Paying_Amount')"
                    v-model.number="facture.montant"
                    :state="getValidationState(validationContext)"
                    aria-describedby="Amount-feedback"
                  ></b-form-input>
                  <b-form-invalid-feedback id="Amount-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

            <!-- change Amount  -->
            <b-col lg="4" md="12" sm="12">
              <label>{{$t('Change')}} :</label>
              <p
                class="change_amount"
              >{{parseFloat(facture.received_amount - facture.montant).toFixed(2)}}</p>
            </b-col>
           
            <!-- Note -->
            <b-col lg="12" md="12" sm="12" class="mt-3">
              <b-form-group :label="$t('Note')">
                <b-form-textarea id="textarea" v-model="facture.notes" rows="3" max-rows="6"></b-form-textarea>
              </b-form-group>
            </b-col>
            <b-col md="12" class="mt-3">
              <b-button
                variant="primary"
                type="submit"
                :disabled="paymentProcessing"
              ><i class="i-Yes me-2 font-weight-bold"></i> {{$t('submit')}}</b-button>
              <div v-once class="typo__p" v-if="paymentProcessing">
                <div class="spinner sm spinner-primary mt-3"></div>
              </div>
            </b-col>
          </b-row>
        </b-form>
      </b-modal>
    </validation-observer>
    
    
    <!-- Modal Pay_due-->
    <validation-observer ref="ref_pay_due">
      <b-modal
        hide-footer
        size="md"
        id="modal_Pay_due"
        title="Nouveau paiement"
      >
        <b-form @submit.prevent="Submit_Payment_Purchase_due">
          <b-row>
          
            <!-- Providers  -->
            <b-col lg="12" md="12" sm="12" class="mb-3">
              <validation-provider name="Supplier" :rules="{ required: true}">
                <b-form-group slot-scope="{ valid, errors }" :label="$t('Supplier') + ' ' + '*'">
                  <v-select
                    :class="{'is-invalid': !!errors.length}"
                    :state="errors[0] ? false : (valid ? true : null)"
                    v-model="facture.provider_id"
                    @input="Selected_supplier"
                    :reduce="label => label.value"
                    :placeholder="$t('Choose_Supplier')"
                    :options="suppliers.map(suppliers => ({label: suppliers.name, value: suppliers.id}))"
                  />
                  <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>
            <!-- Paying Amount  -->
            <b-col lg="6" md="12" sm="12" v-if="facture.provider_id != ''">
              <validation-provider
                name="Amount"
                :rules="{ required: true , regex: /^\d*\.?\d*$/}"
                v-slot="validationContext"
              >
                <b-form-group :label="$t('Paying_Amount') + ' ' + '*'">
                  <b-form-input
                   @keyup="Verified_paidAmount2(facture.montant)"
                    label="Amount"
                    :placeholder="$t('Paying_Amount')"
                    v-model.number="facture.montant"
                    :state="getValidationState(validationContext)"
                    aria-describedby="Amount-feedback"
                  ></b-form-input>
                  <b-form-invalid-feedback id="Amount-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                  <span class="badge badge-danger">{{$t('Due')}} : {{currentUser.currency}} {{facture.due}}</span>
                </b-form-group>
              </validation-provider>
            </b-col>


            <!-- Payment choice -->
            <b-col lg="6" md="12" sm="12" v-if="facture.provider_id != ''">
              <validation-provider name="Payment choice" :rules="{ required: true}">
                <b-form-group slot-scope="{ valid, errors }" :label="$t('Paymentchoice')+ ' ' + '*'">
                  <v-select
                    :class="{'is-invalid': !!errors.length}"
                    :state="errors[0] ? false : (valid ? true : null)"
                    v-model="facture.Reglement"
                    :reduce="label => label.value"
                    :placeholder="$t('PleaseSelect')"
                    :options="
                          [
                          {label: $t('Cash'), value: 'Cash'},
                          {label: $t('Credit_card'), value: 'credit card'},
                          {label: 'TPE', value: 'tpe'},
                          {label: $t('Cheque'), value: 'cheque'},
                          {label: 'Western Union', value: 'Western Union'},
                          {label: $t('Bank_transfer'), value: 'bank transfer'},
                          {label: $t('Other'), value: 'other'},
                          ]"
                  ></v-select>
                  <b-form-invalid-feedback>{{ errors[0] }}</b-form-invalid-feedback>
                </b-form-group>
              </validation-provider>
            </b-col>

            <!-- Note -->
            <b-col lg="12" md="12" sm="12" class="mt-3" v-if="facture.provider_id != ''">
              <b-form-group :label="$t('Please_provide_any_details')">
                <b-form-textarea id="textarea" v-model="facture.notes" rows="3" max-rows="6"></b-form-textarea>
              </b-form-group>
            </b-col>

            <b-col md="12" class="mt-3">
              <b-button
                variant="primary"
                type="submit"
                :disabled="paymentProcessing"
              ><i class="i-Yes me-2 font-weight-bold"></i> {{$t('submit')}}</b-button>
              <div v-once class="typo__p" v-if="paymentProcessing">
                <div class="spinner sm spinner-primary mt-3"></div>
              </div>
            </b-col>

          </b-row>
        </b-form>
      </b-modal>
    </validation-observer>

  </div>
</template>

<script>
import { mapActions, mapGetters } from "vuex";
import NProgress from "nprogress";

export default {
  metaInfo: {
    title: "Achats Paiements"
  },
  data() {
    return {
      paymentProcessing: false,
      isLoading: true,
      SubmitProcessing:false,
      serverParams: {
        columnFilters: {},
        sort: {
          field: "id",
          type: "desc"
        },
        page: 1,
        perPage: 10
      },
      selectedIds: [],
      totalRows: "",
      search: "",
      limit: "10",
      payments: [],
      editmode: false,
      EditPaiementMode: false,
      suppliers:[],
      facture: {
        provider_id: "",
        montant: "",
        received_amount: "",
        Reglement: "",
        notes: "",
        due:''
      },
    };
  },

  computed: {
    ...mapGetters(["currentUserPermissions", "currentUser"]),
    columns() {
      return [
        {
          label: this.$t("Date"),
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
          label: this.$t("Amount"),
          field: "montant",
          tdClass: "text-left",
          thClass: "text-left"
        },
        {
          label: this.$t("PayeBy"),
          field: "Reglement",
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
        this.Get_Payments(currentPage);
      }
    },

    //---- Event Per Page Change
    onPerPageChange({ currentPerPage }) {
      if (this.limit !== currentPerPage) {
        this.limit = currentPerPage;
        this.updateParams({ page: 1, perPage: currentPerPage });
        this.Get_Payments(1);
      }
    },

    //---- Event Select Rows
    selectionChanged({ selectedRows }) {
      this.selectedIds = [];
      selectedRows.forEach((row, index) => {
        this.selectedIds.push(row.id);
      });
    },

    //---- Event Sort Change

    onSortChange(params) {
      this.updateParams({
        sort: {
          type: params[0].type,
          field: params[0].field
        }
      });
      this.Get_Payments(this.serverParams.page);
    },

    //---- Event Search
    onSearch(value) {
      this.search = value.searchTerm;
      this.Get_Payments(this.serverParams.page);
    },

    //---- Validation State Form
    getValidationState({ dirty, validated, valid = null }) {
      return dirty || validated ? valid : null;
    },

  //---------- keyup paid Amount
  //---------- keyup paid Amount
//------------------------------------ Remove Payment -------------------------------\\
    Remove_Payment(id) {
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
            .delete("payment_purchase/" + id)
            .then(() => {
              this.$swal(
                this.$t("Delete.Deleted"),
                this.$t("Delete.PaymentDeleted"),
                "success"
              );
              Fire.$emit("Delete_montant_purchase");
              this.$router.go(this.$router.currentRoute)
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

    Verified_Received_Amount() {
      if (isNaN(this.facture.received_amount)) {
        this.facture.received_amount = 0;
      } 
    },
    
    
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
    
    
    Verified_paidAmount2() {
      if (isNaN(this.facture.montant)) {
        this.facture.montant = 0;
      } else if (this.facture.montant > this.facture.due) {
        this.makeToast(
          "warning",
          this.$t("Paying_amount_is_greater_than_Total_Due"),
          this.$t("Warning")
        );
        this.facture.montant = 0;
      } 
    },
    //------------------------------------------- Update Payment -------------------------------\\
    Update_Payment() {
      this.paymentProcessing = true;
      NProgress.start();
      NProgress.set(0.1);
     
        axios
          .put("payment_purchase/" + this.facture.id, {
            purchase_id: this.facture.purchase_id,
            date: this.facture.date,
            montant: parseFloat(this.facture.montant).toFixed(2),
            received_amount: parseFloat(this.facture.received_amount).toFixed(2),
            Reglement: this.facture.Reglement,
            change: parseFloat(this.facture.received_amount - this.facture.montant).toFixed(2),
            notes: this.facture.notes
          })
          .then(response => {
            this.paymentProcessing = false;
            Fire.$emit("Update_Facture_purchase");
            this.makeToast(
              "success",
              this.$t("Update.TitlePayment"),
              this.$t("Success")
            );
            this.$router.go(this.$router.currentRoute)
          })
          .catch(error => {
            this.paymentProcessing = false;
            NProgress.done();
          });
    },

  
    //------ Validate Form Submit_Payment
    Submit_Payment() {
        this.$refs.Add_payment.validate().then(success => {
        if (!success) {
          return;
        } else if (this.facture.montant > this.facture.received_amount) {
          this.makeToast(
            "warning",
            this.$t("Paying_amount_is_greater_than_Received_amount"),
            this.$t("Warning")
          );
          this.facture.received_amount = 0;
        }
        else if (this.facture.montant > this.due) {
          this.makeToast(
            "warning",
            this.$t("Paying_amount_is_greater_than_Grand_Total"),
            this.$t("Warning")
          );
          this.facture.montant = 0;

        }else if (!this.EditPaiementMode) {
            this.Create_Payment();
        } else {
            this.Update_Payment();
        }

      });
    },

    //------ Toast
    makeToast(variant, msg, title) {
      this.$root.$bvToast.toast(msg, {
        title: title,
        variant: variant,
        solid: true
      });
    },


    reset_form_payment() {
      this.facture = {
        id: "",
        provider_id: "",
        purchase_id: "",
        date: "",
        Ref: "",
        montant: "",
        received_amount: "",
        Reglement: "",
        notes: "",
        due:''
      };
    },


    Edit_Payment(facture) {
      NProgress.start();
      NProgress.set(0.1);
      this.reset_form_payment();
      this.facture.id               = facture.id;
      this.facture.purchase_id      = facture.purchase_id
      this.facture.Ref              = facture.Ref;
      this.facture.Reglement        = facture.Reglement;
      this.facture.date             = facture.date;
      this.facture.change           = facture.change;
      this.facture.montant          = facture.montant;
      this.facture.received_amount  = parseFloat(facture.montant + facture.change).toFixed(2);
      this.facture.notes   = facture.notes;
      this.due = parseFloat(this.purchase_due) + facture.montant;
      this.EditPaiementMode = true;
      setTimeout(() => {
        // Complete the animation of the  progress bar.
        NProgress.done();
        this.$bvModal.show("Add_Payment");
      }, 500);
    },

    //--------------------------Get ALL warehouses ---------------------------\\

    Get_Payments(page) {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
      axios
        .get(
          "all_purchase_payments?page=" +
            page +
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
          this.payments = response.data.payments;
          this.totalRows = response.data.totalRows;
          this.suppliers = response.data.suppliers;
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

    Pay_due() {
        this.reset_form_payment();
        this.facture.provider_id = "";
        this.facture.provider_name = "";
        this.facture.montant = 0;
        this.facture.due = "";
        this.facture.date = new Date().toISOString().slice(0, 10);
        setTimeout(() => {
        this.$bvModal.show("modal_Pay_due");
        }, 500);
    },

    Selected_supplier(value){
        const tmp = (this.suppliers.find(user => user.id === value))
        this.facture.provider_name = tmp.name;
        this.facture.due = tmp.due;
    },
    
    Submit_Pay_due() {
      this.paymentProcessing = true;
      axios
        .post("pay_supplier_due", {
          provider_id: this.facture.provider_id,
          amount: this.facture.montant,
          notes: this.facture.notes,
          Reglement: this.facture.Reglement,
        })
        .then(response => {
          Fire.$emit("Event_pay_due");

          this.makeToast(
            "success",
            this.$t("Create.TitlePayment"),
            this.$t("Success")
          );
          this.paymentProcessing = false;
        })
        .catch(error => {
          this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
          this.paymentProcessing = false;
        });
    },
    
    Submit_Payment_Purchase_due() {
      this.$refs.ref_pay_due.validate().then(success => {
        if (!success) {
           this.makeToast(
            "danger",
            this.$t("Please_fill_the_form_correctly"),
            this.$t("Failed")
          );
        } else if (this.facture.montant > this.facture.due) {
          this.makeToast(
            "warning",
            this.$t("Paying_amount_is_greater_than_Total_Due"),
            this.$t("Warning")
          );
          this.facture.montant = 0;
        }
        else
        {
            this.Submit_Pay_due();
            this.$router.go(this.$router.currentRoute)
        }

      });
    },
    Payment_Purchase_PDF(facture, id) {
      // Start the progress bar.
      NProgress.start();
      NProgress.set(0.1);
     
       axios
        .get("payment_purchase_pdf/" + id, {
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
    
    
  },
    
  //----------------------------- Created function-------------------\\

  created: function() {
    this.Get_Payments(1);

    Fire.$on("Event_Warehouse", () => {
      setTimeout(() => {
        this.Get_Payments(this.serverParams.page);
        this.$bvModal.hide("New_Warehouse");
      }, 500);
    });

    Fire.$on("Delete_Warehouse", () => {
      setTimeout(() => {
        this.Get_Payments(this.serverParams.page);
      }, 500);
    });
  }
};
</script>