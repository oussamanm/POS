<template>
    <div class="main-content">
      <breadcumb page="Inventaire" :folder="$t('ListInventries')"/>
      <div v-if="isLoading" class="loading_page spinner spinner-primary mr-3"></div>

      <validation-observer ref="Edit_inventory" v-if="!isLoading">
        <b-form @submit.prevent="Submit__inventory">
          <b-row>
            <b-col lg="12" md="12" sm="12">
              <b-card>
                <b-row>

                  <!-- WareHouse  -->
                  <b-col md="12" class="mb-2 col-md-12" >
                      <div style="display:flex;" class="flex-1 flex-row gap-2">
                          <p class="mr-2">Warehouse :</p>
                          <p style="font-weight: 700" class="font-semibold">{{inventory?.warehouse?.name}}</p>
                      </div>
                  </b-col>

                  <!-- Applied  -->
                  <b-col md="12" class="col-md-12" >
                      <div class="d-flex flex-row gap-2">
                          <p class="mr-2">Stock Applique :</p>
                          <div>
                            <span :class="{'badge badge-outline-success font-semibold': inventory?.applied, 'badge badge-outline-warning font-semibold': !inventory?.applied}">
                                {{inventory?.applied ? 'Oui' : 'Non'}}
                            </span>
                          </div>
                      </div>
                  </b-col>

                  <!-- date  -->
                  <b-col md="12" class="mb-2 mt-2">
                    <validation-provider
                      name="date"
                      :rules="{ required: true}"
                      v-slot="validationContext"
                    >
                      <b-form-group :label="$t('date') + ' ' + '*'">
                        <b-form-input
                          :state="getValidationState(validationContext)"
                          aria-describedby="date-feedback"
                          type="date"
                          v-model="inventory.date"
                        ></b-form-input>
                        <b-form-invalid-feedback
                          id="OrderTax-feedback"
                        >{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                      </b-form-group>
                    </validation-provider>
                  </b-col>

                   <!-- Product -->
                  <b-col md="12" class="mb-2">
                    <h6>{{$t('ProductName')}}</h6>
                  </b-col>

                  <!-- Details product  -->
                  <b-col md="12">
                    <div class="table-responsive">
                      <table class="table table-hover">
                        <thead class="bg-gray-300">
                          <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{$t('CodeProduct')}}</th>
                            <th scope="col">{{$t('ProductName')}}</th>
                            <th scope="col">{{$t('CurrentStock')}}</th>
                            <th scope="col">{{$t('NewStock')}}</th>
                            <th scope="col" class="text-center">
                              <i class="fa fa-trash"></i>
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr v-if="details.length <=0">
                            <td colspan="6">{{$t('NodataAvailable')}}</td>
                          </tr>
                          <tr
                            v-for="detail in details"
                            :class="{'row_deleted': detail.del === 1}"
                            :key="detail.id"
                          >
                            <td>{{detail?.product_id}}</td>
                            <td>{{detail?.product_code}}</td>
                            <td>({{detail?.product_name}})</td>
                            <td>
                              <span
                                class="badge badge-outline-warning"
                              >{{detail?.old_stock}}</span>
                            </td>
                            <td>
                              <div class="quantity">
                                <b-input-group>
                                  <input
                                    class="form-control"
                                    @keyup="Verified_Qty(detail.new_stock)"
                                    v-model.number="detail.new_stock"
                                    :disabled="detail.del === 1"
                                  >
                                </b-input-group>
                              </div>
                            </td>

                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </b-col>

                  <b-col md="12">
                    <b-form-group>
                      <b-button variant="primary" :disabled="SubmitProcessing" @click="Submit__inventory"><i class="i-Yes me-2 font-weight-bold"></i> {{$t('submit')}}</b-button>
                      <div v-once class="typo__p" v-if="SubmitProcessing">
                          <div class="spinner sm spinner-primary mt-3"></div>
                        </div>
                    </b-form-group>
                  </b-col>
                </b-row>
              </b-card>
            </b-col>
          </b-row>
        </b-form>
      </validation-observer>
    </div>
  </template>

  <script>
  import NProgress from "nprogress";

  export default {
    metaInfo: {
      title: "Edit Inventory"
    },
    data() {
      return {
        focused: false,
        timer:null,
        search_input:'',
        product_filter:[],
        isLoading: true,
        SubmitProcessing:false,
        products: [],
        details: [],
        inventory: {
          id: "",
          user_id: "",
          warehouse_id: "",
          applied: "",
          file_stock: "",
          date: ""
        },
        product: {
          id: "",
          product_id: "",
          product_code: "",
          product_name: "",

          old_stock: "",
          new_stock: "",

          del: "",
          unit: ""
        }
      };
    },

    methods: {

       handleFocus() {
        this.focused = true
      },

      handleBlur() {
        this.focused = false
      },


      //------------- Submit Validation Update Adjustment
      Submit__inventory() {
        this.$refs.Edit_inventory.validate().then(success => {
          if (!success) {
            this.makeToast(
              "danger",
              this.$t("Please_fill_the_form_correctly"),
              this.$t("Failed")
            );
          } else {
            this.Update_Inventory();
          }
        });
      },

      //------------- Event Get Validation state
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


      //-----------------------------------Verified QTY ------------------------------\\
      Verified_Qty(detail) {
          if (detail.new_stock < 0)
          {
              this.makeToast("warning", this.$t("LowStock"), this.$t("Warning"));
              this.details[i].new_stock = detail.new_stock;
          }
          this.$forceUpdate();
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


      //----------------------------------- Verified Quantity if Null Or zero ------------------------------\\
      verifiedForm(){
        if (this.details.length <= 0)
        {
          this.makeToast("warning",this.$t("AddProductToList"),this.$t("Warning"));

          return false;
        }
        else
        {
            for (var i = 0; i < this.details.length; i++)
            {
                if (this.details[i].new_stock == null || this.details[i].new_stock == "")
                    continue;
                if (this.details[i].new_stock <= 0)
                {
                    this.makeToast("warning", "Invalid Quantity in Produit :"+this.details[i].product_name, this.$t("Warning"));
                    return false;
                }
            }
            return true;
        }
      },

      //--------------------------------- Update inventory -------------------------\\
      Update_Inventory() {
        if (this.verifiedForm()) {
          this.SubmitProcessing = true;
          // Start the progress bar.
          NProgress.start();
          NProgress.set(0.1);
          let id = this.$route.params.id;
          axios
            .put(`inventory/${id}`, {
              details: this.details
            })
            .then(response => {
              // Complete the animation of theprogress bar.
              NProgress.done();
              this.SubmitProcessing = false;
              this.$router.push({
                name: "index_inventory"
              });
              this.makeToast(
                "success",
                this.$t("Successfully_Updated"),
                this.$t("Success")
              );
            })
            .catch(error => {
              // Complete the animation of theprogress bar.
              NProgress.done();
              this.makeToast("danger", this.$t("InvalidData"), this.$t("Failed"));
              this.SubmitProcessing = false;
            });
        }
      },


      //---------------------------------------Get Adjustment Elements------------------------------\\
      GetElements() {
        let id = this.$route.params.id;
        axios
          .get(`inventory/${id}/edit`)
          .then(response => {
            this.inventory = response.data.inventory;
            this.details = response.data.details;
            this.isLoading = false;
          })
          .catch(response => {
            setTimeout(() => {
              this.isLoading = false;
            }, 500);
          });
      }
    },

    //----------------------------- Created function-------------------
    created() {
      this.GetElements();
    }
  };
  </script>
